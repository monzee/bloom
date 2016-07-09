<?php

namespace demo\fizzbuzz;

use Codeia\Mvc\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Sets up the FizzBuzz to be rendered.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class FizzBuzzController implements Controller {

    private $fizzbuzz;

    function __construct(FizzBuzz $model) {
        $this->fizzbuzz = $model;
    }

    function dispatch(Request $r) {
        $this->fizzbuzz->at((int) $r->getAttribute('prev', 1));
    }

}
