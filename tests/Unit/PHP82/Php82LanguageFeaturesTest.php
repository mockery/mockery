<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP82;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Reflector;
use PHP82\HasNullReturnType;
use PHP82\HasReservedWordFalse;
use PHP82\HasReservedWordTrue;
use PHP82\IterableObject;
use PHP82\IterableObjectString;
use PHP82\IterableStdClassString;
use PHP82\Sut;
use PHP82\TestOne;
use PHP82\TestReturnCoVarianceOne;
use PHP82\TestReturnCoVarianceThree;
use PHP82\TestReturnCoVarianceTwo;
use PHP82\TestThree;
use PHP82\TestTwo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use ReflectionClass;
use ReflectionType;
use Throwable;

use function mock;

/**
 * @requires PHP 8.2.0-dev
 *
 * @coversDefaultClass \Mockery
 */
#[RequiresPhp('>=8.2.0-dev')]
final class Php82LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public static function provideMockParameterDisjunctiveNormalFormTypesCases(): iterable
    {
        $fixtures = [Sut::class, TestOne::class, TestTwo::class, TestThree::class];

        foreach ($fixtures as $fixture) {
            yield $fixture => [$fixture];
        }
    }

    /**
     * @throws Throwable
     */
    public static function provideMockReturnDisjunctiveNormalFormTypesCases(): iterable
    {
        $fixtures = [
            TestReturnCoVarianceOne::class,
            TestReturnCoVarianceTwo::class,
            TestReturnCoVarianceThree::class,
        ];

        foreach ($fixtures as $fixture) {
            yield $fixture => [$fixture];
        }
    }

    /**
     * @throws Throwable
     */
    public function testCanMockReservedWordFalse(): void
    {
        $mock = mock(HasReservedWordFalse::class);

        $mock->expects('testFalseMethod')
            ->once();

        self::assertFalse($mock->testFalseMethod());
        self::assertInstanceOf(HasReservedWordFalse::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockReservedWordTrue(): void
    {
        $mock = mock(HasReservedWordTrue::class);

        $mock->expects('testTrueMethod')
            ->once();

        self::assertTrue($mock->testTrueMethod());
        self::assertInstanceOf(HasReservedWordTrue::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockUndefinedClasses(): void
    {
        $class = mock('MockUnDefinedClass');

        $class->foo = 'bar';

        self::assertSame('bar', $class->foo);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAnClassWithNullReturnType(): void
    {
        $mock = Mockery::mock(HasNullReturnType::class);

        self::assertInstanceOf(HasNullReturnType::class, $mock);
    }

    /**
     * @param class-string $fullyQualifiedClassName
     *
     * @dataProvider provideMockParameterDisjunctiveNormalFormTypesCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideMockParameterDisjunctiveNormalFormTypesCases')]
    public function testMockParameterDisjunctiveNormalFormTypes(string $fullyQualifiedClassName): void
    {
        $expectedReflectionClass = new ReflectionClass($fullyQualifiedClassName);
        $expectedMethod = $expectedReflectionClass->getMethods()[0];
        $expectedType = $expectedMethod
            ->getParameters()[0]
            ->getType();

        $mock = mock($fullyQualifiedClassName);

        $reflectionClass = new ReflectionClass($mock);
        $type = $reflectionClass->getMethod($expectedMethod->getName())
            ->getParameters()[0]
            ->getType();

        self::assertSame($expectedType->__toString(), $type->__toString());
    }

    /**
     * @param class-string $fullyQualifiedClassName
     *
     * @dataProvider provideMockReturnDisjunctiveNormalFormTypesCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideMockReturnDisjunctiveNormalFormTypesCases')]
    public function testMockReturnDisjunctiveNormalFormTypes(string $fullyQualifiedClassName): void
    {
        $expectedReflectionClass = new ReflectionClass($fullyQualifiedClassName);
        $expectedMethod = $expectedReflectionClass->getMethods()[0];
        $expectedType = $expectedMethod->getReturnType();

        self::assertInstanceOf(ReflectionType::class, $expectedType);

        $mock = mock($fullyQualifiedClassName);

        $reflectionClass = new ReflectionClass($mock);

        $type = $reflectionClass->getMethod($expectedMethod->getName())
            ->getReturnType();

        self::assertInstanceOf(ReflectionType::class, $type);

        self::assertSame($expectedType->__toString(), $type->__toString());
    }

    /**
     * @throws Throwable
     */
    public function testTypeHintIIterableStdClassString(): void
    {
        $refClass = new ReflectionClass(IterableStdClassString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame('iterable|\stdClass|string', Reflector::getTypeHint($refParam));
    }

    /**
     * @throws Throwable
     */
    public function testTypeHintIterableObject(): void
    {
        $refClass = new ReflectionClass(IterableObject::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame('iterable|object', Reflector::getTypeHint($refParam));
    }

    /**
     * @throws Throwable
     */
    public function testTypeHintIterableObjectString(): void
    {
        $refClass = new ReflectionClass(IterableObjectString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame('iterable|object|string', Reflector::getTypeHint($refParam));
    }
}
