<?php

namespace Codeia\Typical;

use Codeia\Mvc\Controller;
use Codeia\Mvc\FrontController;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Default HTTP application.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Application implements FrontController {

    private $controller;
    private $view;
    private $context;

    function setRoute($controllerClass, $viewClass, $contextClass = null) {
        $this->controller = $controllerClass;
        $this->view = $viewClass;
        $this->context = $contextClass;
    }

    function main(ContainerInterface $c) {
        $request = $c->get(ServerRequestInterface::class);
        $request = $c->get(Controller::class)->dispatch($request) ?: $request;
        if ($this->context !== null) {
            $c = new ContainerGraph($c, $c->get($this->context));
        }
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