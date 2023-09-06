<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Tests\Unit\PHP81;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PDO;
use PHP81\ArgumentIntersectionTypeHint;
use PHP81\ClassThatImplementsSerializable;
use PHP81\ClassWithNewInInitializer;
use PHP81\HandlerClass;
use PHP81\IntersectionTypeHelper1Interface;
use PHP81\IntersectionTypeHelperClass;
use PHP81\MockClass;
use PHP81\NeverReturningTypehintClass;
use PHP81\ReturnTypeWillChangeAttributeNoReturnType;
use PHP81\ReturnTypeWillChangeAttributeWrongReturnType;
use PHP81\SimpleEnum;
use PHP81\UsesEnums;
use RuntimeException;
use Serializable;
use TypeError;

use function pcntl_fork;
use function pcntl_waitpid;
use function pcntl_wexitstatus;

/**
 * @requires PHP 8.1.0-dev
 * @internal
 */
final class Php81LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @group issue/339
     */
    public function testCanMockClassesThatImplementSerializable(): void
    {
        $mock = mock(ClassThatImplementsSerializable::class);
        static::assertInstanceOf(Serializable::class, $mock);
    }


    public function testItCanMockAClassWithANeverReturningTypeHint(): void
    {
        $mock = Mockery::mock(NeverReturningTypehintClass::class)->makePartial();

        $this->expectException(RuntimeException::class);
        $mock->throws();
    }

    /**
     * @requires extension pcntl
     */
    public function testItCanMockAClassWithANeverReturningTypeHintWithExit(): void
    {
        $mock = Mockery::mock(NeverReturningTypehintClass::class)->makePartial();

        $pid = pcntl_fork();

        if (-1 === $pid) {
            static::markTestSkipped("Couldn't fork for exit test");

            return;
        } elseif ($pid) {
            pcntl_waitpid($pid, $status);
            static::assertEquals(123, pcntl_wexitstatus($status));

            return;
        }

        $mock->exits();
    }


    public function testItCanMockAClassWithAnIntersectionArgumentTypeHint(): void
    {
        $mock = Mockery::spy(ArgumentIntersectionTypeHint::class);
        $object = new IntersectionTypeHelperClass();
        $mock->allows()->foo($object);

        $mock->foo($object);

        $this->expectException(TypeError::class);
        $mock->foo(Mockery::mock(IntersectionTypeHelper1Interface::class));
    }


    public function testItCanMockAClassWithReturnTypeWillChangeAttributeAndNoReturnType(): void
    {
        $mock = spy(ReturnTypeWillChangeAttributeNoReturnType::class);

        static::assertNull($mock->getTimestamp());
    }


    public function testItCanMockAClassWithReturnTypeWillChangeAttributeAndWrongReturnType(): void
    {
        $mock = spy(ReturnTypeWillChangeAttributeWrongReturnType::class);

        static::assertSame(0.0, $mock->getTimestamp());
    }


    public function testItCanMockAnInternalClassWithTentativeReturnTypes(): void
    {
        $mock = spy(DateTime::class);

        static::assertSame(0, $mock->getTimestamp());
    }


    public function testItCanMockAnInternalClassWithTentativeUnionReturnTypes(): void
    {
        $mock = Mockery::mock(PDO::class);

        static::assertInstanceOf(PDO::class, $mock);

        $mock->shouldReceive('exec')->once();

        try {
            static::assertSame(0, $mock->exec('select * from foo.bar'));
        } finally {
            Mockery::close();
        }
    }


    public function testItCanParseEnumAsDefaultValueCorrectly(): void
    {
        $mock = Mockery::mock(UsesEnums::class);
        $mock->shouldReceive('set')->once();
        $mock->set();
        static::assertEquals(SimpleEnum::first, $mock->enum); // check that mock did not set internal variable
    }


    public function testMockingClassWithNewInInitializer(): void
    {
        $mock = Mockery::mock(ClassWithNewInInitializer::class);

        static::assertInstanceOf(ClassWithNewInInitializer::class, $mock);
    }
    public function testNewInitializerExpression(): void
    {
        $class = mock(MockClass::class)
            ->expects('test')
            ->with('test')
            ->andReturn('it works')
            ->getMock();

        static::assertSame('it works', (new HandlerClass())->doStuff($class));
    }
}
