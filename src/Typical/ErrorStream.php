<?php

namespace Codeia\Typical;

use Psr\Http\Message\StreamInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Decorates a {@see StreamInterface}, attaches an exception.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class ErrorStream implements StreamInterface {

    private $wrapped;
    private $exception;

    function __construct(StreamInterface $s, \Exception $e) {
        $this->wrapped = $s;
        $this->exception = $e;
    }

    /** @return \Exception */
    function error() {
        return $this->exception;
    }

    function __toString() {
        return $this->wrapped->__toString();
    }

    function close() {
        return $this->wrapped->close();
    }

    public function detach() {
        return $this->wrapped->detach();
    }

    public function eof() {
        return $this->wrapped->eof();
    }

    public function getContents() {
        return $this->wrapped->getContents();
    }

    public function getMetadata($key = null) {
        return $this->wrapped->getMetadata($key);
    }

    public function getSize() {
        return $this->wrapped->getSize();
    }

    public function isReadable() {
        return $this->wrapped->isReadable();
    }

    public function isSeekable() {
        return $this->wrapped->isSeekable();
    }

    public function isWritable() {
        return $this->wrapped->isWritable();
    }

    public function read($length) {
        return $this->wrapped->read($length);
    }

    public function rewind() {
        return $this->wrapped->rewind();
    }

    public function seek($offset, $whence = SEEK_SET) {
        return $this->wrapped->seek($offset, $whence);
    }

    public function tell() {
        return $this->wrapped->tell();
    }

    public function write($string) {
        return $this->wrapped->write($string);
    }

}
