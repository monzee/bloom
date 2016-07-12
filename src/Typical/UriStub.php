<?php

namespace Codeia\Typical;

use Psr\Http\Message\UriInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Made this so I can remove the concrete dependency in the url gen trait.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class UriStub implements UriInterface {

    private $path;
    private $caller;

    function __construct($class, $method) {
        $this->caller = "{$class}::{$method}()";
    }

    function __toString() {
        return $this->path;
    }

    function getAuthority() {
        return '';
    }

    function getFragment() {
        return '';
    }

    function getHost() {
        return '';
    }

    function getPath() {
        return $this->path;
    }

    function getPort() {
        return null;
    }

    function getQuery() {
        return '';
    }

    function getScheme() {
        return '';
    }

    function getUserInfo() {
        return '';
    }

    function withFragment($fragment) {
        $this->error();
    }

    function withHost($host) {
        $this->error();
    }

    function withPath($path) {
        $this->path = $path;
        return $this;
    }

    function withPort($port) {
        $this->error();
    }

    function withQuery($query) {
        $this->error();
    }

    function withScheme($scheme) {
        $this->error();
    }

    function withUserInfo($user, $password = null) {
        $this->error();
    }

    private function error() {
        $msg = 'This is a stub that only sets/returns paths. Please use a real '
            . 'UriInterface implementation. This instance was constructed at '
            . $this->caller;
        throw new \BadMethodCallException($msg, 500);
    }
}
