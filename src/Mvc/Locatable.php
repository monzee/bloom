<?php

namespace Codeia\Mvc;

use Psr\Http\Message\UriInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Locatable
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface Locatable {

    /** @return UriInterface */
    function locate(UriInterface $uri);
}
