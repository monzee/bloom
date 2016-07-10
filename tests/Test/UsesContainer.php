<?php

namespace Codeia\Test;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of UsesContainer
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class UsesContainer {
    public $container;

    function __construct(ContainerInterface $c) {
        $this->container = $c;
    }
}
