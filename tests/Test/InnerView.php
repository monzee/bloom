<?php

namespace Codeia\Test;

use Codeia\Mvc\View;
use Psr\Http\Message\StreamInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of InnerView
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class InnerView implements View {

    use \Codeia\Typical\CanWriteContent;

    function write(StreamInterface $body) {
        $shell = new ShellView();
        $shell->accept($this);
        $shell->write($body);
    }

    function __invoke($start, $end) {
        yield 'title' => 'the title';
        yield 'author' => 'me';
        yield 'content' => $start;
        echo
<<< BODY
Lorem ipsum dolor sit
         amet; the quick brown
         for jumps over the
         lazy dog.
BODY;
        yield $end;
    }

}
