<?php

namespace Codeia\Mvc;

use Psr\Http\Message\ServerRequestInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Routable
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface Routable extends Locatable {

    /** @return ServerRequestInterface */
    function traverse(ServerRequestInterface $r);
}
