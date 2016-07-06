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

    function setRoute($controllerClass, $viewClass, $contextClass = null);
}
