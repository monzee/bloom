<?php

namespace Codeia\Test;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of VarAnnotations
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class VarAnnotations {
    /** '@var Codeia\Test\Emiya' */
    public $insideSingleQuotes;
    /** "@var Codeia\Test\Emiya" */
    public $insideDoubleQuotes;
    /** {@var Codeia\Test\Emiya} */
    public $insideBraces;
    /** \@var Codeia\Test\Emiya */
    public $atSignWasEscaped;
    /** \'@var Codeia\Test\Emiya */
    public $leftSingleQuoteWasEscaped;
    /** \"@var Codeia\Test\Emiya */
    public $leftDoubleQuoteWasEscaped;
    /** \{@var Codeia\Test\Emiya} */
    public $leftBraceWasEscaped;
    /** 'asdf' @var Codeia\Test\Emiya */
    public $afterSqString;
    /** "asdf" @var Codeia\Test\Emiya */
    public $afterDqString;
    /** {asdf} @var Codeia\Test\Emiya */
    public $afterBlock;

    /** @var l!ul\4head */
    private $followedByAnInvalidFQCN;
}
