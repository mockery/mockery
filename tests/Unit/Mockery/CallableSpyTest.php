<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\InvalidCountException;

/**
 * @coversDefaultClass \Mockery
 */
final class CallableSpyTest extends MockeryTestCase
{
    public function testItActsAsPartial(): void
    {
        $spy = \spy(function ($number) {
            return $number + 1;
        });

        self::assertSame(124, $spy(123));
        $spy->shouldHaveBeenCalled();
    }

    public function testItCanVerifyItWasCalledANumberOfTimes(): void
    {
        $spy = \spy(function (): void {});

        $spy();
        $spy();

        $spy->shouldHaveBeenCalled()
            ->twice();
    }

    public function testItCanVerifyItWasCalledANumberOfTimesWithParticularArguments(): void
    {
        $spy = \spy(function (): void {});

        $spy(123);
        $spy(123);

        $spy->shouldHaveBeenCalled()
            ->with(123)
            ->twice();
    }

    public function testItThrowsIfItWasCalledLessThanTheNumberOfTimesWeExpected(): void
    {
        $spy = \spy(function (): void {});

        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->twice();
    }

    public function testItThrowsIfItWasCalledLessThanTheNumberOfTimesWeExpectedWithParticularArguments(): void
    {
        $spy = \spy(function (): void {});

        $spy();
        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123)
            ->twice();
    }

    public function testItThrowsIfItWasCalledMoreThanTheNumberOfTimesWeExpected(): void
    {
        $spy = \spy(function (): void {});

        $spy();
        $spy();
        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->twice();
    }

    public function testItThrowsIfItWasCalledMoreThanTheNumberOfTimesWeExpectedWithParticularArguments(): void
    {
        $spy = \spy(function (): void {});

        $spy(123);
        $spy(123);
        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123)
            ->twice();
    }

    public function testItThrowsIfItWasCalledWhenWeExpectedItToNotHaveBeenCalled(): void
    {
        $spy = \spy(function (): void {});

        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldNotHaveBeenCalled();
    }

    public function testItThrowsIfItWasCalledWithTheArgsWeWereNotExpecting(): void
    {
        $spy = \spy(function (): void {});

        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldNotHaveBeenCalled([123]);
    }

    public function testItThrowsIfTheArgumentsDoNotMatch(): void
    {
        $spy = \spy(function (): void {});

        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123, 546);
    }

    public function testItThrowsIfTheCallableWasNotCalledAtAll(): void
    {
        $spy = \spy(function (): void {});

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled();
    }

    public function testItThrowsIfThereWereNoArgumentsButWeExpectedSome(): void
    {
        $spy = \spy(function (): void {});

        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123, 546);
    }

    public function testItVerifiesItWasNotCalledWithSomeParticularArgumentsWhenCalledWithDifferentArgs(): void
    {
        $spy = \spy(function (): void {});

        $spy(456);

        $spy->shouldNotHaveBeenCalled([123]);
    }

    public function testItVerifiesItWasNotCalledWithSomeParticularArgumentsWhenCalledWithNoArgs(): void
    {
        $spy = \spy(function (): void {});

        $spy();

        $spy->shouldNotHaveBeenCalled([123]);
    }

    public function testItVerifiesTheClosureWasCalled(): void
    {
        $spy = \spy(function (): void {});

        $spy();

        $spy->shouldHaveBeenCalled();
    }

    public function testItVerifiesTheClosureWasNotCalled(): void
    {
        $spy = \spy(function (): void {});

        $spy->shouldNotHaveBeenCalled();
    }
}
