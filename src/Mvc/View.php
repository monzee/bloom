<?php

namespace Codeia\Mvc;

use Psr\Http\Message\ResponseInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of View
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface View {

    /** @return ResponseInterface|null */
    function fold(ResponseInterface $r);
}
