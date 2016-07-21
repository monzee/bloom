<?php

namespace Codeia\Typical;

use PHPUnit\Framework\TestCase;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Unit and functional tests for TemplateExtendingView.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class TemplateExtendingViewTest extends TestCase {

    private function render(TemplateView $view) {
        $res = new \GuzzleHttp\Psr7\Response;
        return (string) $view->fold($res)->getBody();
    }

    private function view(Template $tpl) {
        $view = new TemplateExtendingView($tpl);
        $view->unshiftPath(__DIR__ . '/../Test');
        return $view;
    }

    function test_calling_extend_in_phtml() {
        $tpl = new Template('child');
        $s = $this->render($this->view($tpl));
        self::assertContains('title: none', $s);
        self::assertContains('captured from child', $s);
    }

    function test_extended_template_gets_values_from_child_model() {
        $tpl = new Template('child');
        $tpl->bind('title', 'i did not set this during extension');
        $s = $this->render($this->view($tpl));
        self::assertContains('title: i did not set this during extension', $s);
    }

    function test_extended_vars_override_the_default_vars() {
        $tpl = new Template('child');
        $tpl->bind('author', 'i did not set this during extension');
        $s = $this->render($this->view($tpl));
        self::assertNotContains('author: i did not set this during extension', $s);
        self::assertContains('author: overridden', $s);
    }

}
