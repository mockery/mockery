<?php

namespace Mockery\Tests\Unit\Regression;

use DateTime;
use InvalidArgumentException;
use Mockery;
use Mockery\Exception\InvalidCountException;
use Mockery\Expectation;
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
        set_error_handler(
            static function (int $errorCode, string $errorMessage): void {
                restore_error_handler();
                throw new InvalidArgumentException($errorMessage, $errorCode);
            },
            E_ALL
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Expectation::ERROR_ZERO_INVOCATION);

        $mock = Mockery::mock(DateTime::class);

        $mock->shouldNotReceive("format")->once();

        $mock->format("Y");

        Mockery::close();
    }

    public function testThrowsInvalidArgumentExceptionForChainingAdditionalInvocationCountMethod(): void
    {
        set_error_handler(
            static function (int $errorCode, string $errorMessage): void {
                restore_error_handler();
                throw new InvalidArgumentException($errorMessage, $errorCode);
            },
            E_ALL
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Expectation::ERROR_ZERO_INVOCATION);

        $mock = Mockery::mock(DateTime::class);

        $mock->shouldNotReceive("format")->times(0);

        $mock->format("Y");

        Mockery::close();
    }
}
