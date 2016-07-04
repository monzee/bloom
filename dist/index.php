<?php

namespace scratch;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

require_once __DIR__ . '/../vendor/autoload.php';

class Bloom {

    static function run(ContainerInterface $context) {
        $front = $context->get(FrontController::class);
        $context->get(View::class)->fold($front->main($context));
    }

}

class Context implements ContainerInterface {

    function get($id) {
        switch ($id) {
            case RequestInterface::class:
                return $this->provideRequest();
            case ResponseInterface::class:
                return $this->provideResponse();
            case FrontController::class:
                return $this->provideFrontController();
            case Controller::class:
                return $this->provideController();
            case View::class:
                return $this->provideView();
            case StaticPage::class:
                return $this->provideStaticPage();
            case RenderPage::class:
                return $this->provideRenderPage();
            default:
                throw new \InvalidArgumentException("unknown: {$id}");
        }
    }

    function has($id) {
        return true;
    }

    private function provideRequest() {
        return ServerRequest::fromGlobals();
    }

    private function provideResponse() {
        return new Response();
    }

    private function provideFrontController() {
        return $this->scoped('_frontController');
    }

    private function provideController() {
        return new Router($this->provideFrontController());
    }

    private function provideView() {
        return new Responder();
    }

    private function provideStaticPage() {
        return new StaticPage($this->providePhtml());
    }

    private function provideRenderPage() {
        return new RenderPage($this->providePhtml());
    }

    private function providePhtml() {
        return $this->scoped('_phtml');
    }

    private function _frontController() {
        return new Dispatcher();
    }

    private function _phtml() {
        return new Phtml();
    }

    private function scoped($name) {
        static $instances = [];
        if (!isset($instances[$name])) {
            $instances[$name] = $this->$name();
        }
        return $instances[$name];
    }

}

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

class Router implements Controller {

    private $dispatcher;

    function __construct(FrontController $m) {
        $this->dispatcher = $m;
    }

    function dispatch(RequestInterface $r) {
        $this->dispatcher->setRoute(StaticPage::class, RenderPage::class);
    }

}

class Responder implements View {

    function fold(ResponseInterface $r) {
        $response = $r->withAddedHeader('X-Powered-By', 'BLOOM/0.1.0-dev');
        $this->emit($response);
        return $response;
    }

    private function emit(ResponseInterface $response) {
        if (headers_sent()) {
            error_log('headers already sent.');
        } else {
            header('HTTP/' . $response->getProtocolVersion()
                . ' ' . $response->getStatusCode()
                . ' ' . $response->getReasonPhrase());
            foreach ($response->getHeaders() as $header => $values) {
                $value = implode(', ', $values);
                header("{$header}: {$value}");
            }
        }
        echo $response->getBody();
    }

}

class Phtml implements Routable {

    public $page;

    function locate(UriInterface $uri) {
        return $uri->withPath($this->page . '.php');
    }

}

class StaticPage implements Controller {

    private $model;

    function __construct(Phtml $m) {
        $this->model = $m;
    }

    function dispatch(RequestInterface $r) {
        $this->model->page = 'index';
    }

}

class RenderPage implements View {

    private $model;

    function __construct(Phtml $m) {
        $this->model = $m;
    }

    function fold(ResponseInterface $r) {
        $r = $r->withStatus(200)->withHeader('content-type', 'text/html');
        ob_start();
        $this->render($this->model->page . '.phtml');
        $r->getBody()->write(ob_get_clean());
        return $r;
    }

    private function render($tpl) {
        $url = $this->model->locate(new Uri());
        include $tpl;
    }

}

trait CanGenerateUrl {

    /**
     * @param Routable $resource
     * @param Uri $base
     * @return Uri
     */
    function urlTo(Routable $resource, Uri $base = null) {
        return $resource->locate($base ?: new Uri());
    }

}

trait CanRenderTemplate {

    private $_templatePaths = ['.'];

    /**
     * @param string $file
     * @return string|bool The full path of the template or false if not found.
     */
    function find($file) {

    }

    function render($file, array $vars = []) {

    }

}

Bloom::run(new Context());
