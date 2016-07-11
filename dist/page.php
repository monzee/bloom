<?php

namespace Codeia;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Integrations\GuzzleFastRoute();

$app(function ($on, $default) {
    $on->stem('/dist/page.php')
        ->get('/', \demo\Root::class)
        ->get('/hello[/{name}]', [\demo\Hello::class, \demo\HelloView::class])
        ->get('/fox', $default);
});

(new Typical\Responder)->fold($app->main(new Di\EmptyContainer));