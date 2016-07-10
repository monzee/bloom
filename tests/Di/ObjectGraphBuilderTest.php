<?php

namespace Codeia\Di;

use PHPUnit\Framework\TestCase;
use Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Unit and functional tests for ObjectGraphBuilder.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class ObjectGraphBuilderTest extends TestCase {

    use \Codeia\Typical\CanDoTerribleThings;

    const SCOPED = ObjectGraphBuilder::SCOPED;

    private $sut;

    public static function setUpBeforeClass() {
        foreach ([
            'assertNotNull', 'assertNull', 'assertSame', 'assertNotSame',
            'assertInstanceOf',
        ] as $m) {
            self::namespaceEval($m, __NAMESPACE__);
        }
    }

    /*
     * Root -----> Dep1 -----> SubDep1 <-,
     *    |`-----> Dep2 --------. |      |
     *    `------> Dep3 -----.  v v      |
     *                       | SubDep2   |
     *                       `-----------'
     */

    function setup() {
        $this->mod = new Test\BaseModule;
        $this->sut = new ObjectGraphBuilder($this->mod);
    }

    function test_inflate_root_object() {
        $c = $this->sut->withServices($this->mod->mapping)->build();
        $root = $c->get(Test\Root::class);
        assertNotNull($root);
        assertInstanceOf(Test\ROot::class, $root);
    }

    function test_unscoped_objects_are_unique() {
        $c = $this->sut->withServices($this->mod->mapping)->build();
        $a = $c->get(Test\Root::class);
        $b = $c->get(Test\Root::class);
        assertNotSame($a, $b);
    }

    function test_scoped_objects_are_identical() {
        $c = $this->sut->withScoped($this->mod->mapping)->build();
        $a = $c->get(Test\Root::class);
        $b = $c->get(Test\Root::class);
        assertSame($a, $b);
    }

    function test_scoped_dependencies_are_identical() {
        $c = $this->sut->withScoped($this->mod->mapping)->build();
        $a = $c->get(Test\Dep1::class);
        $b = $c->get(Test\Dep3::class);
        assertInstanceOf(Test\SubDep1::class, $a->a);
        assertInstanceof(Test\subdep1::class, $b->a);
        assertSame($a->a, $b->a);
    }

    function test_changing_unscoped_to_scoped() {
        $c = $this->sut->withServices($this->mod->mapping)
            ->bind('subdep1', Test\SubDep1::class, self::SCOPED)
            ->build();
        $a = $c->get(Test\Dep1::class);
        $b = $c->get(Test\Dep3::class);
        assertSame($a->a, $b->a);
        $d = $c->get(Test\Dep2::class);
        assertInstanceOf(Test\SubDep2::class, $a->a->a);
        assertInstanceOf(Test\SubDep2::class, $d->a);
        assertNotSame($a->a->a, $d->a);
    }

    function test_change_scoped_to_unscoped() {
        $c = $this->sut->withScoped($this->mod->mapping)
            ->bind('subdep2', Test\SubDep2::class)
            ->build();
        $a = $c->get(Test\Dep1::class);
        $b = $c->get(Test\Dep3::class);
        assertSame($a->a, $b->a);
        $d = $c->get(Test\Dep2::class);
        assertNotSame($a->a->a, $d->a);
    }

    function test_change_many_unscoped_to_scoped() {
        $c = $this->sut->withServices($this->mod->mapping)
            ->withScoped($this->mod->mapping)
            ->build();
        $a = $c->get(Test\Root::class);
        $b = $c->get(Test\Root::class);
        assertSame($a, $b);
        assertSame($a->a->a->a, $a->b->a);
    }

    function test_change_many_scoped_to_unscoped() {
        $c = $this->sut->withScoped($this->mod->mapping)
            ->withServices($this->mod->mapping)
            ->build();
        $a = $c->get(Test\Root::class);
        $b = $c->get(Test\Root::class);
        assertNotSame($a, $b);
        assertNotSame($a->a->a->a, $a->b->a);
    }

}
