<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP81;

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
use Throwable;
use TypeError;

use function mock;
use function pcntl_fork;
use function pcntl_waitpid;
use function pcntl_wexitstatus;
use function spy;

/**
 * @requires PHP 8.1.0-dev
 *
 * @coversDefaultClass \Mockery
 */
final class Php81LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testCanMockClassesThatImplementSerializable(): void
    {
        $mock = mock(ClassThatImplementsSerializable::class);

        self::assertInstanceOf(Serializable::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithANeverReturningTypeHint(): void
    {
        $mock = Mockery::mock(NeverReturningTypehintClass::class)->makePartial();

        $this->expectException(RuntimeException::class);
        $mock->throws();
    }

    /**
     * @requires extension pcntl
     *
     * @throws Throwable
     */
    public function testItCanMockAClassWithANeverReturningTypeHintWithExit(): void
    {
        $mock = Mockery::mock(NeverReturningTypehintClass::class)->makePartial();

        $pid = pcntl_fork();

        if (-1 === $pid) {
            self::markTestSkipped("Couldn't fork for exit test");

            return;
        }

        if ($pid) {
            pcntl_waitpid($pid, $status);
            self::assertSame(123, pcntl_wexitstatus($status));

            return;
        }

        $mock->exits();
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAnIntersectionArgumentTypeHint(): void
    {
        $mock = Mockery::spy(ArgumentIntersectionTypeHint::class);
        $object = new IntersectionTypeHelperClass();
        $mock->allows()
            ->foo($object);

        $mock->foo($object);

        $this->expectException(TypeError::class);
        $mock->foo(Mockery::mock(IntersectionTypeHelper1Interface::class));
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithReturnTypeWillChangeAttributeAndNoReturnType(): void
    {
        $mock = spy(ReturnTypeWillChangeAttributeNoReturnType::class);

        self::assertNull($mock->getTimestamp());
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithReturnTypeWillChangeAttributeAndWrongReturnType(): void
    {
        $mock = spy(ReturnTypeWillChangeAttributeWrongReturnType::class);

        self::assertSame(0.0, $mock->getTimestamp());
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAnInternalClassWithTentativeReturnTypes(): void
    {
        $mock = spy(DateTime::class);

        self::assertSame(0, $mock->getTimestamp());
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAnInternalClassWithTentativeUnionReturnTypes(): void
    {
        $mock = Mockery::mock(PDO::class);

        self::assertInstanceOf(PDO::class, $mock);

        $mock->shouldReceive('exec')
            ->once();

        self::assertSame(0, $mock->exec('select * from foo.bar'));
    }

    /**
     * @throws Throwable
     */
    public function testItCanParseEnumAsDefaultValueCorrectly(): void
    {
        $mock = Mockery::mock(UsesEnums::class);
        $mock->shouldReceive('set')
            ->once();
        $mock->set();
        self::assertSame(SimpleEnum::first, $mock->enum); // check that mock did not set internal variable
    }

    /**
     * @throws Throwable
     */
    public function testMockingClassWithNewInInitializer(): void
    {
        $mock = Mockery::mock(ClassWithNewInInitializer::class);

        self::assertInstanceOf(ClassWithNewInInitializer::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testNewInitializerExpression(): void
    {
        $class = mock(MockClass::class)
            ->expects('test')
            ->with('test')
            ->andReturn('it works')
            ->getMock();

        self::assertSame('it works', (new HandlerClass())->doStuff($class));
    }
}
