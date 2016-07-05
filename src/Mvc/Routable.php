<?php

namespace Codeia\Mvc;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Routable
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface Routable {

    /** @return RequestInterface */
    function traverse(RequestInterface $r);

    /** @return UriInterface */
    function locate(UriInterface $uri);

}
