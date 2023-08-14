<?php

namespace Mockery\Tests\Unit\Issues;

use DateTime;
use InvalidArgumentException;
use Mockery;
use Mockery\Exception\InvalidCountException;
use PHPUnit\Framework\TestCase;

final class Issue1328Test extends TestCase
{
    public function testShouldFailWithAnInvocationCountError(): void
    {
        $this->expectException(InvalidCountException::class);

        $mock = Mockery::mock(DateTime::class);

        $mock->shouldNotReceive("format");

        $mock->format("Y");

        Mockery::close();
    }

    public function testThrowsInvalidArgumentExceptionWhenInvocationCountChanges(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mock = Mockery::mock(DateTime::class);

        $mock->shouldNotReceive("format")->once();

        $mock->format("Y");

        Mockery::close();
    }

    public function testThrowsInvalidArgumentExceptionForChainingAdditionalInvocationCountMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mock = Mockery::mock(DateTime::class);

        $mock->shouldNotReceive("format")->times(0);

        $mock->format("Y");

        Mockery::close();
    }
}
