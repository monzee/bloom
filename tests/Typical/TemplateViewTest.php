<?php

namespace Codeia\Typical;

use PHPUnit\Framework\TestCase;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Unit and functional tests for TemplateView.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class TemplateViewTest extends TestCase {

    private function render(TemplateView $view) {
        $res = new \GuzzleHttp\Psr7\Response;
        return (string) $view->fold($res)->getBody();
    }

    private function view(Template $tpl) {
        $view = new TemplateView($tpl);
        $view->unshiftPath(__DIR__ . '/../Test');
        return $view;
    }

    function test_set_variables_through_template() {
        $tpl = new Template('shell');
        $tpl->bind('title', 'abc');
        $tpl->bind('author', '123');
        $tpl->bind('content', '!@#');
        $s = $this->render($this->view($tpl));
        self::assertContains('title: abc', $s);
        self::assertContains('author: 123', $s);
        self::assertContains('content: !@#', $s);
    }

    function test_set_variables_through_generator() {
        $view = $this->view(new Template('shell'));
        $view->accept(function () {
            yield 'title' => 'zxc';
            yield 'author' => 'qwe';
            yield 'content' => 'asd';
        });
        $s = $this->render($view);
        self::assertContains('title: zxc', $s);
        self::assertContains('author: qwe', $s);
        self::assertContains('content: asd', $s);
    }

    function test_yield_end_is_optional() {
        $view = $this->view(new Template('shell'));
        $view->accept(function ($capture) {
            yield 'title' => $capture;
            echo 'zxc';
            yield 'author' => $capture;
            echo 'qwe';
            yield 'content' => $capture;
            echo 'asd';
        });
        $s = $this->render($view);
        self::assertContains('title: zxc', $s);
        self::assertContains('author: qwe', $s);
        self::assertContains('content: asd', $s);
    }

    function test_set_variables_through_array_returned_by_callable() {
        $view = $this->view(new Template('shell'));
        $view->accept(function () {
            return [
                'title' => 'zxc',
                'author' => 'qwe',
                'content' => 'asd',
            ];
        });
        $s = $this->render($view);
        self::assertContains('title: zxc', $s);
        self::assertContains('author: qwe', $s);
        self::assertContains('content: asd', $s);
    }

    function test_generator_mutates_the_template() {
        $tpl = new Template('shell');
        $view = $this->view($tpl);
        $content = new \stdclass;
        $view->accept(function () use ($content) {
            yield 'title' => 'zxc';
            yield 'author' => 12345;
            yield 'content' => $content;
        });
        $vars = $tpl->extras;
        self::assertEquals('zxc', $vars['title']);
        self::assertEquals(12345, $vars['author']);
        self::assertThat($vars['author'], self::isType('integer'));
        self::assertSame($content, $vars['content']);
    }

    /** @depends test_generator_mutates_the_template */
    function test_captured_text_is_wrapped() {
        $tpl = new Template('shell');
        $view = $this->view($tpl);
        $sentence = 'The quick brown fox jumps over the lazy dog.';
        $view->accept(function ($start, $end) use ($sentence) {
            yield 'title' => $start;
            echo $sentence;
            yield $end;
        });
        $vars = $tpl->extras;
        self::assertInstanceOf(RawText::class, $vars['title']);
        self::assertEquals($sentence, (string) $vars['title']);
    }

}
