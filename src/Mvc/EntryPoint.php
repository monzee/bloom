<?php

namespace Codeia\Mvc;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of EntryPoint
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface EntryPoint {

    /** @return ResponseInterface */
    function main(ContainerInterface $c);
}
