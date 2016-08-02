<?php

namespace Codeia\Data;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * A column in a data table.
 *
 * forall T
 * name :: Attribute T -> str
 * get :: Attribute T -> Row -> T | null
 * set :: Attribute T -> Row * T -> ()
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface Attribute {

    /**
     * @return string
     */
    function name();

    /**
     * @param array|ArrayAccess &$tuple :: Row
     * @param mixed $value
     */
    function set(&$tuple, $value);

    /**
     * @param array|ArrayAccess $tuple :: Row
     * @return mixed
     */
    function get($tuple);
}
