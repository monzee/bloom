<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Nothing. I'm sure this could be useful.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class EmptyContainer implements ContainerInterface {
    const SHOULD_THROW = true;
    const SHOULD_RETURN_NULL = false;

    private $shouldThrow;

    function __construct($onGet = self::SHOULD_RETURN_NULL) {
        $this->shouldThrow = $onGet;
    }

    function get($id) {
        if ($this->shouldThrow) {
            throw new UnknownServiceError($id);
        }
        return null;
    }

    public function has($id) {
        return false;
    }

}
