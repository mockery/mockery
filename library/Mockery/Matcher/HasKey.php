<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

use ArrayAccess;
use Override;

use ReturnTypeWillChange;

use function array_key_exists;
use function is_array;
use function sprintf;

class HasKey extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return sprintf('<HasKey[%s]>', $this->_expected);
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
        if (! is_array($actual) && ! $actual instanceof ArrayAccess) {
            return false;
        }

        return array_key_exists($this->_expected, (array) $actual);
    }
}
