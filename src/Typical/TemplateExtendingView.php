<?php

namespace Codeia\Typical;

use Psr\Http\Message\ResponseInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Allows rendering of other templates by calling extend().
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class TemplateExtendingView extends TemplateView {

    private $superTemplate;
    private $extender;

    function extend($name, callable $gen) {
        $this->superTemplate = $name;
        $this->extender = $gen;
    }

    function fold(ResponseInterface $res) {
        $response = parent::fold($res);
        if (empty($this->superTemplate)) {
            return $response;
        }
        $saved = $this->model;
        $this->model = new Template($this->superTemplate);
        foreach ($saved->extras as $key => $val) {
            $this->model->bind($key, $val);
        }
        $this->accept($this->extender);
        $response = parent::fold($res);
        $this->model = $saved;
        return $response;
    }
}
