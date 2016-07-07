<?php

namespace Codeia;

use Codeia\Mvc\EntryPoint;
use Codeia\Mvc\View;
use Codeia\Typical\RouteListBuilder;
use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Root runner. You'll probably want to use this in index.php.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
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
     * @param callable $routing  Function of type RouteListBuilder -> (). The
     *                           context should be able to provide a
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

}
