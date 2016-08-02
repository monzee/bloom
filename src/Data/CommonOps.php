<?php

namespace Codeia\Data;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Common operations used in queries.
 *
 * Typedefs:
 * Row := Map (str * any)
 * Predicate := Row -> bool
 * Value T := (Row -> T) | T
 * Iterable := array | Traversable
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface CommonOps {

    const INNER = 0;
    const LEFT_OUTER = 1;
    const RIGHT_OUTER = 2;

    /**
     * @param mixed $left :: Value any
     * @param mixed $right :: Value any
     * @return callable :: Predicate
     */
    function eq($left, $right);

    /**
     *
     * @param mixed $needle :: Value T
     * @param mixed $haystack :: Iterable T
     * @return callable :: Predicate
     */
    function in($needle, $haystack);

    /**
     *
     * @param callable $branch
     * @return callable :: Predicate
     */
    function _and(callable $branch);

    /**
     *
     * @param callable $branch :: Predicate
     * @return callable :: Predicate
     */
    function _or(callable $branch);

    /**
     * @param array|Traversable $right :: List Row
     * @param callable $on :: Predicate
     * @param int $mode One of INNER, LEFT_OUTER or RIGHT_OUTER
     * @return callable :: Iterable -> Iterable
     */
    function join($right, $on, $mode = self::INNER);

    /**
     * @param array|ArrayAccess $table
     * @param array $colNames :: List str
     * @return callable :: Row -> int ; returns the row_id of the new row
     */
    function into($table, array $colNames);

    /**
     * @param array|ArrayAccess|Column $table
     * @param array $changes :: List str
     * @return callable :: Row -> int ; number of affected rows
     */
    function set($table, array $changes);

}
