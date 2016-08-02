<?php

namespace Codeia\Data\Maps;

use Codeia\Data\Attribute;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Column implements Attribute {

    private $name;
    private $fullName;

    function __construct($name, $prefix = '') {
        $this->name = $name;
        $this->fullName = "{$prefix}_{$name}";
    }

    function name() {
        return $this->name;
    }

    function get($tuple) {
        $key = $this->fullName;
        if (array_key_exists($key, $tuple)) {
            return $tuple[$key];
        }
        return null;
    }

    function set(&$tuple, $value) {
        $tuple[$this->fullName] = $value;
    }

}
