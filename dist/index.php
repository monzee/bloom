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
    $on->get('/', Root::class);
    $on->get('/foo', CleanUrlApp::DEFAULT_ROUTE);
    $on->get('/hello[/{name}]', [Hello::class, HelloView::class]);
    $on->get('/fizzbuzz[/{prev:\d+}]',
        [fb\FizzBuzzController::class, fb\FizzBuzzView::class]);
});
