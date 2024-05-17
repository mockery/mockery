<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\InvalidCountException;

use function anyArgs;

/**
 * @coversDefaultClass \Mockery
 */
final class SpyTest extends MockeryTestCase
{
    public function testAnyArgsCanBeUsedWithAlternativeSyntax(): void
    {
        $spy = Mockery::spy();
        $spy->foo(123, 456);

        $spy->shouldHaveReceived()
            ->foo(anyArgs());
    }

    public function testItIncrementsExpectationCountWhenShouldHaveReceivedIsUsed(): void
    {
        $spy = Mockery::spy();
        $spy->myMethod('param1', 'param2');
        $spy->shouldHaveReceived('myMethod')
            ->with('param1', 'param2');
        self::assertEquals(1, $spy->mockery_getExpectationCount());
    }

    public function testItIncrementsExpectationCountWhenShouldNotHaveReceivedIsUsed(): void
    {
        $spy = Mockery::spy();
        $spy->shouldNotHaveReceived('method');
        self::assertEquals(1, $spy->mockery_getExpectationCount());
    }

    public function testItVerifiesAMethodWasCalled(): void
    {
        $spy = Mockery::spy();
        $spy->myMethod();
        $spy->shouldHaveReceived('myMethod');

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveReceived('someMethodThatWasNotCalled');
    }

    public function testItVerifiesAMethodWasCalledASpecificNumberOfTimes(): void
    {
        $spy = Mockery::spy();
        $spy->myMethod();
        $spy->myMethod();
        $spy->shouldHaveReceived('myMethod')
            ->twice();

        $this->expectException(InvalidCountException::class);
        $spy->myMethod();
        $spy->shouldHaveReceived('myMethod')
            ->twice();
    }

    public function testItVerifiesAMethodWasCalledWithSpecificArguments(): void
    {
        $spy = Mockery::spy();
        $spy->myMethod(123, 'a string');
        $spy->shouldHaveReceived('myMethod')
            ->with(123, 'a string');
        $spy->shouldHaveReceived('myMethod', [123, 'a string']);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveReceived('myMethod')
            ->with(123);
    }

    public function testItVerifiesAMethodWasNotCalled(): void
    {
        $spy = Mockery::spy();
        $spy->shouldNotHaveReceived('myMethod');

        $this->expectException(InvalidCountException::class);
        $spy->myMethod();
        $spy->shouldNotHaveReceived('myMethod');
    }

    public function testItVerifiesAMethodWasNotCalledWithParticularArguments(): void
    {
        $spy = Mockery::spy();
        $spy->myMethod(123, 456);

        $spy->shouldNotHaveReceived('myMethod', [789, 10]);

        $this->expectException(InvalidCountException::class);
        $spy->shouldNotHaveReceived('myMethod', [123, 456]);
    }

    public function testShouldHaveReceivedHigherOrderMessageCallAMethodWithCorrectArguments(): void
    {
        $spy = Mockery::spy();
        $spy->foo(123);

        $spy->shouldHaveReceived()
            ->foo(123);
    }

    public function testShouldHaveReceivedHigherOrderMessageCallAMethodWithIncorrectArgumentsThrowsException(): void
    {
        $spy = Mockery::spy();
        $spy->foo(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldHaveReceived()
            ->foo(456);
    }

    public function testShouldNotHaveReceivedHigherOrderMessageCallAMethodWithCorrectArgumentsThrowsAnException(): void
    {
        $spy = Mockery::spy();
        $spy->foo(123);

        $this->expectException(InvalidCountException::class);
        $spy->shouldNotHaveReceived()
            ->foo(123);
    }

    public function testShouldNotHaveReceivedHigherOrderMessageCallAMethodWithIncorrectArguments(): void
    {
        $spy = Mockery::spy();
        $spy->foo(123);

        $spy->shouldNotHaveReceived()
            ->foo(456);
    }
}
