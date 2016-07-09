<?php

namespace Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Dep3
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Dep3 {
    public $a;

    function __construct(SubDep1 $a) {
        $this->a = $a;
    }
}
