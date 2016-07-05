<?php

namespace Codeia\Typical;

use InvalidArgumentException;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of CanRenderTemplates
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanRenderTemplates {

    private $_templatePaths = ['.'];

    function render($file, array $vars = []) {
        $path = $this->pathTo($file);
        if ($path !== null) {
            $tpl = new TemplateScope($this);
            ob_start();
            $tpl->render($path, $vars);
            return ob_get_clean();
        }
        throw new InvalidArgumentException("template {$file} not found.");
    }

    private function pathTo($file) {
        $stem = ltrim($file, '\\/');
        foreach ($this->_templatePaths as $prefix) {
            $base = rtrim($prefix, '\\/');
            $filename = "{$base}/{$stem}";
            $path = stream_resolve_include_path($filename);
            if (false !== $path) {
                return $path;
            }
        }
        return null;
    }

}
