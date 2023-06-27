<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

abstract class MatcherAbstract
{
    /**
     * The expected value (or part thereof)
     *
     * @var mixed
     */
    protected $_expected = null;

    /**
     * Set the expected value
     *
     * @param mixed $expected
     */
    public function __construct($expected = null)
    {
        $this->_expected = $expected;
    }

    /**
     * Check if the actual value matches the expected.
     * Actual passed by reference to preserve reference trail (where applicable)
     * back to the original method parameter.
     *
     * @param mixed $actual
     * @return bool
     */
    abstract public function match(&$actual);

    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    abstract public function __toString();
}
