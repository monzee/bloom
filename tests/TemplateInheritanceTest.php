<?php

namespace Codeia;

use PHPUnit\Framework\TestCase;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Unit and functional tests for TemplateInheritance.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class TemplateInheritanceTest extends TestCase {

    function setup() {
    }

    function render(Mvc\View $view) {
        $res = new \GuzzleHttp\Psr7\Response;
        $res = $view->fold($res) ?: $res;
        return (string) $res->getBody();
    }

    function test_just_the_shell() {
        $s = $this->render(new Test\ShellView);
        self::assertContains('title: default title', $s);
        self::assertContains('author: default author', $s);
        self::assertContains('content: default content', $s);
    }

    /** @depends test_just_the_shell */
    function test_inner_view_overrides_the_shell() {
        $s = $this->render(new Test\InnerView);
        self::assertContains('title:', $s);
        self::assertContains('author:', $s);
        self::assertContains('content:', $s);
        self::assertNotContains('default title', $s);
        self::assertNotContains('default author', $s);
        self::assertNotContains('default content', $s);
    }

}
