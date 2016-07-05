<?php

namespace Codeia\Typical;

use Codeia\Mvc\Routable;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\Psr7\Uri;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of CanGenerateUrl
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanGenerateUrls {

    /**
     * @param Routable $resource
     * @param UriInterface $base
     * @return UriInterface
     */
    function urlTo(Routable $resource, UriInterface $base = null) {
        return $resource->locate($base ?: new Uri('/'));
    }

}
