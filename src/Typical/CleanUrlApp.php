<?php

namespace Codeia\Typical;

use Codeia\Mvc;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of CleanUrlApp
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class CleanUrlApp extends Context implements ContainerInterface {

    use CanGenerateUrls;

    private $services;

    function __construct(array $services = []) {
        parent::__construct();
        $this->services = $services;
    }

    function provide($id) {
        switch ($id) {
            case Mvc\EntryPoint::class:  // fallthrough
            case Mvc\FrontController::class:
                return $this->scoped('dispatcher');

            case Mvc\Controller::class:
                return new Router($this->get(Mvc\FrontController::class));

            case Mvc\View::class:
                return new Responder();

            case Mvc\Routable::class:  // fallthrough
            case Template::class:
                return $this->scoped('template');

            case RoutableController::class:
                return new RoutableController($this->get(Mvc\Routable::class));

            case TemplateBasedView::class:
                return new TemplateBasedView($this->get(Template::class));

            case RequestInterface::class:  // fallthrough
            case ServerRequestInterface::class:
                return ServerRequest::fromGlobals();

            case ResponseInterface::class:
                return new Response();

            default:
                throw new UnknownServiceError($id);
        }
    }

    function has($id) {
        return true;  // not really
    }

    protected function dispatcher() {
        return new Dispatcher();
    }

    protected function template() {
        return new Template();
    }

}
