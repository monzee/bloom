<?php

namespace Codeia\Typical;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Thrown when a trait is asked to render a file that is not in the path.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class TemplateNotFoundError extends \UnexpectedValueException {

    /**
     * @param string $name The name of the template
     * @param array $paths Strings
     */
    function __construct($name, array $paths) {
        $ps = implode(', ', $paths);
        parent::__construct("Cannot find `${name}` in [{$ps}]", 404);
    }

}
