<?php

namespace Codeia;

use Interop\Container\ContainerInterface;
use Codeia\Mvc\EntryPoint;
use Codeia\Mvc\View;
use Codeia\Typical\RouteListBuilder;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Bloom
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Bloom {

    const VERSION = '0.2.0-dev';

    /**
     * @param ContainerInterface $context
     * @param callable $routing RouteListBuilder -> ()
     */
    static function run(ContainerInterface $context, callable $routing = null) {
        if (!empty($routing)) {
            $routing($context->get(RouteListBuilder::class));
        }
        $front = $context->get(EntryPoint::class);
        $context->get(View::class)->fold($front->main($context));
    }

}
