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

use function is_object;

/**
 * @deprecated 2.0 Due to ambiguity, use PHPUnit equivalents
 */
class MustBe extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return '<MustBe>';
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
        if (! is_object($actual)) {
            return $this->_expected === $actual;
        }

        return $this->_expected == $actual;
    }
}
