<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of ReplaceableContainer
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
interface ReplaceableContainer extends ContainerInterface {

    /**
     * When invoked, all future calls to has() should return false for this $id.
     *
     * @param string $id
     */
    function supersede($id);
}
