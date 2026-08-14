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
use Override;
use PHP73\ClassChildOfWithCustomFormatter;
use PHP73\ClassImplementsWithCustomFormatter;
use PHP73\ClassWithCustomFormatter;
use PHP73\ClassWithoutCustomFormatter;
use PHP73\InterfaceWithCustomFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Throwable;

use function get_class;

/**
 * @coversDefaultClass \Mockery
 */
final class WithCustomFormatterExpectationTest extends MockeryTestCase
{
    #[Override]
    protected function mockeryTestSetUp()
    {
        Mockery::getConfiguration()->setObjectFormatter(
            ClassWithCustomFormatter::class,
            function ($object, $nesting) {
                return [
                    'formatter' => ClassWithCustomFormatter::class,
                    'properties' => [
                        'stringProperty' => $object->stringProperty,
                    ],
                    'getters' => [
                        'gettedProperty' => $object->getArrayProperty(),
                    ],
                ];
            }
        );
        Mockery::getConfiguration()->setObjectFormatter(
            InterfaceWithCustomFormatter::class,
            function ($object, $nesting) {
                return [
                    'formatter' => InterfaceWithCustomFormatter::class,
                    'properties' => [
                        'stringProperty' => $object->stringProperty,
                    ],
                    'getters' => [
                        'gettedProperty' => $object->getArrayProperty(),
                    ],
                ];
            }
        );
    }

    public static function provideFormatObjectsCases(): iterable
    {
        return [
            [
                new ClassWithoutCustomFormatter(),
                ['stringProperty', 'numberProperty', 'arrayProperty'],
                ['privateProperty'],
            ],
            [
                new ClassWithCustomFormatter(),
                ['stringProperty', 'gettedProperty'],
                ['numberProperty', 'privateProperty'],
            ],
            [
                new ClassImplementsWithCustomFormatter(),
                ['stringProperty', 'gettedProperty'],
                ['numberProperty', 'privateProperty'],
            ],
        ];
    }

    public static function provideGetObjectFormatterCases(): iterable
    {
        return [
            [new stdClass(), null],
            [new ClassWithoutCustomFormatter(), null],
            [new ClassWithCustomFormatter(), ClassWithCustomFormatter::class],
            [new ClassChildOfWithCustomFormatter(), ClassWithCustomFormatter::class],
            [new ClassImplementsWithCustomFormatter(), InterfaceWithCustomFormatter::class],
        ];
    }

    /**
     * @dataProvider provideFormatObjectsCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideFormatObjectsCases')]
    public function testFormatObjects($obj, $shouldContains, $shouldNotContains): void
    {
        $string = Mockery::formatObjects([$obj]);
        foreach ($shouldContains as $containString) {
            self::assertStringContainsString($containString, $string);
        }
        foreach ($shouldNotContains as $containString) {
            self::assertStringNotContainsString($containString, $string);
        }
    }

    /**
     * @dataProvider provideGetObjectFormatterCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideGetObjectFormatterCases')]
    public function testGetObjectFormatter($object, $expected): void
    {
        $defaultFormatter = function ($class, $nesting): ?array {
            return null;
        };

        $formatter = Mockery::getConfiguration()->getObjectFormatter(get_class($object), $defaultFormatter);
        $formatted = $formatter($object, 1);

        self::assertSame($expected, $formatted ? $formatted['formatter'] : null);
    }
}
