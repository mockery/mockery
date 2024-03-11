<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Exception;

use Mockery\CountValidator\Exception;
use Mockery\LegacyMockInterface;

use function in_array;

class InvalidCountException extends Exception
{
    protected $actual = null;

    protected $expected = 0;

    protected $expectedComparative = '<=';

    protected $method = null;

    protected $mockObject = null;

    public function getActualCount()
    {
        return $this->actual;
    }

    public function getExpectedCount()
    {
        return $this->expected;
    }

    public function getExpectedCountComparative()
    {
        return $this->expectedComparative;
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

    public function setActualCount($count)
    {
        $this->actual = $count;
        return $this;
    }

    public function setExpectedCount($count)
    {
        $this->expected = $count;
        return $this;
    }

    public function setExpectedCountComparative($comp)
    {
        if (! in_array($comp, ['=', '>', '<', '>=', '<='], true)) {
            throw new RuntimeException('Illegal comparative for expected call counts set: ' . $comp);
        }

        $this->expectedComparative = $comp;
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
