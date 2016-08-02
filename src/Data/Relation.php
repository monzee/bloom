<?php

namespace Codeia\Data;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Query
 *
 * Typedefs:
 * Row := Map (str * str)
 * Iterable := array | Traversable
 * Table := Iterable | (Iterable -> Iterable)
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface Relation extends \Traversable {

    /**
     * @param callable $column :: Row -> any
     */
    function select(callable $column);

    /**
     * @param array|Traversable|callable $table :: Table
     */
    function from($table);

    /**
     * @param callable $predicate :: Row -> bool
     */
    function where(callable $predicate);

    /**
     * @param int $n
     */
    function limit($n);

    /**
     * @param int $n
     */
    function offset($n);

}
