<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Contains values only; immutable.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class ValueContainer implements ContainerInterface {

    private $values;

    function __construct(array $values) {
        $this->values = $values;
    }

    function get($id) {
        if (!array_key_exists($id, $this->values)) {
            throw new UnknownServiceError($id);
        }
        return $this->values[$id];
    }

    function has($id) {
        return array_key_exists($id, $this->values);
    }
}
