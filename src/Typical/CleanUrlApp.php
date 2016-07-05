<?php

namespace Codeia\Typical;

use Interop\Container\ContainerInterface;
use Codeia\Mvc;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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
class CleanUrlApp implements ContainerInterface {

    use CanGenerateUrls;

    private $scoped = [];
    private $resolving = [];
    private $services;

    function __construct(array $services = []) {
        $this->services = $services;
    }

    function get($id) {
        $this->ensureNoCycles($id);
        switch ($id) {
            case Mvc\EntryPoint::class:  // fallthrough
            case Mvc\FrontController::class:
                $inst = $this->scoped('provideFrontController');
                break;
            case Mvc\Controller::class:
                $inst = $this->provideRouter();
                break;
            case Mvc\View::class:
                $inst = $this->provideRootView();
                break;
            case Mvc\Routable::class:  // fallthrough
            case Template::class:
                $inst = $this->scoped('provideTemplate');
                break;
            case RoutableController::class:
                $inst = $this->provideVanillaController();
                break;
            case TemplateBasedView::class:
                $inst = $this->provideTemplateRenderer();
                break;
            case RequestInterface::class:
                $inst = $this->provideRequest();
                break;
            case ResponseInterface::class:
                $inst = $this->provideResponse();
                break;
            default:
                throw new UnknownServiceError($id);
        }
        array_pop($this->resolving);
        return $inst;
    }

    function has($id) {
        return true;  // not really
    }

    protected function scoped($name) {
        if (!array_key_exists($name, $this->scoped)) {
            $this->scoped[$name] = $this->$name();
        }
        return $this->scoped[$name];
    }

    private function ensureNoCycles($current) {
        if (in_array($current, $this->resolving)) {
            throw new CyclicDependencyError($this->resolving);
        }
        $this->resolving[] = $current;
    }

    private function provideFrontController() {
        return new Dispatcher();
    }

    private function provideRouter() {
        return new Router($this->get(Mvc\FrontController::class));
    }

    private function provideRootView() {
        return new Responder();
    }

    private function provideVanillaController() {
        return new RoutableController($this->get(Mvc\Routable::class));
    }

    private function provideTemplateRenderer() {
        return new TemplateBasedView($this->get(Template::class));
    }

    private function provideTemplate() {
        return new Template();
    }

    private function provideRequest() {
        return ServerRequest::fromGlobals();
    }

    private function provideResponse() {
        return new Response();
    }

}
