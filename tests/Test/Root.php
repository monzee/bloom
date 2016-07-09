<?php

namespace Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Root
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Root {
    public $a;
    public $b;
    public $c;

    function __construct(Dep1 $a, Dep2 $b, Dep3 $c = null) {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }
}
