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

class InvalidOrderException extends Exception
{
    /**
     * @var int|null
     */
    protected $actual;

    /**
     * @var int
     */
    protected $expected = 0;

    /**
     * @var string|null
     */
    protected $method;

    /**
     * @var LegacyMockInterface|null
     */
    protected $mockObject;

    /**
     * @return int|null
     */
    public function getActualOrder()
    {
        return $this->actual;
    }

    /**
     * @return int
     */
    public function getExpectedOrder()
    {
        return $this->expected;
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
     * @param  int  $count
     * @return self
     */
    public function setActualOrder($count)
    {
        $this->actual = $count;

        return $this;
    }

    /**
     * @param  int  $count
     * @return self
     */
    public function setExpectedOrder($count)
    {
        $this->expected = $count;

        return $this;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setMethodName($name)
    {
        $this->method = $name;

        return $this;
    }

    /**
     * @return self
     */
    public function setMock(LegacyMockInterface $mock)
    {
        $this->mockObject = $mock;

        return $this;
    }
}
