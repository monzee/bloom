<?php

namespace Codeia\Typical;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Context
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
abstract class Context implements ContainerInterface {

    private $superScope;
    private $resolving = [];
    private $scoped = [];
    private $self;

    function __construct(ContainerInterface $parent = null) {
        $this->superScope = $parent;
        $this->self = [ContainerInterface::class, Context::class, static::class];
    }

    /**
     * T => Class T -> T
     * @param string $id
     * @return mixed
     */
    abstract function provide($id);

    function get($id) {
        if (in_array($id, $this->self)) {
            return $this;
        }
        if ($this->has($id)) {
            $this->ensureNoCycles($id);
            $instance = $this->provide($id);
            array_pop($this->resolving);
            return $instance;
        }
        if ($this->superScope !== null) {
            return $this->superScope->get($id);
        }
        throw new UnknownServiceError($id);
    }

    protected function scoped($name) {
        if (!array_key_exists($name, $this->scoped)) {
            $this->scoped[$name] = $this->$name();
        }
        return $this->scoped[$name];
    }

    private function ensureNoCycles($name) {
        if (in_array($name, $this->resolving)) {
            throw new CyclicDependencyError($name, $this->resolving);
        }
        $this->resolving[] = $name;
    }

}
