<?php

namespace Codeia\Di;

use PHPUnit\Framework\TestCase;
use Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * sempai tests
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class SempaiTest extends TestCase {

    function setup() {
        $this->con = new AutoResolve(new EmptyContainer);
        $this->shirou = new Sempai($this->con);
    }

    function test_returns_the_same_instance() {
        $aRock = new \stdclass;
        $this->assertSame($aRock, $this->shirou->inject($aRock));
    }

    function test_injects_public_props() {
        $rin = new Test\Tohsaka;
        $this->shirou->inject($rin);
        $this->assertNotNull($rin->tsun);
        $this->assertInstanceOf(Test\Emiya::class, $rin->tsun);
    }

    function test_does_not_inject_private_props() {
        $sakura = new Test\Sakura;
        $this->shirou->inject($sakura);
        $this->assertNull($sakura->target());
    }

    function test_does_not_inject_protected_props() {
        $ilya = new Test\Ilya;
        $this->shirou->inject($ilya);
        $this->assertNull($ilya->target());
    }

    function test_does_not_inject_undocumented_props() {
        $rin = new Test\Tohsaka;
        $sakura = new Test\Sakura;
        $ilya = new Test\Ilya;
        $this->shirou->inject($rin);
        $this->shirou->inject($sakura);
        $this->shirou->inject($ilya);
        $this->assertNull($rin->dere);
        $this->assertNull($sakura->other());
        $this->assertNull($ilya->other());
    }

    function test_does_not_inject_documented_but_unannotated_props() {
        $saber = new Test\Saber;
        $this->shirou->inject($saber);
        $this->assertNull($saber->master);
    }

    function test_injects_private_when_forced() {
        $sakura = new Test\Sakura;
        $this->shirou->inject($sakura, Sempai::NO);
        $this->assertNotNull($sakura->target());
        $this->assertInstanceOf(Test\Emiya::class, $sakura->target());
    }

    function test_injects_protected_when_forced() {
        $ilya = new Test\Ilya;
        $this->shirou->inject($ilya, Sempai::NO);
        $this->assertNotNull($ilya->target());
        $this->assertInstanceOf(Test\Emiya::class, $ilya->target());
    }

    function test_properly_detects_var_annotations() {
        $o = new Test\VarAnnotations;
        $this->shirou->inject($o);
        $this->assertNull($o->insideSingleQuotes);
        $this->assertNull($o->insideDoubleQuotes);
        $this->assertNull($o->insideBraces);
        $this->assertNull($o->atSignWasEscaped);
        $this->assertNotNull($o->leftSingleQuoteWasEscaped);
        $this->assertNotNull($o->leftDoubleQuoteWasEscaped);
        $this->assertNotNull($o->leftBraceWasEscaped);
        $this->assertNotNull($o->afterSqString);
        $this->assertNotNull($o->afterDqString);
        $this->assertNotNull($o->afterBlock);
    }

    /** @expectedException PHPUnit_Framework_Error_Notice */
    function test_sends_notice_when_class_is_not_resolvable() {
        $o = new Test\VarAnnotations;
        $this->shirou->inject($o, Sempai::NO);
    }

}
