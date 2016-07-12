<?php

namespace demo;

use Codeia\Di;
use Codeia\Integrations;
use Codeia\Typical;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Integrations\GuzzleFastRoute(function ($on, $default) {
    foreach (['/page.php', '/dist/page.php'] as $prefix) {
        $on->stem($prefix)
            ->get('/', Root::class)
            ->get('/hello[/{name}]', [Hello::class, HelloView::class])
            ->get('/fizzbuzz[/{prev:\d+}]', [
                fizzbuzz\FizzBuzzController::class,
                fizzbuzz\FizzBuzzView::class
            ])
            ->get('/foo', $default);
    }
});

if (null !== ($response = $app->main(new Di\EmptyContainer))) {
    $body = $response->getBody();
    if ($body->getSize() === 0) {
        $body->write($response->getReasonPhrase() . "\n");
    }
    (new Typical\Responder)->fold($response);
}