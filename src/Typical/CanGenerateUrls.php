<?php

namespace Codeia\Typical;

use Codeia\Mvc\Locatable;
use Psr\Http\Message\UriInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Convenience methods to generate urls from model objects.
 *
 * You will most likely want to `use` this in your view implementations. The
 * default view ({@see TemplateBasedRenderer}) imports this, so you can call
 * `$this->urlTo($model)` in template files.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanGenerateUrls {

    private $_baseUri;

    /**
     * This uri object will be passed to the Locatable object when urlTo() is
     * called without a $base argument.
     *
     * This is useful if for example your site is not served from the docroot of
     * the web server and you need to prefix the generated urls with the base
     * path.
     *
     * @param UriInterface $uri
     */
    function setBaseUri(UriInterface $uri) {
        $this->_baseUri = $uri;
    }

    /**
     * @param Locatable $resource
     * @param UriInterface|null $base If null, will use the UriInterface passed
     *                                to setBaseUri() as base.
     * @return UriInterface
     */
    function urlTo(Locatable $resource, UriInterface $base = null) {
        $this->_baseUri = $this->_baseUri ?: new UriStub(__CLASS__, __METHOD__);
        return $resource->locate($base ?: $this->_baseUri);
    }

    /**
     * @return UriInterface
     */
    function baseUri() {
        return _baseUri;
    }

}
