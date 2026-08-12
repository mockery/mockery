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

use function class_exists;
use function function_exists;
use function interface_exists;
use function is_string;
use function strtolower;
use function ucfirst;

class Type extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return '<' . ucfirst($this->_expected) . '>';
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
        $function = 'real' === $this->_expected ? 'is_float' : 'is_' . strtolower($this->_expected);

        if (function_exists($function)) {
            return $function($actual);
        }

        if (! is_string($this->_expected)) {
            return false;
        }

        if (class_exists($this->_expected) || interface_exists($this->_expected)) {
            return $actual instanceof $this->_expected;
        }

        return false;
    }
}
