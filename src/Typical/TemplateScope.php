<?php

namespace Codeia\Typical;

use stdclass;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of TemplateScope
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class TemplateScope {

    // disallow names starting with underscore
    const LEGAL_IDENTIFIER = '#^[[:alpha:]][_[:alnum:]]*#';

    private $delegate;
    private $raw = [];

    function __construct($delegate) {
        $this->delegate = is_object($delegate) ? $delegate : new stdclass;
    }

    function __call($method, array $args) {
        return call_user_func_array([$this->delegate, $method], $args);
    }

    function __get($name) {
        if (array_key_exists($name, $this->raw)) {
            return $this->raw[$name];
        }
        return null;
    }

    function render($__FILE, array $__LOCALS = []) {
        $this->raw = $__LOCALS;
        foreach ($__LOCALS as $k => $v) {
            if (preg_match(self::LEGAL_IDENTIFIER, $k)) {
                $$k = is_string($v) ? $this->e($v) : $v;
            }
        }
        include $__FILE;
    }

    function e($str) {
        if (method_exists($this->delegate, 'escape')) {
            return $this->delegate->escape($str);
        }
        return htmlentities($str, ENT_COMPAT, 'UTF-8');
    }

}
