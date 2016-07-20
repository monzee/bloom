<?php

namespace demo;

use Codeia\Bloom\Seed;
use Codeia\Bloom\GuzzleFastRoute;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = new GuzzleFastRoute(function ($on, $default) {
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

Seed::noContext()->run($app, Seed::EMIT_IF_NEEDED);