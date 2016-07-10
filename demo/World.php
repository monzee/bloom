<?php

namespace demo;

use Codeia\Mvc\Locatable;
use Psr\Http\Message\UriInterface as Uri;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * The model class of the Hello VC pair.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class World implements Locatable {

    private $name;

    function greet($name) {
        $this->name = $name;
    }

    function name() {
        return $this->name;
    }

    function randomPerson() {
        $choices = ['Phillip', 'Mercy', 'Candice', 'Jordan', 'Mickey', 'Dan'];
        $world = new World();
        $world->greet($choices[mt_rand(0, count($choices) - 1)]);
        return $world;
    }

    function locate(Uri $base) {
        $name = $this->name ? "/{$this->name}" : '';
        return $base->withPath("/hello{$name}");
    }

}