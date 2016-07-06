<?php

namespace Codeia\Typical;

use Codeia\Mvc\Routable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Template
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Template implements Routable {

    public $page;
    public $extras = [];

    function __construct($page = 'index') {
        $this->page = $page;
    }

    function bind($key, $value) {
        $this->extras[$key] = $value;
    }

    function traverse(RequestInterface $r) {
        $uri = $r->getUri();
        if (preg_match('#^/?([^/]+)(.*)#', $uri->getPath(), $matches)) {
            $this->page = $matches[1];
            return $r->withUri($uri->withPath($matches[2]));
        }
        return $r;
    }

    function locate(UriInterface $uri) {
        return $uri->withPath($this->page == 'index' ? '/' : $this->page);
    }

    function unit() {
        return null;
    }
}
