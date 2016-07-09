<?php

namespace Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of SubDep1
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class SubDep1 {
    public $a;

    function __construct(SubDep2 $a) {
        $this->a = $a;
    }
}
