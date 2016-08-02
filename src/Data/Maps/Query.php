<?php

namespace Codeia\Data\Maps;

use Codeia\Data\Relation;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Query
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Query implements \IteratorAggregate, Relation {

    private $source;
    private $predicate;
    private $projection;
    private $limit = -1;
    private $offset = 0;

    function from($table) {
        if (empty($this->source) || !is_callable($table)) {
            $this->source = $table;
        } else {
            $this->source = $table($this->source);
        }
    }

    function where(callable $predicate) {
        if (empty($this->predicate)) {
            $this->predicate = $predicate;
        } else {
            $this->predicate = function ($row) use ($predicate) {
                $f = $this->predicate;
                return $f($row) && $predicate($row);
            };
        }
    }

    function select(callable $columns) {
        if (empty($this->projection)) {
            $this->projection = $columns;
        } else {
            $this->projection = function ($row) use ($columns) {
                $f = $this->projection;
                $left = $columns($row);
                if (!is_array($left)) {
                    return $left;
                }
                $right = $f($row);
                if (is_array($right)) {
                    return array_merge($left + $right);
                } else {
                    $left[] = $right;
                    return $left;
                }
            };
        }
    }

    function limit($n) {
        $this->limit = $n;
    }

    function offset($n) {
        $this->offset = max(0, $n);
    }

    function getIterator() {
        foreach ($this->source as $row) {
            if ($this->limit == 0) {
                break;
            }
            if ($this->filter($row)) {
                if ($this->offset-- > 0) {
                    continue;
                }
                $this->limit--;
                yield $this->project($row);
            }
        }
    }

    private function filter($row) {
        $f = $this->predicate;
        return empty($f) || $f($row);
    }

    private function project($row) {
        $f = $this->projection;
        return empty($f) ? $row : $f($row);
    }

}
