<?php

namespace Mockery\Tests\Unit\Issues;

use DateTime;
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

    public function testShouldFailWithAnInvocationCountErrorWhenInvocationCountChanges(): void
    {
        $this->expectException(InvalidCountException::class);

        $mock = Mockery::mock(DateTime::class);

        $mock->shouldNotReceive("format")->once();

        $mock->format("Y");

        Mockery::close();
    }
}
