<?php

namespace Codeia\Data;

use PHPUnit\Framework\TestCase;
use Codeia\Data\Maps\Column;
use Codeia\Data\Maps\Query;

class MapsPrototypeTest extends TestCase implements Backend {

    use \Codeia\Typical\CanQueryIterables;

    private $users = [[
        'user_id' => 1, 'user_email' => 'a@b.c', 'user_name' => 'first'
    ], [
        'user_id' => 2, 'user_email' => 'd@e.f', 'user_name' => 'second'
    ], [
        'user_id' => 3, 'user_email' => 'g@h.i', 'user_name' => 'third'
    ], [
        'user_id' => 4, 'user_email' => 'j@k.l', 'user_name' => 'fourth'
    ], [
        'user_id' => 5, 'user_email' => 'm@n.o', 'user_name' => 'fifth'
    ]];

    private $roles = [[
        'role_id' => 1, 'role_user' => 1, 'role_role' => 'admin',
    ], [
        'role_id' => 2, 'role_user' => 1, 'role_role' => 'user',
    ], [
        'role_id' => 3, 'role_user' => 2, 'role_role' => 'user',
    ], [
        'role_id' => 4, 'role_user' => 3, 'role_role' => 'staff',
    ], [
        'role_id' => 5, 'role_user' => 4, 'role_role' => 'staff',
    ]];

    private $cols;

    function primarySource() {
        return $this->relation('users');
    }

    function relation($name) {
        switch (strtolower($name)) {
        case 'users':
            return $this->users;
        case 'roles':
            return $this->roles;
        default:
            return [];
        }
    }

    function attributes($name) {
        if (empty($this->cols)) {
            $this->cols = [
                'users' => [
                    'id' => new Column('id', 'user'),
                    'email' => new Column('email', 'user'),
                    'name' => new Column('name', 'user')
                ],
                'roles' => [
                    'id' => new Column('id', 'role'),
                    'user' => new Column('user', 'role'),
                    'role' => new Column('role', 'role')
                ]
            ];
        }
        return isset($this->cols[$name]) ? $this->cols[$name] : [];
    }

    function transact(callable $block) {
        $copy = ['users' => $this->users, 'roles' => $this->roles];
        if (false === $block($this)) {
            $this->users = $copy['users'];
            $this->roles = $copy['roles'];
        }
    }

    function fetch(array $clauses) {
        $q = new Query;
        foreach ($clauses as $c) {
            $c($q, $this);
        }
        return $q;
    }

    function exec(array $commands) {
    }


    function eq($left, $right) {
        return function ($row) use ($left, $right) {
            $l = $left instanceof Attribute ? $left->get($row) : $left;
            $r = $right instanceof Attribute ? $right->get($row) : $right;
            return $l == $r;
        };
    }

    function in($needle, $haystack) {
        return function ($row) use ($needle, $haystack) {
            $n = $needle instanceof Attribute ? $needle->get($row) : $needle;
            $h = $haystack instanceof Attribute ?
                $haystack->get($row) :
                $haystack;
            if (is_array($haystack)) {
                return in_array($n, $haystack);
            }
            if (is_string($haystack)) {
                return strpos((string) $n, $h) !== false;
            }
            foreach ($haystack as $val) {
                if ($val === $n) {
                    return true;
                }
            }
            return false;
        };
    }

    /** @test */
    function fetch_all() {
        $result = [];
        foreach ($this->fetch([$this->all()]) as $row) {
            $result[] = $row;
        }
        self::assertCount(5, $result);
    }

    /** @test */
    function fetch_one() {
        $result = [];
        foreach ($this->fetch([$this->one()]) as $row) {
            $result[] = $row;
        }
        self::assertCount(1, $result);
    }

    /** @test */
    function fetch_slice() {
        $q = $this->fetch([
            $this->page(1, 2)
        ]);
        $result = iterator_to_array($q);
        self::assertCount(2, $result);
        self::assertEquals(3, $result[0]['user_id']);
    }

    /** @test */
    function projection() {
        $row = current(iterator_to_array($this->fetch([
            $this->one(),
            $this->withProjection($this->attributes('users'))
        ])));
        self::assertEquals(1, $row['id']);
        self::assertEquals('a@b.c', $row['email']);
        self::assertEquals('first', $row['name']);
    }

    /** @test */
    function arbitrary_projection() {
        $q = $this->fetch([
            function (Query $q) {
                $q->from($this->users);
                $q->select(function () { return 'whatever'; });
            }
        ]);
        $count = 0;
        foreach ($q as $row) {
            self::assertEquals('whatever', $row);
            $count++;
        }
        self::assertEquals(5, $count);
    }

