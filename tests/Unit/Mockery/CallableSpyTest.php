<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\InvalidCountException;
use Throwable;

use function spy;

/**
 * @coversDefaultClass \Mockery
 */
final class CallableSpyTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItActsAsPartial(): void
    {
        $spy = spy(function ($number) {
            return $number + 1;
        });

        self::assertSame(124, $spy(123));
        $spy->shouldHaveBeenCalled();
    }

    /**
     * @throws Throwable
     */
    public function testItCanVerifyItWasCalledANumberOfTimes(): void
    {
        $spy = spy(function (): void {});

        $spy();
        $spy();

        $spy->shouldHaveBeenCalled()
            ->twice();
    }

    /**
     * @throws Throwable
     */
    public function testItCanVerifyItWasCalledANumberOfTimesWithParticularArguments(): void
    {
        $spy = spy(function (): void {});

        $spy(123);
        $spy(123);

        $spy->shouldHaveBeenCalled()
            ->with(123)
            ->twice();
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfItWasCalledLessThanTheNumberOfTimesWeExpected(): void
    {
        $spy = spy(function (): void {});

        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->twice();
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfItWasCalledLessThanTheNumberOfTimesWeExpectedWithParticularArguments(): void
    {
        $spy = spy(function (): void {});

        $spy();
        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123)
            ->twice();
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfItWasCalledMoreThanTheNumberOfTimesWeExpected(): void
    {
        $spy = spy(function (): void {});

        $spy();
        $spy();
        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->twice();
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfItWasCalledMoreThanTheNumberOfTimesWeExpectedWithParticularArguments(): void
    {
        $spy = spy(function (): void {});

        $spy(123);
        $spy(123);
        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123)
            ->twice();
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfItWasCalledWhenWeExpectedItToNotHaveBeenCalled(): void
    {
        $spy = spy(function (): void {});

        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldNotHaveBeenCalled();
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfItWasCalledWithTheArgsWeWereNotExpecting(): void
    {
        $spy = spy(function (): void {});

        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldNotHaveBeenCalled([123]);
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfTheArgumentsDoNotMatch(): void
    {
        $spy = spy(function (): void {});

        $spy(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123, 546);
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfTheCallableWasNotCalledAtAll(): void
    {
        $spy = spy(function (): void {});

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled();
    }

    /**
     * @throws Throwable
     */
    public function testItThrowsIfThereWereNoArgumentsButWeExpectedSome(): void
    {
        $spy = spy(function (): void {});

        $spy();

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveBeenCalled()
            ->with(123, 546);
    }

    /**
     * @throws Throwable
     */
    public function testItVerifiesItWasNotCalledWithSomeParticularArgumentsWhenCalledWithDifferentArgs(): void
    {
        $spy = spy(function (): void {});

        $spy(456);

        $spy->shouldNotHaveBeenCalled([123]);
    }

    /**
     * @throws Throwable
     */
    public function testItVerifiesItWasNotCalledWithSomeParticularArgumentsWhenCalledWithNoArgs(): void
    {
        $spy = spy(function (): void {});

        $spy();

        $spy->shouldNotHaveBeenCalled([123]);
    }

    /**
     * @throws Throwable
     */
    public function testItVerifiesTheClosureWasCalled(): void
    {
        $spy = spy(function (): void {});

        $spy();

        $spy->shouldHaveBeenCalled();
    }

    /**
     * @throws Throwable
     */
    public function testItVerifiesTheClosureWasNotCalled(): void
    {
        $spy = spy(function (): void {});

        $spy->shouldNotHaveBeenCalled();
    }
}
