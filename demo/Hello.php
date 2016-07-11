<?php

namespace demo;

use Codeia\Mvc\Controller;
use Codeia\Mvc\View;
use Codeia\Typical\HttpState;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface as Stream;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Hello world example that doesn't use the default view.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Hello implements Controller {

    private $world;

    function __construct(World $model) {
        $this->world = $model;
    }

    function dispatch(Request $request) {
        $this->world->greet($request->getAttribute('name'));
    }

}

/**
 * This really should be in its own file, but meh.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class HelloView implements View {

    use \Codeia\Typical\CanGenerateUrls;
    use \Codeia\Typical\CanRenderTemplates;
    use \Codeia\Typical\CanWriteContent;

    private $world;

    function __construct(World $model, HttpState $http) {
        $this->world = $model;
        $this->setBaseUri($http->baseUri()->build());
    }

    function write(Stream $body) {
        $this->pushPath(__DIR__);
        $body->write($this->render('hello.phtml', [
            'name' => $this->world->name(),
            'somebody' => $this->world->randomPerson(),
        ]));
    }

}
