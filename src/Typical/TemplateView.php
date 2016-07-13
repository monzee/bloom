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

    private $model;
    private $status = 200;
    private $reason = 'Ok';
    private $type = 'text/html';

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
        $phtml = $this->model->page . '.phtml';
        $r->getBody()->write($this->render($phtml, $this->model->extras));
        return $r;
    }

}
