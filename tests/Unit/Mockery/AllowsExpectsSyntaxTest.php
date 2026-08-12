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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\InvalidCountException;
use PHP73\ClassWithAllowsMethod;
use PHP73\ClassWithExpectsMethod;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class AllowsExpectsSyntaxTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testAllowsCanTakeAString(): void
    {
        $stub = Mockery::mock();
        $stub->allows('foo')
            ->andReturns('bar');
        self::assertSame('bar', $stub->foo());
    }

    /**
     * @throws Throwable
     */
    public function testAllowsCanTakeAnArrayOfCalls(): void
    {
        $stub = Mockery::mock();
        $stub->allows([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        self::assertSame('bar', $stub->foo());
        self::assertSame('baz', $stub->bar());
    }

    /**
     * @throws Throwable
     */
    public function testAllowsSetsUpMethodStub(): void
    {
        $stub = Mockery::mock();
        $stub->allows()
            ->foo(123)
            ->andReturns(456);

        self::assertSame(456, $stub->foo(123));
    }

    /**
     * @throws Throwable
     */
    public function testCallVerificationCountCanBeOverridenAfterExpects(): void
    {
        $mock = Mockery::mock();
        $mock->expects()
            ->foo(123)
            ->twice();

        $mock->foo(123);
        $mock->foo(123);
    }

    /**
     * @throws Throwable
     */
    public function testCallVerificationCountCanBeOverridenAfterExpectsThrowsExceptionWhenIncorrectNumberOfCalls(): void
    {
        $mock = Mockery::mock();
        $mock->expects()
            ->foo(123)
            ->twice();

        $mock->foo(123);
        $this->expectException(InvalidCountException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testExpectsCanOptionallyMatchOnAnyArguments(): void
    {
        $mock = Mockery::mock();
        $mock->allows()
            ->foo()
            ->withAnyArgs()
            ->andReturns(123);

        self::assertSame(123, $mock->foo(456, 789));
    }

    /**
     * @throws Throwable
     */
    public function testExpectsCanTakeAString(): void
    {
        $mock = Mockery::mock();
        $mock->expects('foo')
            ->andReturns(123);

        self::assertSame(123, $mock->foo(456, 789));
    }

    /**
     * @throws Throwable
     */
    public function testExpectsSetsUpExpectationOfOneCall(): void
    {
        $mock = Mockery::mock();
        $mock->expects()
            ->foo(123);

        $this->expectException(InvalidCountException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testGenerateSkipsAllowsMethodIfAlreadyExists(): void
    {
        $stub = Mockery::mock(ClassWithAllowsMethod::class);

        $stub->shouldReceive('allows')
            ->andReturn(123);

        self::assertSame(123, $stub->allows());
    }

    /**
     * @throws Throwable
     */
    public function testGenerateSkipsExpectsMethodIfAlreadyExists(): void
    {
        $stub = Mockery::mock(ClassWithExpectsMethod::class);

        $stub->shouldReceive('expects')
            ->andReturn(123);

        self::assertSame(123, $stub->expects());
    }
}
