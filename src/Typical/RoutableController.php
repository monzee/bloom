<?php

namespace Codeia\Typical;

use Codeia\Mvc\Controller;
use Codeia\Mvc\Routable;
use Psr\Http\Message\ServerRequestInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of RoutableController
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class RoutableController implements Controller {

    private $model;

    function __construct(Routable $model) {
        $this->model = $model;
    }

    function dispatch(ServerRequestInterface $r) {
        return $this->model->traverse($r);
    }

}
