<?php

namespace Codeia\Di;

use PHPUnit\Framework\TestCase;
use Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Unit and functional tests for AutoResolve.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class AutoResolveTest extends TestCase {

    private $sut;

    function setup() {
        $this->sut = new AutoResolve(
            new EmptyContainer(EmptyContainer::SHOULD_RETURN_NULL)
        );
    }

    function test_resolve_no_deps() {
        $class = Test\SubDep2::class;
        $this->assertInstanceOf($class, $this->sut->get($class));
    }

    function test_resolve_one_dep() {
        $root = Test\SubDep1::class;
        $dep = Test\SubDep2::class;
        $o = $this->sut->get($root);
        $this->assertInstanceOf($dep, $o->a);
    }

    function test_instances_are_scoped() {
        $foo = Test\Dep2::class;
        $bar = Test\SubDep1::class;
        $shared = Test\SubDep2::class;
        $a = $this->sut->get($foo);
        $b = $this->sut->get($bar);
        $c = $this->sut->get($shared);
        $this->assertSame($a->a, $c);
        $this->assertSame($b->a, $c);
    }

    function test_resolve_optional_param() {
        $a = $this->sut->get(Test\Root::class);
        $this->assertNotNull($a->c);
    }

    /** @expectedException \Interop\Container\Exception\NotFoundException */
    function test_throws_on_unloadable_class() {
        $this->sut->get('\foo\bar\baz\clzzz');
    }

    /** @expectedException \Interop\Container\Exception\NotFoundException */
    function test_throws_on_uninstantiable_service() {
        $this->sut->get(Test\AnInterface::class);
    }

    /** @expectedException \Interop\Container\Exception\NotFoundException */
    function test_throws_on_uninstantiable_dep() {
        $this->sut->get(Test\UsesAnInterface::class);
    }

    /** @expectedException \Interop\Container\Exception\NotFoundException */
    function test_throws_on_non_object_params() {
        $this->sut->get(Test\UsesSomething::class);
    }

    function test_uses_values_from_a_container() {
        $values = new MutableSandwich(new EmptyContainer);
        $auto = new AutoResolve($values);
        $foo = new \stdclass;
        $values->unshift('something', $foo);
        $fooUser = $auto->get(Test\UsesSomething::class);
        $this->assertSame($foo, $fooUser->thing);
    }
}
