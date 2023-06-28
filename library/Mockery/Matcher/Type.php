<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

class Type extends MatcherAbstract
{
    /**
     * Check if the actual value matches the expected.
     *
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual)
    {
        if ($this->_expected == 'real') {
            $function = 'is_float';
        } else {
            $function = 'is_' . strtolower($this->_expected);
        }
        if (function_exists($function)) {
            return $function($actual);
        } elseif (is_string($this->_expected)
        && (class_exists($this->_expected) || interface_exists($this->_expected))) {
            return $actual instanceof $this->_expected;
        }
        return false;
    }

    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        return '<' . ucfirst($this->_expected) . '>';
    }
}
