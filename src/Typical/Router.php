<?php

namespace Codeia\Typical;

use Codeia\Mvc\Controller;
use Codeia\Mvc\FrontController;
use Psr\Http\Message\ServerRequestInterface;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher as RouteDispatcher;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Router
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Router implements Controller {

    private $front;
    private $dispatcher;

    function __construct(FrontController $m, RouteDispatcher $d) {
        $this->front = $m;
        $this->dispatcher = $d;
    }

    function dispatch(ServerRequestInterface $r) {
        $uri = $r->getUri();
        $result = $this->dispatcher->dispatch($r->getMethod(), $uri->getPath());
        switch (count($result)) {
            case 1:  // not found
                break;
            case 2:  // matched path but not the method
                break;

            case 3:
                list(, $handler, $attrs) = $result;
                foreach ($attrs as $k => $v) {
                    $r = $r->withAttribute($k, $v);
                }
                if (is_callable($handler)) {
                    return $handler($this->front) ?: $r;
                }
                list($c, $v, $context) = $handler;
                $this->front->setRoute($c, $v, $context);
                return $r;

            default:
                break;
        }
    }

    static function on(RouteCollector $rc) {
        return new RouteListBuilder($rc);
    }

}
