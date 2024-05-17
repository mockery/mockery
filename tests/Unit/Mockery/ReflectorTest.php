<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Generator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Reflector;
use PHP73\ChildClass;
use PHP73\NullableObject;
use PHP73\ParentClass;
use ReflectionClass;

use const PHP_VERSION_ID;

/**
 * @coversDefaultClass \Mockery\Reflector
 */
final class ReflectorTest extends MockeryTestCase
{
    public static function provideReservedWords(): Generator
    {
        foreach ([
            'bool',
            'false',
            'float',
            'int',
            'iterable',
            'mixed',
            'never',
            'null',
            'object',
            'string',
            'true',
            'void',
        ] as $type) {
            yield $type => [$type];
        }
    }

    /**
     * @dataProvider typeHintDataProvider
     */
    public function testGetTypeHint(string $class, string $expectedTypeHint): void
    {
        $refClass = new ReflectionClass($class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame($expectedTypeHint, Reflector::getTypeHint($refParam));
    }

    /**
     * @dataProvider provideReservedWords
     */
    public function testIsReservedWord(string $type): void
    {
        self::assertTrue(Reflector::isReservedWord($type));
    }

    public static function typeHintDataProvider(): Generator
    {
        $isPHPLessThan8 = PHP_VERSION_ID < 80000;

        yield from [
            [ParentClass::class, '\\' . ParentClass::class],
            [ChildClass::class, '\\' . ParentClass::class],
            NullableObject::class => [NullableObject::class, $isPHPLessThan8 ? '?object' : 'object|null'],
        ];
    }
}
