<?php

namespace Codeia\Typical;

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
    protected $_templateCallReceiver;

    function pushPath($path) {
        $this->_templatePaths[] = $path;
    }

    function unshiftPath($path) {
        array_unshift($this->_templatePaths, $path);
    }

    function templateScope() {
        return new TemplateScope($this->_templateCallReceiver ?: $this);
    }

    function render($file, array $vars = []) {
        $path = $this->pathTo($file);
        if ($path !== null) {
            ob_start();
            $this->templateScope()->render($path, $vars);
            return ob_get_clean();
        }
        throw new TemplateNotFoundError($file, $this->_templatePaths);
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
