<?php

namespace Codeia\Typical;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of CanWriteContent
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanWriteContent {

    /**
     * @param StreamInterface $body
     */
    abstract function write(StreamInterface $body);

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface|null
     */
    function willWrite(ResponseInterface $response) {

    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface|null
     */
    function didWrite(ResponseInterface $response) {

    }

    function fold(ResponseInterface $response) {
        $response = $this->willWrite($response) ?: $response;
        $this->write($response->getBody());
        return $this->didWrite($response) ?: $response;
    }
}
