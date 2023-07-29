<?php

namespace Mockery\Tests\Unit\PHP82;

use Generator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Reflector;
use ReflectionType;

/**
 * @requires PHP 8.2.0-dev
 */
class Php82LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @param class-string $fullyQualifiedClassName
     * @dataProvider parameterContraVarianceDataProvider
     */
    public function testMockParameterDisjunctiveNormalFormTypes(string $fullyQualifiedClassName): void
    {
        $expectedReflectionClass = new \ReflectionClass($fullyQualifiedClassName);
        $expectedMethod = $expectedReflectionClass->getMethods()[0];
        $expectedType = $expectedMethod
            ->getParameters()[0]
            ->getType();

        $mock = mock($fullyQualifiedClassName);

        $reflectionClass = new \ReflectionClass($mock);
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
        $expectedReflectionClass = new \ReflectionClass($fullyQualifiedClassName);
        $expectedMethod = $expectedReflectionClass->getMethods()[0];
        $expectedType = $expectedMethod->getReturnType();

        self::assertInstanceOf(ReflectionType::class, $expectedType);

        $mock = mock($fullyQualifiedClassName);

        $reflectionClass = new \ReflectionClass($mock);

        $type = $reflectionClass->getMethod($expectedMethod->getName())
            ->getReturnType();

        self::assertInstanceOf(ReflectionType::class, $type);

        self::assertSame($expectedType->__toString(), $type->__toString());
    }

    public static function parameterContraVarianceDataProvider(): Generator
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

    public function testTypeHintIterableObject(): void
    {
        $refClass = new \ReflectionClass(IterableObject::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame(
            'iterable|object',
            Reflector::getTypeHint($refParam)
        );
    }

    public function testTypeHintIterableObjectString(): void
    {
        $refClass = new \ReflectionClass(IterableObjectString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame(
            'iterable|object|string',
            Reflector::getTypeHint($refParam)
        );
    }

    public function testTypeHintIIterableStdClassString(): void
    {
        $refClass = new \ReflectionClass(IterableStdClassString::class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame(
            'iterable|\stdClass|string',
            Reflector::getTypeHint($refParam)
        );
    }
}

class IterableObject
{
    public function __invoke(iterable|object $arg): void
    {
    }
}

class IterableObjectString
{
    public function __invoke(iterable|object|string $arg): void
    {
    }
}

class IterableStdClassString
{
    public function __invoke(iterable|\stdClass|string $arg): void
    {
    }
}

/**
 * The test fixtures in this directory have been directly copied and pasted from the source mentioned,
 * which is the PHP RFC (Request for Comments) titled "DNF Types" available at https://wiki.php.net/rfc/dnf_types.
 *
 * Please note that the copyrights for these test fixtures belong to:
 *
 * Copyright (c) George Peter Banyard <girgias@php.net>
 * Copyright (c) Larry Garfield <crell@php.net>
 *
 * All rights are reserved by the respective authors.
 */

interface A
{
}
interface B
{
}
interface C extends A, B
{
}
interface D
{
}

class W implements A
{
}
class X implements B
{
}
class Y implements C
{
}
class Z extends Y implements D
{
}

class Sut
{
    public function foo(A|(B&C)$arg)
    {
        var_dump($arg);
    }
}

interface ITest {
    public function stuff((A&B)|D $arg): void;
}

// Acceptable. Everything that ITest accepts is still valid
// and then some.
class TestOne implements ITest {
    public function stuff((A&B)|D|Z $arg): void {}
}

// Acceptable. This accepts objects that implement just
// A, which is a super-set of those that implement A&B.
class TestTwo implements ITest {
    public function stuff(A|D $arg): void {}
}

interface ITestTwo {
    public function things(C|D $arg): void;
}

// Anything that implements C implements A&B,
// but this rule also allows classes that implement A&B
// directly, and thus is wider.
class TestThree implements ITestTwo {
    public function things((A&B)|D $arg): void
    {

    }
}

interface IReturnCoVarianceTest
{
    public function stuff(): (A&B)|D;
}

// A&B is more restrictive.
class TestReturnCoVarianceOne implements IReturnCoVarianceTest {
    public function stuff(): A&B {
        return new Y;
    }
}

// D is is a subset of A&B|D
class TestReturnCoVarianceTwo implements IReturnCoVarianceTest {
    public function stuff(): D {
        return new Z;
    }
}

// Since C is a subset of A&B, even though it is not identical.
class TestReturnCoVarianceThree implements IReturnCoVarianceTest {
    public function stuff(): C|D {
        return new Y;
    }
}
