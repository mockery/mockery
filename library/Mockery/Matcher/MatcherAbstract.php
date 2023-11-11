<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

abstract class MatcherAbstract implements Matcher
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
}
