<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Reflector;
use PHP73\ChildClass;
use PHP73\NullableObject;
use PHP73\ParentClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Throwable;

use const PHP_VERSION_ID;

/**
 * @coversDefaultClass \Mockery\Reflector
 */
final class ReflectorTest extends MockeryTestCase
{
    public static function provideGetTypeHintCases(): iterable
    {
        $isPHPLessThan8 = PHP_VERSION_ID < 80000;

        yield from [
            [ParentClass::class, '\\' . ParentClass::class],
            [ChildClass::class, '\\' . ParentClass::class],
            NullableObject::class => [NullableObject::class, $isPHPLessThan8 ? '?object' : 'object|null'],
        ];
    }

    public static function provideIsReservedWordCases(): iterable
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
     * @dataProvider provideGetTypeHintCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideGetTypeHintCases')]
    public function testGetTypeHint(string $class, string $expectedTypeHint): void
    {
        $refClass = new ReflectionClass($class);
        $refMethod = $refClass->getMethods()[0];
        $refParam = $refMethod->getParameters()[0];

        self::assertSame($expectedTypeHint, Reflector::getTypeHint($refParam));
    }

    /**
     * @dataProvider provideIsReservedWordCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideIsReservedWordCases')]
    public function testIsReservedWord(string $type): void
    {
        self::assertTrue(Reflector::isReservedWord($type));
    }
}
