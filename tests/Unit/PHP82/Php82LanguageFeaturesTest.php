<?php

namespace Mockery\Tests\Unit\PHP82;

use Fixture\PHP82\DisjunctiveNormalFormTypes\ParameterContraVariance;
use Fixture\PHP82\DisjunctiveNormalFormTypes\ReturnCoVariance;
use Fixture\PHP82\Sut;
use Generator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Reflection;
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
            ParameterContraVariance\TestOne::class,
            ParameterContraVariance\TestTwo::class,
            ParameterContraVariance\TestThree::class,
        ];

        foreach ($fixtures as $fixture) {
            yield $fixture => [$fixture];
        }
    }
    public static function returnCoVarianceDataProvider(): Generator
    {
        $fixtures = [
            ReturnCoVariance\TestOne::class,
            ReturnCoVariance\TestTwo::class,
            ReturnCoVariance\TestThree::class,
        ];

        foreach ($fixtures as $fixture) {
            yield $fixture => [$fixture];
        }
    }
}
