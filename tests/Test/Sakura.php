<?php

namespace Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Sakura
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Sakura {
    /** @var \Codeia\Test\Emiya */
    private $sempai;
    private $dinner;

    function target() {
        return $this->sempai;
    }

    function other() {
        return $this->dinner;
    }
}
