<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of AutoResolve
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class AutoResolve implements ContainerInterface {

    private $source;
    private $resolving = [];
    private $scope = [];
    private $resolvable = [];

    function __construct(ContainerInterface $container) {
        $this->source = $container;
    }

    function get($id) {
        if ($this->source->has($id)) {
            return $this->source->get($id);
        } else if ($this->maybeResolvable($id)) {
            if (!array_key_exists($id, $this->scope)) {
                $this->scope[$id] = $this->resolve($id);
            }
            return $this->scope[$id];
        }
        throw new CannotAutoResolveError($id);
    }

    function has($id) {
        return $this->source->has($id) || $this->maybeResolvable($id);
    }

    private function resolve($className) {
        $this->ensureNoCycles($className);
        $cls = new \ReflectionClass($className);
        $cons = $cls->getConstructor();
        if (empty($cons)) {
            $instance = $cls->newInstance();
        } else {
            $args = array_map(function (\ReflectionParameter $p) {
                $type = $p->getClass();
                if (!empty($type)) {
                    return $this->get($type->getName());
                }
                // TODO: try to resolve as uri
                // probably should be the first thing to try
                // also should probably be in a subclass/sibling
                // method:<fqcn>/<method>?<param>
                // e.g. method:Foo%5CBar%5CBaz/__construct?theParam
                $name = $p->getName();
                if ($this->source->has($name)) {
                    return $this->get($name);
                }
                if ($p->isDefaultValueAvailable()) {
                    return $p->getDefaultValue();
                }
                throw new CannotAutoResolveError($name);
            }, $cons->getParameters());
            $instance = $cls->newInstanceArgs($args);
        }
        array_pop($this->resolving);
        return $instance;
    }

    private function maybeResolvable($service) {
        if (array_key_exists($service, $this->resolvable)) {
            return true;
        }
        if (!class_exists($service)) {
            return false;
        }
        if ((new \ReflectionClass($service))->isInstantiable()) {
            $this->resolvable[$service] = true;
            return true;
        }
        return false;
    }

    private function ensureNoCycles($name) {
        if (in_array($name, $this->resolving)) {
            throw new CyclicDependencyError($this->resolving);
        }
        $this->resolving[] = $name;
    }
}
