<?php

namespace Codeia\Typical;

use Codeia\Mvc;
use Codeia\Di\AutoResolve;
use Codeia\Di\ObjectGraphBuilder;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased as RegexStrategy;
use FastRoute\Dispatcher\GroupCountBased as DispatchStrategy;
use FastRoute\Dispatcher as RouteDispatcher;

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

    const DEFAULT_ROUTE = [RoutableController::class, TemplateBasedView::class];

    protected $container;

    function __construct() {
        $this->container = $this->newContext();
    }

    static function autoResolve() {
        $app = new static();
        $app->container = new AutoResolve($app->container);
        return $app;
    }

    function get($id) {
        return $this->container->get($id);
    }

    function has($id) {
        return $this->container->has($id);
    }

    function newContext() {
        return (new ObjectGraphBuilder($this))->withServices([
            'request' => [ServerRequestInterface::class, RequestInterface::Class],
            'response' => [ResponseInterface::class],
            'metaController' => [Mvc\Controller::class],
            'metaView' => [Mvc\View::class],
            'controller' => [RoutableController::class],
            'view' => [TemplateBasedView::class],
        ])->withScoped([
            'dispatcher' => [Mvc\EntryPoint::class, Mvc\FrontController::class],
            'template' => [Mvc\Routable::class, Template::class],
            'routeCollector' => [RouteCollector::class],
            'routeDispatcher' => [RouteDispatcher::class],
            'routesBuilder' => [RouteListBuilder::class],
        ])->build();
    }

    function dispatcher() {
        return new Application();
    }

    function template() {
        return new Template();
    }

    function metaController(ContainerInterface $c) {
        return new Router(
            $c->get(Mvc\FrontController::class),
            $c->get(RouteDispatcher::class)
        );
    }

    function metaView() {
        return new Responder();
    }

    function controller(ContainerInterface $c) {
        return new RoutableController($c->get(Mvc\Routable::class));
    }

    function view(ContainerInterface $c) {
        return new TemplateBasedView($c->get(Template::class));
    }

    function request() {
        return ServerRequest::fromGlobals();
    }

    function response() {
        return new Response();
    }

    function routeCollector() {
        return new RouteCollector(new Std(), new RegexStrategy());
    }

    function routeDispatcher(ContainerInterface $c) {
        $routeData = $c->get(RouteCollector::class)->getData();
        return new DispatchStrategy($routeData);
    }

    function routesBuilder(ContainerInterface $c) {
        return new RouteListBuilder($c->get(RouteCollector::class));
    }
}
