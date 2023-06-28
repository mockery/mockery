<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

class Pattern extends MatcherAbstract
{
    /**
     * Check if the actual value matches the expected pattern.
     *
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual)
    {
        return preg_match($this->_expected, (string) $actual) >= 1;
    }

    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        return '<Pattern>';
    }
}
