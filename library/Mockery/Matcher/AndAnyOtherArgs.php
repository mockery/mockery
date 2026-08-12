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

class AndAnyOtherArgs extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return '<AndAnyOthers>';
    }

    /**
     * Check if the actual value matches the expected.
     *
     * @param  mixed $actual
     * @return bool
     */
    #[Override]
    public function match(&$actual)
    {
        return true;
    }
}
