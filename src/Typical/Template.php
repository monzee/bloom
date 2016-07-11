<?php

namespace Codeia\Typical;

use Codeia\Mvc\Routable;
use Codeia\Typical\BaseUri;
use Psr\Http\Message\ServerRequestInterface;
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

    function traverse(ServerRequestInterface $r) {
        $baseUri = BaseUri::fromRequest($r)->build();
        $prefix = addslashes($baseUri->getPath());
        $pattern = "#^{$prefix}/?([^/]+)(.*)#";
        $uri = $r->getUri();
        if (preg_match($pattern, $uri->getPath(), $matches)) {
            $this->page = $matches[1];
            return $r->withUri($uri->withPath($matches[2]));
        }
        return $r;
    }

    function locate(UriInterface $uri) {
        $path = $this->page == 'index' ? '/' : $this->page;
        $base = $uri->getPath();
        if (!empty($base)) {
            $path = rtrim($base, '/') . '/' . ltrim($path, '/');
        }
        return $uri->withPath($path);
    }

}
