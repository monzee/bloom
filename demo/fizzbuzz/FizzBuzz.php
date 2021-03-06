<?php

namespace demo\fizzbuzz;

use Codeia\Mvc\Locatable;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * You know the drill.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class FizzBuzz implements Locatable {

    use \Codeia\Typical\CanGraftPathsInUrl;

    private $n;

    /** @param int $n */
    function __construct($n = 1) {
        $this->n = $n;
    }

    /** @param int $n */
    function at($n) {
        $this->n = $n;
    }

    /** @return string */
    function say() {
        $current = $this->n;
        list($fizz, $n) = $current % 3 == 0 ? ['Fizz', ''] : ['', $current];
        list($buzz, $n) = $current % 5 == 0 ? ['Buzz', ''] : ['', $n];
        return "{$fizz}{$buzz}{$n}";
    }

    /** @return FizzBuzz */
    function next() {
        return new FizzBuzz($this->n + 1);
    }

    private function pathSuffix() {
        return "/fizzbuzz/{$this->n}";
    }

}
