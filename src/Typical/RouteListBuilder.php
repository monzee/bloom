<?php

namespace Codeia\Typical;

use FastRoute\RouteCollector;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of RouteListBuilder
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class RouteListBuilder {

    private $routes;

    function __construct(RouteCollector $rc) {
        $this->routes = $rc;
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
        $this->routes->addRoute($methods, $path, $handler);
        return $this;
    }

}
