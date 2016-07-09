<?php

namespace Codeia\Typical;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Builds a URI from the parts of an existing instance.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class BaseUri {

    const SCHEME = 1;
    const USER_INFO = 2;
    const HOST = 4;
    const PORT = 8;
    const AUTHORITY = 14;  // userinfo + host + port
    const PATH = 16;
    const QUERY = 32;
    const FRAGMENT = 64;
    const RESOURCE = 112;  // path + query + fragment
    const ALL = 127;

    private $basePath;
    private $projection;
    private $uri;

    function __construct($basePath = '', UriInterface $uri = null) {
        $this->basePath = $basePath;
        $this->projection = 0;
        $this->uri = $uri;
    }

    /** @return BaseUri */
    static function fromRequest(RequestInterface $request, $basePath = '') {
        return new static($basePath, $request->getUri());
    }

    /**
     * @param int $parts Bit pattern of parts to exclude
     * @return $this
     */
    function without($parts) {
        $this->projection &= ~$parts;
        return $this;
    }

    /**
     * @param int $parts Bit pattern of parts to include
     * @return $this
     */
    function with($parts) {
        $this->projection |= $parts;
        return $this;
    }

    /** @return UriInterface */
    function build() {
        return $this->buildFrom($this->uri);
    }

    /**
     * @param UriInterface $uri
     * @return UriInterface
     */
    function buildFrom(UriInterface $uri) {
        $flags = $this->projection;
        if (!empty($this->basePath)) {
            $path = $this->basePath;
            if (($flags & self::PATH) !== 0) {
                $path = rtrim($path, '/') . '/' . ltrim($uri->getPath(), '/');
            }
            $uri = $uri->withPath($path);
        } else if (($flags & self::PATH) === 0) {
            $uri = $uri->withPath('');
        }
        if (($flags & self::SCHEME) === 0) {
            $uri = $uri->withScheme('');
        }
        if (($flags & self::USER_INFO) === 0) {
            $uri = $uri->withUserInfo('');
        }
        if (($flags & self::HOST) === 0) {
            $uri = $uri->withHost('');
        }
        if (($flags & self::PORT) === 0) {
            $uri = $uri->withPort(null);
        }
        if (($flags & self::QUERY) === 0) {
            $uri = $uri->withQuery('');
        }
        if (($flags & self::FRAGMENT) === 0) {
            $uri = $uri->withFragment('');
        }
        return $uri;
    }

}
