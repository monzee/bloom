<?php

namespace Codeia\Typical;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Wrapper class for strings that shouldn't be escaped.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class RawText {

    private $text;

    /**
     * @param string $text
     */
    function __construct($text) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    function __toString() {
        return $this->text;
    }

}
