<?php

namespace Codeia\Bloom;

use Codeia\Di\ObjectGraphBuilder;
use Codeia\Di\ContainerGraph;
use Codeia\Di\AutoResolve;
use Codeia\Di\EmptyContainer;
use Codeia\Mvc\FrontController;
use Codeia\Mvc\Routable;
use Codeia\Typical\BaseUri;
use Codeia\Typical\HttpState;
use Codeia\Typical\RoutableController;
use Codeia\Typical\Template;
use Codeia\Typical\TemplateView;

use Psr\Container\ContainerInterface;
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
    private $response;

    /**
     * Declare routes in the callable argument.
     *
     * @param callable $init Function of type FastRouteInit*(str*str) -> ()
     *                       The second param is the pair of default controller
     *                       and view classes.
     */
    function __construct(callable $init = null) {
        $this->preRoute = $init;
    }

    /**
     * For zend-expressive and other middleware-based frameworks compatibility.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    function __invoke(RequestInterface $request, ResponseInterface $response) {
        if (!($request instanceof ServerRequestInterface)) {
            $request = new ServerRequest(
                $request->getMethod(),
                $request->getUri(),
                $request->getHeaders(),
                $request->getBody(),
                $request->getProtocolVersion(),
                $request->getServerParams()
            );
        }
        $this->request = $request;
        $this->response = $response;
        return $this->main(new EmptyContainer);
    }

    function setRoute($controllerClass, $viewClass, $contextClass = null) {
        $this->controller = $controllerClass;
        $this->view = $viewClass;
        $this->context = $contextClass;
    }

    function main(ContainerInterface $c) {
        $defaults = $this->getContext();
        $c = new AutoResolve(new ContainerGraph($defaults, $c));
        if (!empty($this->preRoute)) {
            call_user_func(
                $this->preRoute,
                $c->get(FastRouteInit::class),
                [RoutableController::class, TemplateView::class]
            );
        }
        $router = $c->get(FastRouteDispatch::class);
        $this->request = $c->get(ServerRequestInterface::class);
        $this->request = $router->dispatch($this->request);

        if ($this->context && $c->has($this->context)) {
            $more = $c->get($this->context);
            $c->tap(function (ContainerInterface $wrapped) use ($more) {
                return new ContainerGraph($wrapped, $more);
            });
        }

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
            'router' => [FastRouteDispatch::class],
            'routeDispatcher' => [RouteDispatcher::class],
            'routeBuilder' => [FastRouteInit::class],
            'baseUri' => [BaseUri::class],
        ])->withScoped([
            'routeCollector' => [RouteCollector::class],
            'httpState' => [HttpState::class],
            'template' => [Template::class, Routable::class],
            'view' => [TemplateView::class],
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
        return $this->response ?: new Response();
    }

    function httpState(ContainerInterface $c) {
        return new HttpState($c->get(ServerRequestInterface::class));
    }

    function router(ContainerInterface $c) {
        return new FastRouteDispatch(
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
        return new FastRouteInit($c->get(RouteCollector::class));
    }

    function template() {
        return new Template();
    }

    function view(ContainerInterface $c) {
        return new TemplateView(
            $c->get(Template::class),
            $c->get(HttpState::class)
        );
    }

    function baseUri(ContainerInterface $c) {
        return $c->get(HttpState::class)->baseUri();
    }

}
