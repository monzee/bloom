<?php

namespace Codeia\Mvc;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * The FrontController is actually a Model in the root MVC. Don't be confused.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface FrontController extends EntryPoint {

    /**
     * C, V, Cont => Class C, Class V, Class Cont -> ()
     * where C :> Controller, V :> View, Cont :> ContainerInterface
     *
     * The arguments must be service names (usually just class names) as
     * strings, not instances. They should be resolvable by a
     * ContainerInterface. The $contextClass must be resolvable by the root
     * container. The other two should be resolvable by either the root or an
     * instance of $contextClass.
     *
     * @param string $controllerClass Class should extend Mvc\Controller
     * @param string $viewClass       Class should extend Mvc\View
     * @param string $contextClass    Class should extend ContainerInterface
     */
    function setRoute($controllerClass, $viewClass, $contextClass = null);
}
