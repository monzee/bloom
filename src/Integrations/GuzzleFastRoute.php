<?php

namespace Codeia\Integrations;

use Codeia\Di\ObjectGraphBuilder;
use Codeia\Di\MutableSandwich;
use Codeia\Di\ContainerGraph;
use Codeia\Di\AutoResolve;
use Codeia\Mvc\FrontController;
use Codeia\Mvc\Controller;
use Codeia\Mvc\View;
use Codeia\Typical\HttpState;
use Codeia\Mvc\Routable;
use Codeia\Typical\RoutableController;
use Codeia\Typical\RouteListBuilder;
use Codeia\Typical\Router;
use Codeia\Typical\Template;
use Codeia\Typical\TemplateBasedView;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use FastRoute\Dispatcher as RouteDispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\Dispatcher\GroupCountBased as DispatchStrategy;
use FastRoute\DataGenerator\GroupCountBased as BuildStrategy;
// NOTE: DispatchStrategy and BuildStrategy should match

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Psr7 impl provided by GuzzleHttp and routing by FastRoute.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class GuzzleFastRoute implements FrontController {

    private $preRoute;
    private $controller;
    private $view;
    private $context;
    private $request;

    function setRoute($controllerClass, $viewClass, $contextClass = null) {
        $this->controller = $controllerClass;
        $this->view = $viewClass;
        $this->context = $contextClass;
    }

    function main(ContainerInterface $c) {
        $defaults = $this->getContext();
        $c = new MutableSandwich(new AutoResolve(new ContainerGraph($defaults, $c)));
        if (!empty($this->preRoute)) {
            call_user_func(
                $this->preRoute,
                $c->get(RouteListBuilder::class),
                [RoutableController::class, TemplateBasedView::class]
            );
        }
        $router = $c->get(Router::class);
        $this->request = $c->get(ServerRequestInterface::class);
        $this->request = $router->dispatch($this->request);

        // TODO: check $context

        if ($this->controller) {
            $ctrlr = $c->get($this->controller);
            $this->request = $ctrlr->dispatch($this->request) ?: $this->request;
        }

        $response = $c->get(ResponseInterface::class);
        if ($this->view) {
            return $c->get($this->view)->fold($response) ?: $response;
        } else {
            $http = $c->get(HttpState::class);
            list($code, $reason) = $http->status();
            $type = $http->contentType();
            return $response
                ->withStatus($code, $reason)
                ->withHeader('Content-Type', $type);
        }
    }

    function __invoke(callable $init) {
        $this->preRoute = $init;
    }

    function willBuildContext(ObjectGraphBuilder $b) {
    }

    function getContext() {
        $b = (new ObjectGraphBuilder($this))->withServices([
            'front' => [FrontController::class],
            'request' => [
                ServerRequestInterface::class,
                ServerRequest::class,
                RequestInterface::class,
            ],
            'response' => [ResponseInterface::class, Response::class],
            'router' => [Router::class],
            'routeDispatcher' => [RouteDispatcher::class],
        ])->withScoped([
            'routeCollector' => [RouteCollector::class],
            'routeBuilder' => [RouteListBuilder::class],
            'httpState' => [HttpState::class],
            'template' => [Template::class, Routable::class],
        ]);
        $this->willBuildContext($b);
        return $b->build();
    }

    function front() {
        return $this;
    }

    function request() {
        return $this->request ?: ServerRequest::fromGlobals();
    }

    function response() {
        return new Response();
    }

    function httpState(ContainerInterface $c) {
        return new HttpState($c->get(ServerRequestInterface::class));
    }

    function router(ContainerInterface $c) {
        return new Router(
            $c->get(FrontController::class),
            $c->get(RouteDispatcher::class)
        );
    }

    function routeDispatcher(ContainerInterface $c) {
        return new DispatchStrategy(
            $c->get(RouteCollector::class)->getData()
        );
    }

    function routeCollector() {
        return new RouteCollector(new Std, new BuildStrategy);
    }

    function routeBuilder(ContainerInterface $c) {
        return new RouteListBuilder($c->get(RouteCollector::class));
    }

    function template() {
        return new Template();
    }
}
