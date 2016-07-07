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
class TemplateBasedView implements View {

    use CanGenerateUrls;
    use CanRenderTemplates;

    private $model;

    function __construct(Template $m) {
        $this->model = $m;
    }

    function fold(ResponseInterface $r) {
        $r = $r->withStatus(200)->withHeader('content-type', 'text/html');
        $phtml = $this->model->page . '.phtml';
        $r->getBody()->write($this->render($phtml, $this->model->extras));
        return $r;
    }

}
