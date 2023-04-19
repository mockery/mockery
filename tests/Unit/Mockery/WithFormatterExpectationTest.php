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
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery;
use Mockery\Exception\NoMatchingExpectationException;
use MockeryTest\Fixture\ClassWithGetter;
use MockeryTest\Fixture\ClassWithGetterWithParam;
use MockeryTest\Fixture\ClassWithPublicStaticGetter;
use MockeryTest\Fixture\ClassWithPublicStaticProperty;
use PHPUnit\Framework\TestCase;
use function mb_strpos;

class WithFormatterExpectationTest extends TestCase
{
    /**
     * @dataProvider formatObjectsDataProvider
     */
    public function testFormatObjects($args, $expected)
    {
        $this->assertEquals(
            $expected,
            Mockery::formatObjects($args)
        );
    }

    /**
     * Note that without the patch checked in with this test, rather than throwing
     * an exception, the program will go into an infinite recursive loop
     */
    public function testFormatObjectsWithMockCalledInGetterDoesNotLeadToRecursion()
    {
        $mock = Mockery::mock(\stdClass::class);
        $mock->shouldReceive('doBar')->with('foo');
        $obj = new ClassWithGetter($mock);
        $this->expectException(NoMatchingExpectationException::class);
        $obj->getFoo();
    }

    public function formatObjectsDataProvider()
    {
        return [
            [
                [null],
                ''
            ],
            [
                ['a string', 98768, ['a', 'nother', 'array']],
                ''
            ],
        ];
    }

    /** @test */
    public function format_objects_should_not_call_getters_with_params()
    {
        $obj = new ClassWithGetterWithParam();
        $string = Mockery::formatObjects([$obj]);

        $this->assertTrue(mb_strpos($string, 'Missing argument 1 for') === false);
    }

    public function testFormatObjectsExcludesStaticProperties()
    {
        $obj = new ClassWithPublicStaticProperty();
        $string = Mockery::formatObjects([$obj]);

        $this->assertTrue(mb_strpos($string, 'excludedProperty') === false);
    }

    public function testFormatObjectsExcludesStaticGetters()
    {
        $obj = new ClassWithPublicStaticGetter();
        $string = Mockery::formatObjects([$obj]);

        $this->assertTrue(mb_strpos($string, 'getExcluded') === false);
    }
}
