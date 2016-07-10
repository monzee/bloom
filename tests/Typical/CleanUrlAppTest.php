<?php

namespace Codeia\Typical;

use PHPUnit\Framework\TestCase;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Unit and functional tests for CleanUrlApp.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class CleanUrlAppTest extends TestCase {

    private $sut;

    function setup() {
        $this->sut = new CleanUrlApp();
    }

    function test_default_views_are_scoped() {
        list(, $viewClass) = CleanUrlApp::DEFAULT_ROUTE;
        $a = $this->sut->get($viewClass);
        $b = $this->sut->get($viewClass);
        self::assertSame($a, $b);
    }

    function test_default_controllers_are_scoped() {
        list($ctrlrClass) = CleanUrlApp::DEFAULT_ROUTE;
        $a = $this->sut->get($ctrlrClass);
        $b = $this->sut->get($ctrlrClass);
        self::assertSame($a, $b);
    }
}
