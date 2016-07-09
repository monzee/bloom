<?php

namespace Codeia\Typical;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Uri;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Unit and functional tests for BaseUri.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class BaseUriTest extends TestCase {

    private $uri;

    function setup() {
        $this->uri = new Uri('http://z@x:foobar.kek:3214/a/b/c?d=e&f=g#h');
    }

    private function addRemove($with, $without) {
        return (new BaseUri)->with($with)->without($without)->buildFrom($this->uri);
    }

    private function removeAdd($without, $with) {
        return (new BaseUri)->without($without)->with($with)->buildFrom($this->uri);
    }

    function test_remove_scheme() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::SCHEME);
        $this->assertEquals('//z@x:foobar.kek:3214/a/b/c?d=e&f=g#h', (string) $uri);
    }

    function test_remove_host() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::HOST);
        $this->assertEquals('http:/a/b/c?d=e&f=g#h', (string) $uri);
    }

    function test_remove_port() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::PORT);
        $this->assertEquals('http://z@x:foobar.kek/a/b/c?d=e&f=g#h', (string) $uri);
    }

    function test_remove_path() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::PATH);
        $this->assertEquals('http://z@x:foobar.kek:3214?d=e&f=g#h', (string) $uri);
    }

    function test_remove_query() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::QUERY);
        $this->assertEquals('http://z@x:foobar.kek:3214/a/b/c#h', (string) $uri);
    }

    function test_remove_fragment() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::FRAGMENT);
        $this->assertEquals('http://z@x:foobar.kek:3214/a/b/c?d=e&f=g', (string) $uri);
    }

    function test_remove_scheme_and_host() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::SCHEME | BaseUri::HOST);
        $this->assertEquals('/a/b/c?d=e&f=g#h', (string) $uri);
    }

    function test_remove_host_and_path() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::HOST | BaseUri::PATH);
        $this->assertEquals('http:?d=e&f=g#h', (string) $uri);
    }

    function test_remove_path_query_and_fragment() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::FRAGMENT | BaseUri::PATH | BaseUri::QUERY);
        $this->assertEquals('http://z@x:foobar.kek:3214', (string) $uri);
    }

    function test_remove_authority() {
        $uri = $this->addRemove(BaseUri::ALL, BaseUri::AUTHORITY);
        $this->assertEquals('http:/a/b/c?d=e&f=g#h', (string) $uri);
    }

    function test_only_scheme_and_authority() {
        $uri = $this->removeAdd(BaseUri::ALL, BaseUri::AUTHORITY | BaseUri::SCHEME);
        $this->AssertEquals('http://z@x:foobar.kek:3214', (string) $uri);
    }

    function test_only_resource() {
        $uri = $this->removeAdd(BaseUri::ALL, BaseUri::RESOURCE);
        $this->assertEquals('/a/b/c?d=e&f=g#h', (string) $uri);
    }

    function test_base_path_is_added_before_the_existing_path() {
        $uri = (new BaseUri('lul'))
            ->without(BaseUri::ALL)
            ->with(BaseUri::PATH)
            ->buildFrom($this->uri);
        $this->assertEquals('lul/a/b/c', (string) $uri);
    }

    function test_default_includes_only_the_base_path() {
        $uri = (new BaseUri('lul'))->buildFrom($this->uri);
        $this->assertEquals('lul', (string) $uri);
    }

    function test_default_is_empty_url_if_base_path_is_empty() {
        $uri = (new BaseUri)->buildFrom($this->uri);
        $this->assertEquals('', (string) $uri);
    }

    function test_trailing_slashes_are_removed_before_joining_paths() {
        $uri = (new BaseUri('/lul//'))
            ->with(BaseUri::PATH)
            ->buildFrom($this->uri);
        $this->assertEquals('/lul/a/b/c', (string) $uri);
    }

    function test_base_slashes_are_untouched_if_path_is_not_included() {
        $uri = (new BaseUri('/lul//'))
            ->with(BaseUri::SCHEME | BaseUri::FRAGMENT)
            ->buildFrom($this->uri);
        $this->assertEquals('http:/lul//#h', (string) $uri);
    }

    function test_base_path_with_some_other_parts() {
        $uri = (new BaseUri('/lul'))
            ->with(BaseUri::RESOURCE)
            ->buildFrom($this->uri);
        $this->assertEquals('/lul/a/b/c?d=e&f=g#h', (string) $uri);
    }
}
