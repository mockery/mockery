<?php

namespace Mockery\Tests\Unit\PHP82;

use Fixture\PHP82\Sut;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @requires PHP 8.2.0-dev
 */
class Php82LanguageFeaturesTest extends MockeryTestCase
{
    public function testCanMockDisjunctiveNormalFormTypes(): void
    {
        $mock = mock(Sut::class);

        $reflectionClass = new \ReflectionClass($mock);
        $type = $reflectionClass->getMethod('foo')
            ->getParameters()[0]
            ->getType();

        $expectedReflectionClass = new \ReflectionClass(Sut::class);
        $expectedType = $expectedReflectionClass->getMethod('foo')
            ->getParameters()[0]
            ->getType();

        self::assertSame($expectedType->__toString(),$type->__toString());
    }
}
