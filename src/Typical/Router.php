<?php

namespace Codeia\Typical;

use Codeia\Mvc\Controller;
use Codeia\Mvc\FrontController;
use Codeia\Typical\TemplateView;
use Psr\Http\Message\ServerRequestInterface;
use FastRoute\Dispatcher as RouteDispatcher;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Router
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 * @deprecated moved to {@see Codeia\Bloom\FastRouteDispatch}
 */
class Router implements Controller {

    const DEFAULT_VIEW = TemplateView::class;

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
                // TODO
                return $r->withAttribute(
                    HttpState::STATUS, HttpState::STATUS_NOT_FOUND
                );

            case 2:  // matched path but not the method
                list(, $allowedMethods) = $result;
                // TODO
                return $r->withAttribute(
                    HttpState::STATUS, '405 Method Not Allowed'
                );

            default:
                list(, $message, $attrs) = $result;
                foreach ($attrs as $k => $v) {
                    $r = $r->withAttribute($k, $v);
                }
                if (is_callable($message)) {
                    return $message($this->front) ?: $r;
                }
                if (is_array($message)) {
                    call_user_func_array([$this->front, 'setRoute'], $message);
                    return $r;
                }
                if (is_string($message)) {
                    $this->front->setRoute($message, self::DEFAULT_VIEW);
                    return $r;
                }
                // what do?
                break;
        }
    }

}
