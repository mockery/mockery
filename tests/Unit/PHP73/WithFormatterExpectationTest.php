<?php

declare(strict_types=1);

namespace Tests\Unit\PHP73;

use Mockery;
use Mockery\Exception\NoMatchingExpectationException;
use PHP73\ClassWithGetter;
use PHP73\ClassWithGetterWithParam;
use PHP73\ClassWithPublicStaticGetter;
use PHP73\ClassWithPublicStaticProperty;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \Mockery
 */
final class WithFormatterExpectationTest extends TestCase
{
    public static function provideFormatObjectsCases(): iterable
    {
        return [[[null], ''], [['a string', 98768, ['a', 'nother', 'array']], '']];
    }

    /**
     * @dataProvider provideFormatObjectsCases
     */
    public function testFormatObjects($args, $expected): void
    {
        self::assertSame($expected, Mockery::formatObjects($args));
    }

    public function testFormatObjectsExcludesStaticGetters(): void
    {
        $obj = new ClassWithPublicStaticGetter();
        $string = Mockery::formatObjects([$obj]);

        self::assertSame(\mb_strpos($string, 'getExcluded'), false);
    }

    public function testFormatObjectsExcludesStaticProperties(): void
    {
        $obj = new ClassWithPublicStaticProperty();
        $string = Mockery::formatObjects([$obj]);

        self::assertSame(\mb_strpos($string, 'excludedProperty'), false);
    }

    public function testFormatObjectsShouldNotCallGettersWithParams(): void
    {
        $obj = new ClassWithGetterWithParam();
        $string = Mockery::formatObjects([$obj]);

        self::assertSame(\mb_strpos($string, 'Missing argument 1 for'), false);
    }

    /**
     * Note that without the patch checked in with this test, rather than throwing
     * an exception, the program will go into an infinite recursive loop
     */
    public function testFormatObjectsWithMockCalledInGetterDoesNotLeadToRecursion(): void
    {
        $mock = Mockery::mock(stdClass::class);
        $mock->shouldReceive('doBar')
            ->with('foo');
        $obj = new ClassWithGetter($mock);
        $this->expectException(NoMatchingExpectationException::class);
        $obj->getFoo();
    }
}
