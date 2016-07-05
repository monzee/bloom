<?php

namespace demo;

use Codeia\Bloom;
use Codeia\Typical\CleanUrlApp;
use Codeia\Typical\Template;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

$assetExtensions = ['css', 'js', 'png', 'jpe?g', 'svg', 'txt', 'md'];
$exts = implode('|', $assetExtensions);
if (preg_match('/\.(?:' . $exts . ')(?:\?.*)?$/', $_SERVER['REQUEST_URI'])) {
    return false;
} else {
    chdir(__DIR__);
    require_once '../vendor/autoload.php';
    $app = new CleanUrlApp();
    $app->get(Template::class)->bind('url', $app->urlTo(new Template('foo')));
    Bloom::run($app);
}
