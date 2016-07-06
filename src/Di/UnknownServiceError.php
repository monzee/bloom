<?php

namespace Codeia\Di;

use InvalidArgumentException;
use Interop\Container\Exception\NotFoundException;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of UnknownServiceError
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class UnknownServiceError extends InvalidArgumentException
    implements NotFoundException {

    function __construct($name, $code = 500) {
        parent::__construct("Unknown service: {$name}", $code);
    }

}
