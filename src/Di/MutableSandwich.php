<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * A container that provides values on top (or below) of an existing container.
 *
 * This container has no instantiation logic. If a service should be provided,
 * it must be instantiated first before being pushed or unshifted here.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class MutableSandwich implements ContainerInterface {
    private $meat;
    private $front = [];
    private $back = [];

    function __construct(ContainerInterface $edible) {
        $this->meat = $edible;
    }

    /**
     * @param string $id
     * @param mixed $value
     */
    function push($id, $value) {
        $this->back[$id] = $value;
    }

    /**
     * @param string $id
     * @param mixed $value
     */
    function unshift($id, $value) {
        $this->front[$id] = $value;
    }

    function get($id) {
        if (array_key_exists($id, $this->front)) {
            return $this->front[$id];
        }
        if ($this->meat->has($id)) {
            return $this->meat->get($id);
        }
        if (array_key_exists($id, $this->back)) {
            return $this->back[$id];
        }
        throw new UnknownServiceError($id);
    }

    function has($id) {
        return array_key_exists($id, $this->front)
            || $this->meat->has($id)
            || array_key_exists($id, $this->back);
    }
}
