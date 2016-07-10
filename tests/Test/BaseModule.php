<?php

namespace Codeia\Test;

use Interop\Container\ContainerInterface as Container;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of BaseModule
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class BaseModule {

    public $mapping = [
        'root' => Root::class,
        'dep1' => Dep1::class,
        'dep2' => Dep2::class,
        'dep3' => Dep3::class,
        'subdep1' => SubDep1::class,
        'subdep2' => SubDep2::class,
    ];

    function root(Container $c) {
        return new Root($c->get(Dep1::class), $c->get(Dep2::class));
    }

    function dep1(Container $c) {
        return new Dep1($c->get(SubDep1::class));
    }

    function dep2(Container $c) {
        return new Dep2($c->get(SubDep2::class));
    }

    function dep3(Container $c) {
        return new Dep3($c->get(SubDep1::class));
    }

    function subdep1(Container $c) {
        return new SubDep1($c->get(SubDep2::class));
    }

    function subdep2() {
        return new SubDep2;
    }

    function take(&$xs, $var_args) {
        $args = func_get_args();
        array_shift($args);
        foreach ($args as $name) {
            $xs[$name] = $this->mapping[$name];
        }
    }

    function __get($name) {
        return $this->mapping[$name];
    }

}
