<?php

namespace Codeia\Bloom;

use Codeia\Di\AutoResolve;
use Codeia\Di\EmptyContainer;
use Codeia\Mvc\EntryPoint;
use Codeia\Typical\Responder;
use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Runner facade
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
final class Seed {

    const EMIT_IF_NEEDED = true;

    private $scope;

    function __construct(ContainerInterface $context) {
        $this->scope = $context;
    }

    /**
     * Runs the main method of an {@see EntryPoint}.
     *
     * @param EntryPoint $app
     * @param bool $emit
     * @return Psr\Http\Message\ResponseInterface|null
     */
    function run(EntryPoint $app, $emit = false) {
        $response = $app->main($this->scope);
        if ($emit && $response !== null) {
            Responder::send($response);
        }
        return $response;
    }

    /**
     * Takes a list of service names and runs them all in the same context.
     *
     * When an {@see EntryPoint} returns a null response, the run sequence is
     * stopped. If the last service returns a non-null response, it is emitted
     * by {@see Responder}.
     *
     * The context should be able to resolve every service name.
     *
     * @param string $varargServices Names of EntryPoints to run in sequence
     */
    function seq($varargServices) {
        $services = func_get_args();
        $response = null;
        foreach ($services as $s) {
            $response = $this->run($this->scope->get($s), false);
            if ($response === null) {
                break;
            }
        }
        if ($response !== null) {
            Responder::send($response);
        }
    }

    /**
     * Returns a runner with a clean context.
     *
     * If the application does not provide its own set of services, you'll
     * likely get a runtime exception.
     *
     * @return self
     */
    static function noContext() {
        return new self(new EmptyContainer());
    }

    /**
     * Wraps the context with {@see AutoResolve} and returns a runner.
     *
     * Note that wrapping a container with AutoResolve may interfere with an
     * {@see EntryPoint}'s own service resolution.
     *
     * @param ContainerInterface $context
     * @return self
     */
    static function auto(ContainerInterface $context) {
        return new self(new AutoResolve($context));
    }

}
