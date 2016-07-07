<?php
/** @deprecated please ignore this */

namespace scratch;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UriInterface as Uri;

interface EntryPoint {

    /** @return Response */
    function main(ContainerInterface $c);
}

interface FrontController extends EntryPoint {

    function setRoute($controllerClass, $viewClass);
}

interface Controller {

    /** @return Request|null */
    function dispatch(Request $r);
}

interface View {

    /** @return Response|null */
    function fold(Response $r);
}

interface Routable {

    /** @return Request */
    function traverse(Request $r);

    /** @return Uri */
    function locate(Uri $uri);
}
