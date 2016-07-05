<?php

namespace Codeia\Typical;

use Codeia\Mvc\Controller;
use Codeia\Mvc\FrontController;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Dispatcher
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Dispatcher implements FrontController {

    private $controller;
    private $view;

    function setRoute($controllerClass, $viewClass) {
        $this->controller = $controllerClass;
        $this->view = $viewClass;
    }

    function main(ContainerInterface $c) {
        $request = $c->get(RequestInterface::class);
        $request = $c->get(Controller::class)->dispatch($request) ?: $request;
        if (null !== $this->controller) {
            $c->get($this->controller)->dispatch($request);
        }
        $response = $c->get(ResponseInterface::class);
        if (null !== $this->view) {
            $response = $c->get($this->view)->fold($response) ?: $response;
        }
        return $response;
    }

}