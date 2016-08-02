<?php

namespace Codeia\Typical;

use Codeia\Data\Attribute;
use Codeia\Data\Backend;
use Codeia\Data\Relation;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of CanQueryIterables
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanQueryIterables {

    abstract function primarySource();

    function all() {
        return function (Relation $q) {
            $q->from($this->primarySource());
            $q->limit(-1);
        };
    }

    function one() {
        return function (Relation $q) {
            $q->from($this->primarySource());
            $q->limit(1);
        };
    }

    function page($pageNum = 0, $pageSize = 10) {
        $pageNum = max(0, $pageNum);
        return function (Relation $q) use ($pageNum, $pageSize) {
            $q->from($this->primarySource());
            $q->limit($pageSize);
            $q->offset($pageNum * $pageSize);
        };
    }

    function withProjection(array $colObjects) {
        return function (Relation $q) use ($colObjects) {
            $q->select(static::pluckColumns($colObjects));
        };
    }

    function withColumns(Backend $db, array $tableColumns) {
        return function (Relation $q) use ($db, $tableColumns) {
            $colObjects = [];
            foreach ($tableColumns as $table => $columns) {
                $attribs = $db->attributes($table);
                foreach ($columns as $col) {
                    $colObjects[] = $attribs[$col];
                }
            }
            $q->select(static::pluckColumns($colObjects));
        };
    }

    static function pluckColumns(array $columns) {
        return function ($row) use ($columns) {
            $result = [];
            foreach ($columns as $col) {
                if ($col instanceof Attribute) {
                    $result[$col->name()] = $col->get($row);
                } else if (!is_callable($col)) {
                    $result[] = $col;
                } else if (is_callable([$col, '__toString'])) {
                    $result[(string) $col] = $col($row);
                } else {
                    $result = $col($row);
                }
            }
            return $result;
        };
    }

}
