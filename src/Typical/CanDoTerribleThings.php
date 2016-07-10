<?php

namespace Codeia\Typical;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Convenient nightmares.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanDoTerribleThings {

    static $REGISTRY = [];

    /**
     * Declares a function that calls a method on $scope or the current $this.
     *
     * Uses eval and globals, fun stuff. If this were rails, it would be called
     * 'elegant'.
     *
     * @param string $name           Name of the method to import.
     * @param string|null $namespace The namespace to declare the function in.
     *                               Will use the global namespace if empty.
     * @param object|null $scope     Will use the object where this trait is
     *                               used if not specified.
     */
    function import($name, $namespace = null, $scope = null) {
        self::namespaceEval($name, $namespace, $scope ?: $this);
    }

    /**
     * Sets the object to send messages to from the generated functions.
     *
     * @param string $namespace
     * @param mixed $instance
     */
    function bindScope($namespace = null, $instance = null) {
        $ns = self::normalizeNamespace($namespace);
        CanDoTerribleThings::$REGISTRY[$ns] = $instance ?: $this;
    }

    private static function namespaceEval(
        $name, $namespace = null, $scope = null
    ) {
        $ns = self::normalizeNamespace($namespace);
        CanDoTerribleThings::$REGISTRY[$ns] = $scope ?: __CLASS__;
        $reg = '\\' . __TRAIT__ . '::$REGISTRY';
        if (!function_exists($namespace . '\\' . $name)) {
            $fn = "function {$name}() {"
                . "return call_user_func_array("
                . "[{$reg}['{$ns}'], '{$name}'], func_get_args());"
                . "}";
            if (!empty($namespace)) {
                eval("namespace {$namespace} { {$fn} }");
            } else {
                eval($fn);
            }
        } else {
            error_log("function `{$name}` already declared.");
        }
    }

    private static function normalizeNamespace($namespace) {
        return !empty($namespace) ? strtolower($namespace) : '%%root%%';
    }
}
