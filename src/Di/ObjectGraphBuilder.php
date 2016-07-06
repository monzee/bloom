<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of ObjectGraphBuilder
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class ObjectGraphBuilder {

    private $services = [];
    private $scoped = [];
    private $module;

    function __construct($p) {
        $this->module = $p;
    }

    function bind($method, $types, $scoped = false) {
        $which = $scoped ? 'scoped' : 'services';
        if (is_int($method)) {
            if (is_array($types)) {
                foreach ($types as $k => $v) {
                    $this->bind($k, $v, $scoped);
                }
                return $this;
            }
            $method = $types;
        }
        if (!is_array($types)) {
            $types = [$types];
        }
        foreach ($types as $type) {
            $this->{$which}[$type] = $method;
        }
        return $this;
    }

    function withServices(array $services) {
        foreach ($services as $method => $type) {
            $this->bind($method, $type, false);
        }
        return $this;
    }

    function withScoped(array $services) {
        foreach ($services as $method => $type) {
            $this->bind($method, $type, true);
        }
        return $this;
    }

    function build() {
        $c = new Component($this->module, $this->services, $this->scoped);
        return new ObjectGraph($c);
    }

}
