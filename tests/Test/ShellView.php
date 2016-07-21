<?php

namespace Codeia\Test;

use Codeia\Mvc\View;
use Psr\Http\Message\StreamInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of ShellView
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class ShellView implements View {

    use \Codeia\Typical\CanContainValues;
    use \Codeia\Typical\CanWriteContent;
    use \Codeia\Typical\CanRenderTemplates;

    private $vars = [
        'title' => 'default title',
        'author' => 'default author',
        'content' => 'default content',
    ];

    function __construct() {
        $this->unshiftPath(__DIR__);
    }

    function write(StreamInterface $body) {
        $body->write($this->render('shell.phtml', $this->vars));
    }

    private $stack = [];

    private function willReceiveValues() {
        ob_start();
        $this->stack = [];
    }

    private function receive($key, $val) {
        $this->vars[$key] = $val;
    }

    private function receiveStart($data, $isSignificant = true) {
        if ($isSignificant) {
            $this->stack[] = $data;
            ob_start();
        }
    }

    private function receiveEnd($data, $isSignificant = true) {
        if (!empty($this->stack)) {
            $val = new \Codeia\Typical\RawText(ob_get_clean());
            $this->receive(array_pop($this->stack), $val);
        }
    }

    private function didReceiveValues() {
        while (!empty($this->stack)) {
            $this->receiveEnd(null);
        }
        ob_end_clean();
    }
}
