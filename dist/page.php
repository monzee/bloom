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
            case PathToPhtml::class:
                return $this->provideSimplePage();
            case RenderPage::class:
                return $this->providePageRenderer();
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

    private function provideSimplePage() {
        return new PathToPhtml($this->providePhtml());
    }

    private function providePageRenderer() {
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
        $this->dispatcher->setRoute(PathToPhtml::class, RenderPage::class);
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
                $k = urlencode($header);
                $v = urlencode(implode(', ', $values));
                header("{$k}: {$v}");
            }
        }
        echo $response->getBody();
    }

}

class Phtml implements Routable {

    public $page;

    function __construct($page = 'index') {
        $this->page = $page;
    }

    function traverse(RequestInterface $r) {
        $uri = $r->getUri();
        if (preg_match('#^/?([^/]+)(.*)#', $uri->getPath(), $matches)) {
            $this->page = $matches[1];
            return $r->withUri($uri->withPath($matches[2]));
        }
        return $r;
    }

    function locate(UriInterface $uri) {
        return $uri->withPath($this->page == 'index' ? '/' : $this->page);
    }

}

class PathToPhtml implements Controller {

    private $model;

    function __construct(Phtml $m) {
        $this->model = $m;
    }

    function dispatch(RequestInterface $r) {
        return $this->model->traverse($r);
    }

}

class RenderPage implements View {

    use CanGenerateUrls;
    use CanRenderTemplates;

    private $model;

    function __construct(Phtml $m) {
        $this->model = $m;
    }

    function fold(ResponseInterface $r) {
        $r = $r->withStatus(200)->withHeader('content-type', 'text/html');
        $phtml = $this->model->page . '.phtml';
        $url = $this->urlTo(new Phtml('foo'));
        $r->getBody()->write($this->render($phtml, ['url' => $url]));
        return $r;
    }

}

trait CanGenerateUrls {

    /**
     * @param Routable $resource
     * @param Uri $base
     * @return Uri
     */
    function urlTo(Routable $resource, Uri $base = null) {
        return $resource->locate($base ?: new Uri());
    }

}

trait CanRenderTemplates {

    private $_templatePaths = ['.'];

    function render($file, array $vars = []) {
        $path = $this->pathTo($file);
        if ($path !== null) {
            $tpl = new TemplateScope($this);
            ob_start();
            $tpl->render($path, $vars);
            return ob_get_clean();
        }
        throw new \InvalidArgumentException("template {$file} not found.");
    }

    private function pathTo($file) {
        $stem = ltrim($file, '\\/');
        foreach ($this->_templatePaths as $prefix) {
            $base = rtrim($prefix, '\\/');
            $filename = "{$base}/{$stem}";
            $path = stream_resolve_include_path($filename);
            if (false !== $path) {
                return $path;
            }
        }
        return null;
    }
}

class TemplateScope {

    const LEGAL_IDENTIFIER = '#^[[:alpha:]][_[:alnum:]]*#';
    // disallow names starting with underscore

    private $delegate;
    private $raw = [];

    function __construct($delegate) {
        $this->delegate = is_object($delegate) ? $delegate : new stdclass;
    }

    function __call($method, array $args) {
        return call_user_func_array([$this->delegate, $method], $args);
    }

    function __get($name) {
        if (array_key_exists($name, $this->raw)) {
            return $this->raw;
        }
        return null;
    }

    function render($__FILE, array $__LOCALS = []) {
        $this->raw = $__LOCALS;
        foreach ($__LOCALS as $k => $v) {
            if (false !== preg_match(self::LEGAL_IDENTIFIER, $k)) {
                $$k = is_string($v) ? $this->e($v) : $v;
            }
        }
        include $__FILE;
    }

    private function e($str) {
        if (method_exists($this->delegate, 'escape')) {
            return $this->delegate->escape($str);
        }
        return htmlentities($str, ENT_COMPAT | ENT_HTML5, 'UTF-8');
    }
}

Bloom::run(new Context());
