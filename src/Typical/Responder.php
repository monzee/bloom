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
 * Default ResponseInterface emitter
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Responder implements View {

    function fold(ResponseInterface $r) {
        $version = 'BLOOM/' . Bloom::VERSION;
        $response = $r->withAddedHeader('X-Powered-By', $version);
        $this->emit($response);
    }

    private function emit(ResponseInterface $response) {
        $body = $response->getBody();
        $code = $response->getStatusCode();
        $status = $code . ' ' . $response->getReasonPhrase();
        if (headers_sent()) {
            error_log('headers already sent.');
        } else {
            header('HTTP/' . $response->getProtocolVersion() . ' ' . $status);
            foreach ($response->getHeaders() as $header => $values) {
                $k = urlencode($header);
                $v = implode(', ', $values);
                header("{$k}: {$v}");
            }
        }
        if ($body->getSize() === 0 && $code !== HttpState::STATUS_NO_CONTENT) {
            $body->write($status . "\n");
        }
        echo $body;
    }

    /**
     * Sends the headers and echoes the body.
     *
     * If the body has no content and the status is not 205 No Content, the
     * response reason phrase is written to the body then echo'd.
     *
     * @param ResponseInterface $response
     */
    static function send(ResponseInterface $response) {
        (new static)->fold($response);
    }
}
