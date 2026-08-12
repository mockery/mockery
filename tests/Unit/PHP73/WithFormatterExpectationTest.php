<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP73;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\NoMatchingExpectationException;
use PHP73\ClassWithGetter;
use PHP73\ClassWithGetterWithParam;
use PHP73\ClassWithPublicStaticGetter;
use PHP73\ClassWithPublicStaticProperty;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Throwable;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class WithFormatterExpectationTest extends MockeryTestCase
{
    public static function provideFormatObjectsCases(): iterable
    {
        return [[[null], ''], [['a string', 98768, ['a', 'nother', 'array']], '']];
    }

    /**
     * @dataProvider provideFormatObjectsCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideFormatObjectsCases')]
    public function testFormatObjects($args, $expected): void
    {
        self::assertSame($expected, Mockery::formatObjects($args));
    }

    /**
     * @throws Throwable
     */
    public function testFormatObjectsExcludesStaticGetters(): void
    {
        $obj = new ClassWithPublicStaticGetter();
        $string = Mockery::formatObjects([$obj]);

        self::assertSame(mb_strpos($string, 'getExcluded'), false);
    }

    /**
     * @throws Throwable
     */
    public function testFormatObjectsExcludesStaticProperties(): void
    {
        $obj = new ClassWithPublicStaticProperty();
        $string = Mockery::formatObjects([$obj]);

        self::assertSame(mb_strpos($string, 'excludedProperty'), false);
    }

    /**
     * @throws Throwable
     */
    public function testFormatObjectsShouldNotCallGettersWithParams(): void
    {
        $obj = new ClassWithGetterWithParam();
        $string = Mockery::formatObjects([$obj]);

        self::assertSame(mb_strpos($string, 'Missing argument 1 for'), false);
    }

    /**
     * Note that without the patch checked in with this test, rather than throwing
     * an exception, the program will go into an infinite recursive loop
     *
     * @throws Throwable
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
