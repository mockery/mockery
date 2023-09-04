<?php declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Tests\Unit\PHP82;

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

/**
 * @requires PHP 8.2.0-dev
 * @internal
 */
final class Php82LanguageFeaturesTest extends MockeryTestCase
{

    public static function provideMockParameterDisjunctiveNormalFormTypesCases(): iterable
    {
        $fixtures = [
            Sut::class,
            TestOne::class,
            TestTwo::class,
            TestThree::class,
        ];

        foreach ($fixtures as $fixture) {
            yield $fixture => [$fixture];
        }
    }
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

    public function testCanMockReservedWordFalse(): void
    {
        $mock = mock(HasReservedWordFalse::class);

        $mock->expects('testFalseMethod')->once();

        static::assertFalse($mock->testFalseMethod());
        static::assertInstanceOf(HasReservedWordFalse::class, $mock);
    }

    public function testCanMockReservedWordTrue(): void
    {
        $mock = mock(HasReservedWordTrue::class);

        $mock->expects('testTrueMethod')->once();

        static::assertTrue($mock->testTrueMethod());
        static::assertInstanceOf(HasReservedWordTrue::class, $mock);
    }

    public function testCanMockUndefinedClasses(): void
    {
        $class = mock('UndefinedClass');

        $class->foo = 'bar';

        static::assertSame('bar', $class->foo);
    }


    public function testItCanMockAnClassWithNullReturnType(): void
    {
        $mock = Mockery::mock(HasNullReturnType::class);

        static::assertInstanceOf(HasNullReturnType::class, $mock);
    }
    /**
     * @param class-string $fullyQualifiedClassName
     * @dataProvider provideMockParameterDisjunctiveNormalFormTypesCases
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

        static::assertSame($expectedType->__toString(), $type->__toString());
    }

    /**
     * @param class-string $fullyQualifiedClassName
     * @dataProvider provideMockReturnDisjunctiveNormalFormTypesCases
     */
    public function testMockReturnDisjunctiveNormalFormTypes(string $fullyQualifiedClassName): void
    {
        $expectedReflectionClass = new ReflectionClass($fullyQualifiedClassName);
        $expectedMethod = $expectedReflectionClass->getMethods()[0];
        $expectedType = $expectedMethod->getReturnType();

        static::assertInstanceOf(ReflectionType::class, $expectedType);

        $mock = mock($fullyQualifiedClassName);

        $reflectionClass = new ReflectionClass($mock);

        $type = $reflectionClass->getMethod($expectedMethod->getName())
            ->getReturnType();

        static::assertInstanceOf(ReflectionType::class, $type);

        static::assertSame($expectedType->__toString(), $type->__toString());
    }

    public function testTypeHintIIterableStdClassString(): void
    {
        $refClass = new ReflectionClass(IterableStdClassString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        static::assertSame(
            'iterable|\stdClass|string',
            Reflector::getTypeHint($refParam)
        );
    }

    public function testTypeHintIterableObject(): void
    {
        $refClass = new ReflectionClass(IterableObject::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        static::assertSame(
            'iterable|object',
            Reflector::getTypeHint($refParam)
        );
    }

    public function testTypeHintIterableObjectString(): void
    {
        $refClass = new ReflectionClass(IterableObjectString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        static::assertSame(
            'iterable|object|string',
            Reflector::getTypeHint($refParam)
        );
    }
}
