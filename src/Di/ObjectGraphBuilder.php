<?php

namespace Codeia\Di;

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

    const SCOPED = true;

    private $services = [];
    private $scoped = [];
    private $module;

    function __construct($p) {
        $this->module = $p;
    }

    function bind($method, $types, $scoped = false) {
        list($which, $other) = $scoped ?
            ['scoped', 'services'] :
            ['services', 'scoped'];
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
            $this->{$which}[$types] = $method;
            unset($this->{$other}[$types]);
            return $this;
        }
        foreach ($types as $type) {
            $this->{$which}[$type] = $method;
            unset($this->{$other}[$type]);
        }
        return $this;
    }

    function withServices(array $services) {
        foreach ($services as $method => $type) {
            $this->bind($method, $type);
        }
        return $this;
    }

    function withScoped(array $services) {
        foreach ($services as $method => $type) {
            $this->bind($method, $type, self::SCOPED);
        }
        return $this;
    }

    function build() {
        $c = new Component($this->module, $this->services, $this->scoped);
        return new ObjectGraph($c);
    }

}
