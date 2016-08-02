<?php
namespace squeal;

interface Dialect {
    /**
     * @param string $name
     * @return string
     */
    function name($name);
    /**
     * @param string|null $name Null means it's a positional parameter
     * @return string
     */
    function param($name = null);
}

interface Node {
    /** @return string */
    function toSql(Dialect $d);
}

interface Named {
    /** @return Node */
    function withName($name);
}

class Sqlite implements Dialect {
    function name($name) {
        return '"' . addcslashes($name, '"') . '"';
    }

    function param($name = null) {
        return $name ? ':' . $name : '?';
    }
}

function test() {
    error_log(Sql::generate(function ($db, $sql) {
        yield $db::FROM => ($u = $db->users);
        yield $db::SELECT => $u->id;
        yield $db::SELECT => $u->cols('email', 'name');
        yield $db::SELECT => [$u->activated, $u->role];
        yield $db::WHERE => $sql->eq($u->email, 'foo@example.com');
        yield $db::WHERE => $sql->eq($u->password, 'asdf');
    })->toSql(new Sqlite));
}

class Sql implements Node {
    private $flattened = [];

    function __construct(array $parts) {
        foreach (self::get($parts, Lang::SELECT, 'select') as $clause => $xs) {
            $this->flattened[] = $clause;
            $this->flattened[] = new Nodes($xs, ', ');
        }
        foreach (self::get($parts, Lang::FROM, "\nfrom") as $clause => $xs) {
            $this->flattened[] = $clause;
            $this->flattened[] = new Nodes($xs, ', ');
        }
        foreach (self::get($parts, Lang::WHERE, "\nwhere") as $clause => $xs) {
            $this->flattened[] = $clause;
            $this->flattened[] = new Nodes($xs, ' AND ');
        }
    }

    function toSql(Dialect $d) {
        $sql = [];
        foreach ($this->flattened as $node) {
            $sql[] = $node->toSql($d);
        }
        return implode(' ', $sql);
    }

    private static function get(array $parts, $key, $prefix) {
        if (array_key_exists($key, $parts)) {
            yield new Fragment($prefix) => (array) $parts[$key];
        }
    }

    static function generate(callable $gen) {
        $it = $gen(new Lang, new Ops);
        $parts = [];
        foreach ($it as $clause => $val) {
            if (!array_key_exists($clause, $parts) || $val === Lang::CLEAR) {
                $parts[$clause] = [];
            }
            if ($val !== Lang::CLEAR) {
                if (is_array($val)) {
                    foreach ($val as $v) {
                        $parts[$clause][] = $v;
                    }
                } else {
                    $parts[$clause][] = $val;
                }
            }
        }
        return new self($parts);
    }
}

class Lang {
    const SELECT = 'SELECT';
    const FROM = 'FROM';
    const WHERE = 'WHERE';
    const CLEAR = 'clear';

    function __invoke($sql) {
        return new Fragment($sql);
    }

    function __get($name) {
        return self::table($name);
    }

    static function table($name, $alias = null) {
        $t = new Table($name);
        return empty($alias) ? $t : new Alias($t, $alias);
    }

    static function param($name = null) {
        return new Param($name);
    }
}

class Nodes implements Node {
    private $nodes;
    private $glue;

    function __construct(array $nodes, $separator = ' ') {
        $this->nodes = $nodes;
        $this->glue = $separator;
    }

    function toSql(Dialect $d) {
        $sql = [];
        foreach ($this->nodes as $node) {
            $sql[] = $node->toSql($d);
        }
        return implode($this->glue, $sql);
    }
}

class Fragment implements Node {
    private $code;

    function __construct($code) {
        $this->code = $code;
    }

    function toSql(Dialect $d) {
        return $this->code;
    }
}

class Table implements Node, Named {
    private $name;

    function __construct($name) {
        $this->name = $name;
    }

    function __get($name) {
        return new Column($this, $name);
    }

    function withName($name) {
        return new Table($name);
    }

    function cols() {
        $cols = [];
        foreach (func_get_args() as $col) {
            $cols[] = new Column($this, $col);
        }
        return new Nodes($cols, ', ');
    }

    function toSql(Dialect $d) {
        return $d->name($this->name);
    }
}

class Column implements Node {
    private $table;
    private $name;

    function __construct(Table $t, $name) {
        $this->table = $t;
        $this->name = $name;
    }

    function toSql(Dialect $d) {
        return $this->table->toSql($d) . '.' . $d->name($this->name);
    }
}

class Alias implements Node {
    private $node;
    private $name;

    function __construct(Node $actual, $as) {
        $this->node = $actual;
        $this->name = $as;
    }

    function __get($name) {
        $node = $this->node instanceof Named ?
            $this->node->withName($this->name) :
            $this->node;
        return $node->$name;
    }

    function __call($method, array $args = []) {
        $node = $this->node instanceof Named ?
            $this->node->withName($this->name) :
            $this->node;
        return call_user_func_array([$node, $method], $args);
    }

    function toSql(Dialect $d) {
        return $this->node->toSql($d) . ' as ' . $d->name($this->name);
    }
}

class Param implements Node {
    private $name;
    private $value;

    function __construct($name = null, $value = null) {
        $this->name = $name;
        $this->value = $value;
    }

    function toSql(Dialect $d) {
        return $d->param($this->name);
    }
}

class Ops {
    function eq($left, $right) {
        $nodes = [];
        $nodes[] = $left instanceof Node ? $left : new Param(null, $left);
        $nodes[] = new Fragment('=');
        $nodes[] = $right instanceof Node ? $right : new Param(null, $right);
        return new Nodes($nodes, ' ');
    }
}
test();
