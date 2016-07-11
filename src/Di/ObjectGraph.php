<?php

namespace Codeia\Di;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * You'd probably want to use a builder to make one of these.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class ObjectGraph implements ReplaceableContainer {

    private $component;
    private $resolving = [];
    private $superseded = [];

    function __construct(Component $c) {
        $this->component = $c;
    }

    function get($id) {
        if ($this->component->provides($id)) {
            $this->ensureNoCycles($id);
            $instance = $this->component->make($id, $this);
            array_pop($this->resolving);
            return $instance;
        }
        throw new UnknownServiceError($id);
    }

    function has($id) {
        return !in_array($id, $this->superseded) &&
            $this->component->provides($id);
    }

    function supersede($id) {
        $this->superseded[] = $id;
    }

    private function ensureNoCycles($name) {
        if (in_array($name, $this->resolving)) {
            throw new CyclicDependencyError($this->resolving);
        }
        $this->resolving[] = $name;
    }


}
