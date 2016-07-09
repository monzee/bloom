<?php

namespace Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of UsesSomething
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class UsesSomething {
    public $thing;
    function __construct($something) {
        $this->thing = $something;
    }
}
