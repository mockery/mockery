<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Exception;

use Mockery\Exception;
use Mockery\LegacyMockInterface;

class InvalidOrderException extends Exception
{
    protected $actual = null;

    protected $expected = 0;

    protected $method = null;

    protected $mockObject = null;

    public function getActualOrder()
    {
        return $this->actual;
    }

    public function getExpectedOrder()
    {
        return $this->expected;
    }

    public function getMethodName()
    {
        return $this->method;
    }

    public function getMock()
    {
        return $this->mockObject;
    }

    public function getMockName()
    {
        return $this->getMock()->mockery_getName();
    }

    public function setActualOrder($count)
    {
        $this->actual = $count;
        return $this;
    }

    public function setExpectedOrder($count)
    {
        $this->expected = $count;
        return $this;
    }

    public function setMethodName($name)
    {
        $this->method = $name;
        return $this;
    }

    public function setMock(LegacyMockInterface $mock)
    {
        $this->mockObject = $mock;
        return $this;
    }
}
