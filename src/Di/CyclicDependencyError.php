<?php

namespace Codeia\Typical;

use RuntimeException;
use Interop\Container\Exception\ContainerException;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of CyclicDependencyError
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class CyclicDependencyError extends RuntimeException
    implements ContainerException {

    function __construct(array $path) {
        $root = $path[0];
        $p = implode(' -> ', $path);
        parent::__construct(
            "Cyclic dependency while resolving {$root}: [{$p}]", 500
        );
    }

}
