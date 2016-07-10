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

    /**
     * Declares a function that calls a method on $scope or the current $this.
     *
     * Uses eval and globals, fun stuff. If this were rails, it would be called
     * 'elegant'.
     *
     * @global object $instance
     * @param string $name           Name of the method to import.
     * @param string|null $namespace The namespace to declare the function in.
     *                               Will use the global namespace if empty.
     * @param object|null $scope     Will use the object where this trait is
     *                               used if not specified.
     */
    function import($name, $namespace = null, $scope = null) {
        global $___instance;
        $___instance = $scope ?: $this;
        if (!function_exists($namespace . '\\' . $name)) {
            $fn = "function {$name}() {"
                . "global \$___instance;"
                . "return call_user_func_array("
                . "[\$___instance, '{$name}'], func_get_args());"
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

}
