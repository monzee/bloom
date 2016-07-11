<?php

namespace demo;

use Codeia\Mvc\Controller;
use Codeia\Typical\BaseUri;
use Codeia\Typical\CanGenerateUrls;
use Codeia\Typical\Template;
use Psr\Http\Message\ServerRequestInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Example of a controller that uses the default model and view.
 *
 * This renders the file `[page].phtml` where `page` is the value of
 * {@see Root::$template::$page}. It is set to `index` by default, thus this
 * class controls the content of the `index.phtml` template. The page can be
 * assigned manually (it is public) or automatically set to the first segment
 * of the url path by calling `$this->template->traverse($request)` in
 * dispatch().
 *
 * This flavor of MVC is similar to how other MVC frameworks render HTML pages.
 * This model is not recommended by the author except for really simple pages.
 * Use real domain objects and custom {@see Codeia\Mvc\View} implementations to
 * build your application. See the HelloWorld example for a better way to
 * structure an application.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 * @todo explain how templates are found and how to add to the search path
 */
class Root implements Controller {

    use CanGenerateUrls;

    private $template;

    function __construct(Template $t) {
        $this->template = $t;
    }

    function dispatch(ServerRequestInterface $r) {
        // this is important if your app is not located at the server's docroot
        $this->setBaseUri(BaseUri::fromRequest($r)->build());
        $this->template->bind('hello', $this->urlTo(new World()));
        $this->template->bind('fizz', $this->urlTo(new fizzbuzz\FizzBuzz()));
        $this->template->bind('foo', $this->urlTo(new Template('foo')));
        $this->template->bind('xss', '<script>alert("xss!")</script>');
    }

}
