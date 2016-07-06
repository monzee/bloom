<?php

namespace Codeia\Mvc;

use Psr\Http\Message\ServerRequestInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Controller
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface Controller {

    /** @return ServerRequestInterface|null */
    function dispatch(ServerRequestInterface $r);
}
