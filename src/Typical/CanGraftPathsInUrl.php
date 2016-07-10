<?php

namespace Codeia\Typical;

use Psr\Http\Message\UriInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Path manipulation trait for {@see Codeia\Mvc\Locatable} implementations.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanGraftPathsInUrl {

    /** @return UriInterface */
    function locate(UriInterface $uri) {
        $stock = $this->pathPrefix();
        $scion = $this->pathSuffix();
        $path = $uri->getPath();
        return $uri->withPath(
            array_reduce([$path, $scion], [$this, 'joinPaths'], $stock)
        );
    }

    /** @return string */
    protected function pathPrefix() {
        return '';
    }

    /** @return string */
    protected function pathSuffix() {
        return '';
    }

    private function joinPaths($left, $right) {
        if (empty($left)) {
            return $right;
        }
        if (empty($right)) {
            return $left;
        }
        $left = rtrim($left, '/');
        $right = ltrim($right, '/');
        return "{$left}/{$right}";
    }

}
