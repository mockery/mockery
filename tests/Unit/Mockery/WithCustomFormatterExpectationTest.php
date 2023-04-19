<?php

namespace MockeryTest\Unit\Mockery;

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category     Mockery
 * @package        Mockery
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license        http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery;
use MockeryTest\Fixture\ClassChildOfWithCustomFormatter;
use MockeryTest\Fixture\ClassImplementsWithCustomFormatter;
use MockeryTest\Fixture\ClassWithCustomFormatter;
use MockeryTest\Fixture\ClassWithoutCustomFormatter;
use MockeryTest\Fixture\InterfaceWithCustomFormatter;
use PHPUnit\Framework\TestCase;
use StdClass;
use function get_class;

class WithCustomFormatterExpectationTest extends TestCase
{
    public function setUp(): void
    {
        Mockery::getConfiguration()->setObjectFormatter(
            ClassWithCustomFormatter::class,
            static function ($object, $nesting) {
                return [
                    'formatter' => ClassWithCustomFormatter::class,
                    'properties' => [
                        'stringProperty' => $object->stringProperty
                    ],
                    'getters' => [
                        'gettedProperty' => $object->getArrayProperty()
                    ]
                ];
            }
        );

        Mockery::getConfiguration()->setObjectFormatter(
            InterfaceWithCustomFormatter::class,
            static function ($object, $nesting) {
                return [
                    'formatter' => InterfaceWithCustomFormatter::class,
                    'properties' => [
                        'stringProperty' => $object->stringProperty
                    ],
                    'getters' => [
                        'gettedProperty' => $object->getArrayProperty()
                    ]
                ];
            }
        );
    }

    /**
     * @dataProvider getObjectFormatterDataProvider
     */
    public function testGetObjectFormatter($object, $expected)
    {
        $defaultFormatter = static function ($class, $nesting) {
            return null;
        };

        $formatter = Mockery::getConfiguration()->getObjectFormatter(get_class($object), $defaultFormatter);
        $formatted = $formatter($object, 1);

        $this->assertEquals(
            $expected,
            $formatted ? $formatted['formatter'] : null
        );
    }

    public function getObjectFormatterDataProvider()
    {
        return [
            [
                new StdClass(),
                null
            ],
            [
                new ClassWithoutCustomFormatter(),
                null
            ],
            [
                new ClassWithCustomFormatter(),
                ClassWithCustomFormatter::class
            ],
            [
                new ClassChildOfWithCustomFormatter(),
                ClassWithCustomFormatter::class
            ],
            [
                new ClassImplementsWithCustomFormatter(),
                InterfaceWithCustomFormatter::class
            ]
        ];
    }

    /**
     * @dataProvider formatObjectsDataProvider
     */
    public function testFormatObjects($obj, $shouldContains, $shouldNotContains)
    {
        $string = Mockery::formatObjects([$obj]);
        foreach ($shouldContains as $containString) {
            $this->assertStringContainsString($containString, $string);
        }
        foreach ($shouldNotContains as $containString) {
            $this->assertStringNotContainsString($containString, $string);
        }
    }

    public function formatObjectsDataProvider()
    {
        return [
            [
                new ClassWithoutCustomFormatter(),
                [
                    'stringProperty',
                    'numberProperty',
                    'arrayProperty'
                ],
                [
                    'privateProperty'
                ]
            ],
            [
                new ClassWithCustomFormatter(),
                [
                    'stringProperty',
                    'gettedProperty'
                ],
                [
                    'numberProperty',
                    'privateProperty'
                ]
            ],
            [
                new ClassImplementsWithCustomFormatter(),
                [
                    'stringProperty',
                    'gettedProperty'
                ],
                [
                    'numberProperty',
                    'privateProperty'
                ]
            ],
        ];
    }
}