    /** @test */
    function column_select() {
        $q = $this->fetch([
            function (Query $q) {
                $q->from($this->users);
                $q->select(function ($row) {
                    return $row['user_id'];
                });
            }
        ]);
        $result = iterator_to_array($q);
        self::assertEquals(range(1, 5), $result);
    }

    /** @test */
    function filter() {
        $attribs = $this->attributes('users');
        $email = $attribs['email'];
        $select = $this->withProjection($attribs);
        $q = $this->fetch([
            function (Query $q) use ($email, $select) {
                $q->from($this->users);
                $q->where($this->eq($email, 'g@h.i'));
                $select($q);
            },
        ]);
        $row = current(iterator_to_array($q));
        self::assertEquals(3, $row['id']);
        self::assertEquals('g@h.i', $row['email']);
        self::assertEquals('third', $row['name']);
    }

    /** @test */
    function membership_query() {
        $attrs = $this->attributes('users');
        $id = $attrs['id'];
        $q = $this->fetch([
            function (Query $q) use ($id) {
                $q->from($this->users);
                $q->where($this->in($id, [1, 3, 5]));
            },
            $this->withProjection($attrs),
        ]);
        $result = iterator_to_array($q);
        self::assertCount(3, $result);
    }

    /** @test */
    function subquery() {
        $attrs = $this->attributes('users');
        $id = $attrs['id'];
        $evens = $this->fetch([
            function (Query $q) use ($id) {
                $q->from($this->users);
                $q->where(function ($u) use ($id) {
                    return $id->get($u) % 2 == 0;
                });
                $q->select(function ($u) use ($id) {
                    return $id->get($u);
                });
            }
        ]);
        $q = $this->fetch([
            function (Query $q) use ($evens, $id) {
                $q->from($this->users);
                $q->where($this->in($id, $evens));
            },
            $this->withProjection([$id]),
        ]);
        $ids = [];
        foreach ($q as $u) {
            $ids[] = $u['id'];
        }
        self::assertEquals([2, 4], $ids);
    }

    function join($right, callable $on) {
        return function ($left) use ($right, $on) {
            foreach ($left as $l) {
                foreach ($right as $r) {
                    $row = $l + $r;
                    if ($on($row)) {
                        yield $row;
                    }
                }
            }
        };
    }

    /** @test */
    function inner_join() {
        $users = $this->attributes('users');
        $roles = $this->attributes('roles');
        $pk = $users['id'];
        $fk = $roles['user'];
        $q = $this->fetch([
            function (Query $q) use ($pk, $fk) {
                $q->from($this->users);
                $q->from($this->join($this->roles, $this->eq($pk, $fk)));
            },
            $this->withProjection([$pk, $users['name'], $roles['role']]),
        ]);
        $ids = [];
        foreach ($q as $row) {
            $id = $row['id'];
            if (array_key_exists($id, $ids)) {
                $ids[$id]++;
            } else {
                $ids[$id] = 1;
            }
        }
        // user #1 has two roles, user #5 has none, everyone else has one.
        self::assertArrayNotHasKey(5, $ids);
        self::assertEquals(2, $ids[1]);
        self::assertCount(4, $ids);
    }

    /** @test */
    function filter_joined() {
        $users = $this->attributes('users');
        $roles = $this->attributes('roles');
        $pk = $users['id'];
        $fk = $roles['user'];
        $role = $roles['role'];
        $q = $this->fetch([
            function (Query $q) use ($pk, $fk, $role) {
                $q->from($this->users);
                $q->from($this->join($this->roles, $this->eq($pk, $fk)));
                $q->where($this->eq($role, 'staff'));
            },
            $this->withProjection([$pk, $users['name'], $role]),
        ]);
        $ids = [];
        foreach ($q as $row) {
            self::assertEquals('staff', $row['role']);
            $ids[] = $row['id'];
        }
        self::assertEquals([3, 4], $ids);
    }

    /** @test */
    function inner_limit_join() {
        $users = $this->attributes('users');
        $roles = $this->Attributes('roles');
        $pk = $users['id'];
        $fk = $roles['user'];
        $firstTwoUsers = $this->fetch([$this->page(0, 2)]);
        $q = $this->fetch([
            function (Query $q) use ($firstTwoUsers, $pk, $fk) {
                $q->from($firstTwoUsers);
                $q->from($this->join($this->roles, $this->eq($pk, $fk)));
            },
            $this->withProjection([$pk, $users['name'], $roles['role']])
        ]);
        self::assertEquals(3, iterator_count($q));
    }

}

