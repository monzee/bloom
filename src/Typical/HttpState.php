<?php

namespace Codeia\Typical;

use Psr\Http\Message\ServerRequestInterface as Request;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Aggregates bits of information from the request object.
 *
 * This class is read-only. The only way to affect its values is through
 * setting various request attributes.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class HttpState {

    const TYPE = 'targetType';
    const STATUS = 'targetStatus';
    const SUPPORTED = 'supportedMethods';

    const STATUS_OK = '200 Ok';
    const STATUS_CREATED = '201 Created';
    const STATUS_NO_CONTENT = '204 No Content';
    const STATUS_FOUND = '302 Found';
    /** Prefer this over 302, but they are practically the same */
    const STATUS_REDIRECT = '303 See Other';
    const STATUS_SEE_OTHER = '303 See Other';
    const STATUS_NOT_MODIFIED = '304 Not Modified';
    /** Use this to redirect with the same request method */
    const STATUS_MOVED = '307 Temporary Redirect';
    const STATUS_BAD_REQUEST = '400 Bad Request';
    const STATUS_UNAUTHORIZED = '401 Unauthorized';
    const STATUS_DENIED = '403 Forbidden';
    const STATUS_FORBIDDEN = '403 Forbidden';
    const STATUS_NOT_FOUND = '404 Not Found';
    const STATUS_BAD_METHOD = '405 Method Not Allowed';
    const STATUS_ERROR = '500 Internal Server Error';

    const TYPE_HTML = 'text/html';
    const TYPE_JSON = 'application/json';
    const TYPE_XML = 'text/xml';

    private $baseUri;
    private $contentType;
    private $status;
    private $supported;

    function __construct(Request $r) {
        $basePath = $r->getAttribute(BaseUri::KEY, '/');
        $this->baseUri = new BaseUri($basePath, $r->getUri());
        $this->contentType = $r->getAttribute(self::TYPE, self::TYPE_HTML);
        list($this->status, $this->reason) = $this->normalizeStatusLine(
            $r->getAttribute(self::STATUS, self::STATUS_OK)
        );
        $this->supported = $r->getAttribute(self::SUPPORTED, [$r->getMethod()]);
    }

    /** @return BaseUri */
    function baseUri() {
        return $this->baseUri;
    }

    /** @return string */
    function contentType() {
        return $this->contentType;
    }

    /** @return array Pair of int*string corresponding to status and reason */
    function status() {
        return [$this->status, $this->reason];
    }

    /** @return array String HTTP method names */
    function methods() {
        return $this->supported;
    }

    private function normalizeStatusLine($status) {
        $s = trim($status);
        list($code, $reason) = explode(' ', $s, 2);
        if (!is_numeric($code)) {
            return [200, $s];
        }
        return [$code, $reason];
    }

}
