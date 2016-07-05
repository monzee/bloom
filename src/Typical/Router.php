<?php

namespace Codeia\Typical;

use Codeia\Mvc\Controller;
use Codeia\Mvc\FrontController;
use Psr\Http\Message\RequestInterface;

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

    private $dispatcher;

    function __construct(FrontController $m) {
        $this->dispatcher = $m;
    }

    function dispatch(RequestInterface $r) {
        $this->dispatcher->setRoute(
            RoutableController::class,
            TemplateBasedView::class);
    }

}