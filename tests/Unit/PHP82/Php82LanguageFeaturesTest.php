<?php

declare(strict_types=1);

namespace Tests\Unit\PHP82;

use Generator;
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
use ReflectionClass;
use ReflectionType;

use function mock;

/**
 * @requires PHP 8.2.0-dev
 * @coversDefaultClass \Mockery
 */
final class Php82LanguageFeaturesTest extends MockeryTestCase
{
    public static function parameterContraVarianceDataProvider(): Generator
    {
        $fixtures = [Sut::class, TestOne::class, TestTwo::class, TestThree::class];

        foreach ($fixtures as $fixture) {
            yield $fixture => [$fixture];
        }
    }

    public static function returnCoVarianceDataProvider(): Generator
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

    public function testCanMockReservedWordFalse(): void
    {
        $mock = mock(HasReservedWordFalse::class);

        $mock->expects('testFalseMethod')
            ->once();

        self::assertFalse($mock->testFalseMethod());
        self::assertInstanceOf(HasReservedWordFalse::class, $mock);
    }

    public function testCanMockReservedWordTrue(): void
    {
        $mock = mock(HasReservedWordTrue::class);

        $mock->expects('testTrueMethod')
            ->once();

        self::assertTrue($mock->testTrueMethod());
        self::assertInstanceOf(HasReservedWordTrue::class, $mock);
    }

    public function testCanMockUndefinedClasses(): void
    {
        $class = mock('MockUnDefinedClass');

        $class->foo = 'bar';

        self::assertSame('bar', $class->foo);
    }

    public function testItCanMockAnClassWithNullReturnType(): void
    {
        $mock = Mockery::mock(HasNullReturnType::class);

        self::assertInstanceOf(HasNullReturnType::class, $mock);
    }

    /**
     * @param class-string $fullyQualifiedClassName
     * @dataProvider parameterContraVarianceDataProvider
     */
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
     * @dataProvider returnCoVarianceDataProvider
     */
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

    public function testTypeHintIIterableStdClassString(): void
    {
        $refClass = new ReflectionClass(IterableStdClassString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame('iterable|\stdClass|string', Reflector::getTypeHint($refParam));
    }

    public function testTypeHintIterableObject(): void
    {
        $refClass = new ReflectionClass(IterableObject::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame('iterable|object', Reflector::getTypeHint($refParam));
    }

    public function testTypeHintIterableObjectString(): void
    {
        $refClass = new ReflectionClass(IterableObjectString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame('iterable|object|string', Reflector::getTypeHint($refParam));
    }
}
