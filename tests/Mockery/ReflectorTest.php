<?php

declare(strict_types=1);

namespace Mockery\Tests\Mockery;

use Generator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Reflector;
use ReflectionClass;

/**
 * @coversDefaultClass \Mockery\Reflector
 */
class ReflectorTest extends MockeryTestCase
{
    /**
     * @covers \Mockery\Reflector::getTypeHint
     * @dataProvider typeHintDataProvider
     */
    public function testGetTypeHint(string $class, string $expectedTypeHint): void
    {
        $refClass = new ReflectionClass($class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame(
            $expectedTypeHint,
            Reflector::getTypeHint($refParam)
        );
    }

    public static function typeHintDataProvider(): Generator
    {
        $isPHPLessThan8 = \PHP_VERSION_ID < 80000;
        yield from [
            [ParentClass::class, '\Mockery\Tests\Mockery\ParentClass'],
            [ChildClass::class, '\Mockery\Tests\Mockery\ParentClass'],
            NullableObject::class => [NullableObject::class, $isPHPLessThan8 ? '?object' : 'object|null'],
        ];
    }

}

class ParentClass
{
    public function __invoke(self $arg): void
    {
    }
}

class ChildClass extends ParentClass
{
    public function __invoke(parent $arg): void
    {
    }
}

class NullableObject
{
    public function __invoke(?object $arg): void
    {
    }
}
