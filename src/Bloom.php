<?php

namespace Codeia;

use Codeia\Mvc\EntryPoint;
use Codeia\Mvc\View;
use Codeia\Typical\RouteListBuilder;
use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Root runner. You'll probably want to use this in index.php.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 * @deprecated Use Codeia\Integrations classes for now; will probably build a
 * facade and bring this class back later when the api has solidified.
 */
class Bloom {

    const VERSION = '0.2.0-dev';

    /**
     * Runs the Codeia\Mvc\EntryPoint service in the container.
     *
     * A null return value signifies that a response has been sent to the client
     * and no further processing needs to be done by the caller. If this returns
     * a ResponseInterface instance, the caller will need to pass it to a
     * response emitter.
     *
     * @param Container $context Expected to have the services
     *                           {@see EntryPoint}, {@see View},
     *                           {@see ResponseInterface} and optionally
     *                           {@see RouteListBuilder}.
     * @param callable $routing  Function of type RouteListBuilder -> (). Will
     *                           be ignored if the context does not provide a
     *                           {@see RouteListBuilder}.
     * @return Response|null
     */
    static function run(Container $context, callable $routing = null) {
        if (!empty($routing) && $context->has(RouteListBuilder::class)) {
            $routing($context->get(RouteListBuilder::class));
        }
        $front = $context->get(EntryPoint::class);
        $appResult = $front->main($context) ?: $context->get(Response::class);
        return $context->get(View::class)->fold($appResult);
    }

    /**
     * Runs an MVC unit.
     *
     * The premise here is that the controller and view have a common dependency
     * (maybe multiple) that is mutated first by the controller and then used by
     * the view to generate a response. The common dependencies are application
     * models. They are not domain models, although they are free to have (and
     * probably do have) domain model dependencies.
     *
     * @param Container $context      This container should be able to provide
     *                                every thing needed transitively by the
     *                                controller and view.
     * @param string $controllerClass
     * @param string $viewClass
     * @return Response|null
     */
    static function test(Container $context, $controllerClass, $viewClass) {
        $controller = $context->get($controllerClass);
        $view = $context->get($viewClass);
        $controller->dispatch($context->get(Request::class));
        return $view->fold($context->get(Response::class));
    }

}
