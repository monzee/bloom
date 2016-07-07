<?php

namespace demo\fizzbuzz;

use Codeia\Mvc\View;
use Psr\Http\Message\StreamInterface as Stream;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Renders a FizzBuzz.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class FizzBuzzView implements View {

    use \Codeia\Typical\CanGenerateUrls;
    use \Codeia\Typical\CanRenderTemplates;
    use \Codeia\Typical\CanWriteContent;

    private $fizzbuzz;

    function __construct(FizzBuzz $model) {
        $this->fizzbuzz = $model;
    }

    function write(Stream $body) {
        $this->pushPath(__DIR__);
        $body->write($this->render('fizzbuzz.phtml', [
            'next' => $this->fizzbuzz->next(),
            'text' => $this->fizzbuzz->text(),
        ]));
    }

}
