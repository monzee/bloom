<?php

namespace Codeia\Bloom;

use FastRoute\RouteCollector;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * This probably should be an interface tbhfam
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class FastRouteInit {

    private $routes;
    private $prefix = '';

    function __construct(RouteCollector $rc) {
        $this->routes = $rc;
    }

    function stem($prefix) {
        $this->prefix = rtrim($prefix, '/');
        return $this;
    }

    function get($path, $handler) {
        return $this->add('GET', $path, $handler);
    }

    function post($path, $handler) {
        return $this->add('POST', $path, $handler);
    }

    function any($path, $handler) {
        return $this->add('*', $path, $handler);
    }

    function anyOf(array $methods, $path, $handler) {
        return $this->add($methods, $path, $handler);
    }

    private function add($methods, $path, $handler) {
        if (!empty($this->prefix)) {
            $path = "{basePath:{$this->prefix}}/" . ltrim($path, '/');
        }
        $this->routes->addRoute($methods, $path, $handler);
        return $this;
    }

}
