<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of ContainerGraph
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class ContainerGraph implements ContainerInterface {

    private $super;
    private $sub;

    function __construct(ContainerInterface $super, ContainerInterface $sub) {
        $this->super = $super;
        $this->sub = $sub;
    }

    function get($id) {
        if ($this->sub->has($id)) {
            return $this->sub->get($id);
        }
        return $this->super->get($id);
    }

    function has($id) {
        return $this->sub->has($id) || $this->super->has($id);
    }

}
