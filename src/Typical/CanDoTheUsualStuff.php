<?php

namespace Codeia\Typical;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Composes the most commonly used traits in views into a single trait.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
trait CanDoTheUsualStuff {
    use CanGenerateUrls;
    use CanRenderTemplates;
    use CanWriteContent;
}
