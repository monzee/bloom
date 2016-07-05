<?php

namespace Codeia\Typical;

use Codeia\Bloom;
use Codeia\Mvc\View;
use Psr\Http\Message\ResponseInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Responder
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Responder implements View {

    function fold(ResponseInterface $r) {
        $version = 'BLOOM/' . Bloom::VERSION;
        $response = $r->withAddedHeader('X-Powered-By', $version);
        $this->emit($response);
        return $response;
    }

    private function emit(ResponseInterface $response) {
        if (headers_sent()) {
            error_log('headers already sent.');
        } else {
            header('HTTP/' . $response->getProtocolVersion()
                . ' ' . $response->getStatusCode()
                . ' ' . $response->getReasonPhrase());
            foreach ($response->getHeaders() as $header => $values) {
                $k = urlencode($header);
                $v = implode(', ', $values);
                header("{$k}: {$v}");
            }
        }
        echo $response->getBody();
    }

}
