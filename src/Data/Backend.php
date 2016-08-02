<?php

namespace Codeia\Data;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Interprets queries and commands against some database.
 *
 * typedef Ops :: any ; set of commands supported by the backend
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface Backend {

    const FROM = 'from';
    const SELECT = 'select';
    const WHERE = 'where';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const ORDER = 'order';
    const INSERT = 'insert';
    const VALUES = 'values';
    const UPDATE = 'update';
    const RESET = 'reset';
    const FLUSH = 'flush';

    /**
     * Get a relation's body.
     *
     * @param string $name
     * @return array|Traversable
     */
    function relation($name);

    /**
     * Get a relation's heading, i.e. the list of attributes (columns).
     *
     * @param string $relation Relation name
     * @return array :: List Attribute
     */
    function attributes($relation);

    /**
     * Execute block in a transaction.
     *
     * The operations in the block will all be saved or dropped as a whole. If
     * the block returns `false`, the changes are rolled back. Any other return
     * value, including null, is treated as `true` and causes a commit.
     *
     * @param callable $block :: Backend -> bool
     */
    function transact(callable $block);

    /**
     * @param array $queries :: List (callable :: Relation * Ops -> any)
     * @return array|Traversable
     */
    function fetch(array $queries);

    /**
     * @param array $commands :: List (callable :: Relation * Ops -> any)
     * @return int
     */
    function exec(array $commands);

}
