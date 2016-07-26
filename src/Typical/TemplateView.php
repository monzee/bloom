<?php

namespace Codeia\Typical;

use Codeia\Mvc\View;
use Psr\Http\Message\ResponseInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of TemplateBasedView
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class TemplateView implements View {

    use CanGenerateUrls;
    use CanRenderTemplates;
    use CanDoTerribleThings;
    use CanContainValues;

    protected $model;
    protected $status = 200;
    protected $reason = 'Ok';
    protected $type = 'text/html';
    private $captureStack;


    function __construct(Template $m, HttpState $http = null) {
        $this->model = $m;
        if ($http) {
            $this->setBaseUri($http->baseUri()->build());
            $this->type = $http->contentType();
            list($this->status, $this->reason) = $http->status();
        }
    }

    function fold(ResponseInterface $r) {
        $r = $r
            ->withStatus($this->status, $this->reason)
            ->withHeader('Content-Type', $this->type);
        $phtml = $this->getTemplateFile($this->model->page);
        $this->setTemplateDelegate($this->model->delegate);
        $r->getBody()->write($this->render($phtml, $this->model->extras));
        return $r;
    }

    protected function setTemplateDelegate($receiver) {
        $this->_secondaryDelegate = $receiver;
    }

    protected function getTemplateFile($name) {
        return $name . '.phtml';
    }

    protected function willReceiveValues() {
        $this->captureStack = [];
        ob_start();
    }

    protected function receive($key, $value) {
        $this->model->bind($key, $value);
    }

    protected function receiveStart($data, $isSignificant = true) {
        if ($isSignificant) {
            $this->captureStack[] = (string) $data;
            ob_start();
        }
    }

    protected function receiveEnd($data, $isSignificant = true) {
        if (!empty($this->captureStack)) {
            $key = array_pop($this->captureStack);
            $val = new RawText(ob_get_clean());
            $this->receive($key, $val);
        }
    }

    protected function didReceiveValues() {
        for ($i = count($this->captureStack); $i > 0; $i--) {
            $this->receiveEnd(null);
        }
        ob_end_clean();
    }
}
