<?php

namespace Codeia\Mvc;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of FrontController
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface FrontController extends EntryPoint {

    function setRoute($controllerClass, $viewClass);
}
