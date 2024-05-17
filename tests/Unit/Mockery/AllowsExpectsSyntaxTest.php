<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\InvalidCountException;
use PHP73\ClassWithAllowsMethod;
use PHP73\ClassWithExpectsMethod;

/**
 * @coversDefaultClass \Mockery
 */
final class AllowsExpectsSyntaxTest extends MockeryTestCase
{
    public function testAllowsCanTakeAString(): void
    {
        $stub = Mockery::mock();
        $stub->allows('foo')
            ->andReturns('bar');
        self::assertEquals('bar', $stub->foo());
    }

    public function testAllowsCanTakeAnArrayOfCalls(): void
    {
        $stub = Mockery::mock();
        $stub->allows([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        self::assertEquals('bar', $stub->foo());
        self::assertEquals('baz', $stub->bar());
    }

    public function testAllowsSetsUpMethodStub(): void
    {
        $stub = Mockery::mock();
        $stub->allows()
            ->foo(123)
            ->andReturns(456);

        self::assertEquals(456, $stub->foo(123));
    }

    public function testCallVerificationCountCanBeOverridenAfterExpects(): void
    {
        $mock = Mockery::mock();
        $mock->expects()
            ->foo(123)
            ->twice();

        $mock->foo(123);
        $mock->foo(123);
    }

    public function testCallVerificationCountCanBeOverridenAfterExpectsThrowsExceptionWhenIncorrectNumberOfCalls(): void
    {
        $mock = Mockery::mock();
        $mock->expects()
            ->foo(123)
            ->twice();

        $mock->foo(123);
        $this->expectException(\Mockery\Exception\InvalidCountException::class);
        Mockery::close();
    }

    public function testExpectsCanOptionallyMatchOnAnyArguments(): void
    {
        $mock = Mockery::mock();
        $mock->allows()
            ->foo()
            ->withAnyArgs()
            ->andReturns(123);

        self::assertEquals(123, $mock->foo(456, 789));
    }

    public function testExpectsCanTakeAString(): void
    {
        $mock = Mockery::mock();
        $mock->expects('foo')
            ->andReturns(123);

        self::assertEquals(123, $mock->foo(456, 789));
    }

    public function testExpectsSetsUpExpectationOfOneCall(): void
    {
        $mock = Mockery::mock();
        $mock->expects()
            ->foo(123);

        $this->expectException(InvalidCountException::class);
        Mockery::close();
    }

    public function testGenerateSkipsAllowsMethodIfAlreadyExists(): void
    {
        $stub = Mockery::mock(ClassWithAllowsMethod::class);

        $stub->shouldReceive('allows')
            ->andReturn(123);

        self::assertEquals(123, $stub->allows());
    }

    public function testGenerateSkipsExpectsMethodIfAlreadyExists(): void
    {
        $stub = Mockery::mock(ClassWithExpectsMethod::class);

        $stub->shouldReceive('expects')
            ->andReturn(123);

        self::assertEquals(123, $stub->expects());
    }
}
