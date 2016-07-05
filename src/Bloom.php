<?php

namespace Codeia;

use Interop\Container\ContainerInterface;
use Codeia\Mvc\EntryPoint;
use Codeia\Mvc\View;

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

    static function run(ContainerInterface $context) {
        $front = $context->get(EntryPoint::class);
        $context->get(View::class)->fold($front->main($context));
    }

}
