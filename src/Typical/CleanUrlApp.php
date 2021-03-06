<?php

namespace Codeia\Typical;

use Codeia\Mvc;
use Codeia\Di\AutoResolve;
use Codeia\Di\ObjectGraphBuilder;
use Psr\Container\ContainerInterface;
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
 * @deprecated use {@see Codeia\Bloom\GuzzleFastRoute}
 */
class CleanUrlApp implements ContainerInterface {

    const DEFAULT_ROUTE = [RoutableController::class, TemplateView::class];

    protected $container;
    protected $superseded = [];
    protected $fallback;

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
        error_log($id);
        error_log(join('|', $this->superseded));
        return !in_array($id, $this->superseded) && $this->container->has($id);
    }

    function supersede(ContainerInterface $newServices, array $ids) {
        $this->fallback = $newServices;
        foreach ($ids as $id) {
            $this->superseded[] = $id;
        }
    }

    function newContext() {
        $builder = (new ObjectGraphBuilder($this))->withServices([
            'request' => [ServerRequestInterface::class, RequestInterface::Class],
            'response' => [ResponseInterface::class],
            'metaController' => [Mvc\Controller::class],
            'metaView' => [Mvc\View::class],
        ])->withScoped([
            'http' => [HttpState::class],
            'controller' => [RoutableController::class],
            'view' => [TemplateView::class],
            'dispatcher' => [Mvc\EntryPoint::class, Mvc\FrontController::class],
            'template' => [Mvc\Routable::class, Template::class],
            'routeCollector' => [RouteCollector::class],
            'routeDispatcher' => [RouteDispatcher::class],
            'routesBuilder' => [RouteListBuilder::class],
        ]);
        $this->willBuild($builder);
        return $builder->build();
    }

    function willBuild(ObjectGraphBuilder $builder) {
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

    function http(ContainerInterface $c) {
        return new HttpState($this->get(ServerRequestInterface::class));
    }

    function controller(ContainerInterface $c) {
        return new RoutableController($c->get(Mvc\Routable::class));
    }

    function view(ContainerInterface $c) {
        return new TemplateView(
            $c->get(Template::class),
            $c->get(HttpState::class)
        );
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
