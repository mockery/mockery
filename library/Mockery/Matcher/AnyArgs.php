<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

use Override;
use ReturnTypeWillChange;

class AnyArgs extends MatcherAbstract implements ArgumentListMatcher
{
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return '<Any Arguments>';
    }

    /**
     * @param  mixed $actual
     * @return bool
     */
    #[Override]
    public function match(&$actual)
    {
        return true;
    }
}
