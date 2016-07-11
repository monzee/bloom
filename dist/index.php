<?php

namespace demo;

use demo\fizzbuzz as fb;
use Codeia\Bloom;
use Codeia\Typical\CleanUrlApp;
use Codeia\Typical\RouteListBuilder;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

$assetExtensions = ['css', 'js', 'png', 'jpe?g', 'svg', 'txt', 'md'];
$exts = implode('|', $assetExtensions);
if (preg_match('/\.(?:' . $exts . ')(?:\?.*)?$/', $_SERVER['REQUEST_URI'])) {
    return false;
}

chdir(__DIR__);
require_once '../vendor/autoload.php';

Bloom::run(CleanUrlApp::autoResolve(), function (RouteListBuilder $on) {
    foreach (['', '/dist', '/dist/index.php'] as $basePath) {
        $on->stem($basePath)
            ->get('/', Root::class)
            ->get('/foo', CleanUrlApp::DEFAULT_ROUTE)
            ->get('/hello[/{name}]', [Hello::class, HelloView::class])
            ->get('/fizzbuzz[/{prev:\d+}]',
                [fb\FizzBuzzController::class, fb\FizzBuzzView::class]);
    }
});
