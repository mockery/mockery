<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Exception;

use Mockery\Exception;
use Mockery\LegacyMockInterface;

class NoMatchingExpectationException extends Exception
{
    /**
     * @var array<mixed>
     */
    protected $actual = [];

    /**
     * @var string|null
     */
    protected $method;

    /**
     * @var LegacyMockInterface|null
     */
    protected $mockObject;

    /**
     * @return array<mixed>
     */
    public function getActualArguments()
    {
        return $this->actual;
    }

    /**
     * @return string|null
     */
    public function getMethodName()
    {
        return $this->method;
    }

    /**
     * @return LegacyMockInterface|null
     */
    public function getMock()
    {
        return $this->mockObject;
    }

    /**
     * @return string|null
     */
    public function getMockName()
    {
        $mock = $this->getMock();

        if (null === $mock) {
            return $mock;
        }

        return $mock->mockery_getName();
    }

    /**
     * @todo Rename param `count` to `args`
     *
     * @param  array<mixed> $count
     * @return static
     */
    public function setActualArguments($count)
    {
        $this->actual = $count;

        return $this;
    }

    /**
     * @param  string $name
     * @return static
     */
    public function setMethodName($name)
    {
        $this->method = $name;

        return $this;
    }

    /**
     * @return static
     */
    public function setMock(LegacyMockInterface $mock)
    {
        $this->mockObject = $mock;

        return $this;
    }
}
