<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Generator;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\ClassName;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Throwable;

use function get_class;

final class ClassNameTest extends MockeryTestCase
{
    /**
     * @return iterable<string,array{0: bool, 1: string}>
     */
    public static function provideClassNameIsAnonymousClassCases(): iterable
    {
        $class = new class {};
        $parent = new class extends stdClass {};
        yield from [
            'empty string' => [false, ''],
            'just a space' => [false, ' '],
            'namespaced interface' => [false, Throwable::class],
            'namespaced class' => [false, stdClass::class],
            'namespaced class name' => [false, ClassName::class],
            'anonymous class' => [true, get_class($class)],
            'anonymous class with parent' => [true, get_class($parent)],
            'anonymous cn' => [true, 'class@anonymous'."\0".'path:0$0'],
            'anonymous qcn' => [true, 'MyNamespace\Myclass@anonymous'."\0".'path:0$0'],
            'anonymous fqcn' => [true, '\MyNamespace\Myclass@anonymous'."\0".'path:0$0'],
        ];
    }

    /**
     * @return iterable<string,array{0: bool, 1: string}>
     */
    public static function provideClassNameIsClassCases(): iterable
    {
        yield from [
            'empty string' => [false, ''],
            'just a space' => [false, ' '],
            'class name with dot' => [false, 'ClassName.WithDot'],
            'too many backslashes' => [false, '\\\\TooManyBackSlashes'],
            'global class name' => [true, 'Foo'],
            'namespaced interface' => [true, Throwable::class],
            'namespaced class' => [true, stdClass::class],
            'namespaced class name' => [true, ClassName::class],
        ];
    }

    /**
     * @dataProvider provideClassNameIsAnonymousClassCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideClassNameIsAnonymousClassCases')]
    public function testClassNameIsAnonymousClass(bool $expected, string $className): void
    {
        self::assertSame($expected, ClassName::isAnonymousClass($className));
    }

    /**
     * @dataProvider provideClassNameIsClassCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideClassNameIsClassCases')]
    public function testClassNameIsClass(bool $expected, string $className): void
    {
        self::assertSame($expected, ClassName::isClass($className));
    }
}
