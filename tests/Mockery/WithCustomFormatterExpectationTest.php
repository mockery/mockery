<?php
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

use PHPUnit\Framework\TestCase;

class WithCustomFormatterExpectationTest extends TestCase
{
    public function setUp(): void
    {
        \Mockery::getConfiguration()->setObjectFormatter(
            'ClassWithCustomFormatter',
            function ($object, $nesting) {
                return array(
                    "formatter" => 'ClassWithCustomFormatter',
                    "properties" => array(
                        "stringProperty" => $object->stringProperty
                    ),
                    "getters" => array(
                        "gettedProperty" => $object->getArrayProperty()
                    )
                );
            }
        );
        \Mockery::getConfiguration()->setObjectFormatter(
            'InterfaceWithCustomFormatter',
            function ($object, $nesting) {
                return array(
                    "formatter" => 'InterfaceWithCustomFormatter',
                    "properties" => array(
                        "stringProperty" => $object->stringProperty
                    ),
                    "getters" => array(
                        "gettedProperty" => $object->getArrayProperty()
                    )
                );
            }
        );
    }

    /**
     * @dataProvider getObjectFormatterDataProvider
     */
    public function testGetObjectFormatter($object, $expected)
    {
        $defaultFormatter = function ($class, $nesting) {
            return null;
        };

        $formatter = \Mockery::getConfiguration()->getObjectFormatter(get_class($object), $defaultFormatter);
        $formatted = $formatter($object, 1);

        $this->assertEquals(
            $expected,
            $formatted ? $formatted['formatter'] : null
        );
    }

    public static function getObjectFormatterDataProvider()
    {
        return array(
            array(
                new \StdClass(),
                null
            ),
            array(
                new ClassWithoutCustomFormatter(),
                null
            ),
            array(
                new ClassWithCustomFormatter(),
                'ClassWithCustomFormatter'
            ),
            array(
                new ClasschildOfWithCustomFormatter(),
                'ClassWithCustomFormatter'
            ),
            array(
                new ClassImplementsWithCustomFormatter(),
                'InterfaceWithCustomFormatter'
            )
        );
    }

    /**
     * @dataProvider formatObjectsDataProvider
     */
    public function testFormatObjects($obj, $shouldContains, $shouldNotContains)
    {
        $string = Mockery::formatObjects(array($obj));
        foreach ($shouldContains as $containString) {
            $this->assertStringContainsString($containString, $string);
        }
        foreach ($shouldNotContains as $containString) {
            $this->assertStringNotContainsString($containString, $string);
        }
    }

    public static function formatObjectsDataProvider()
    {
        return array(
            array(
                new ClassWithoutCustomFormatter(),
                array(
                    'stringProperty',
                    'numberProperty',
                    'arrayProperty'
                ),
                array(
                    'privateProperty'
                )
            ),
            array(
                new ClassWithCustomFormatter(),
                array(
                    'stringProperty',
                    'gettedProperty'
                ),
                array(
                    'numberProperty',
                    'privateProperty'
                )
            ),
            array(
                new ClassImplementsWithCustomFormatter(),
                array(
                    'stringProperty',
                    'gettedProperty'
                ),
                array(
                    'numberProperty',
                    'privateProperty'
                )
            ),
        );
    }
}

class ClassWithoutCustomFormatter
{
    public $stringProperty = "a string";
    public $numberProperty = 123;
    public $arrayProperty = array('a', 'nother', 'array');
    private $privateProperty = "private";
}

class ClassWithCustomFormatter
{
    public $stringProperty = "a string";
    public $numberProperty = 123;
    private $arrayProperty = array('a', 'nother', 'array');
    private $privateProperty = "private";

    public function getArrayProperty()
    {
        return $this->arrayProperty;
    }
}

class ClassChildOfWithCustomFormatter extends ClassWithCustomFormatter
{
}

interface InterfaceWithCustomFormatter
{
}

class ClassImplementsWithCustomFormatter implements InterfaceWithCustomFormatter
{
    public $stringProperty = "a string";
    public $numberProperty = 123;
    private $privateProperty = "private";
    private $arrayProperty = array('a', 'nother', 'array');

    public function getArrayProperty()
    {
        return $this->arrayProperty;
    }
}
