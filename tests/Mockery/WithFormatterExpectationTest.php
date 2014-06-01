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
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

class WithFormatterExpectationTest extends PHPUnit_Framework_TestCase
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
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     *
     * Note that without the patch checked in with this test, rather than throwing
     * an exception, the program will go into an infinite recursive loop
     */
    public function testFormatObjectsWithMockCalledInGetterDoesNotLeadToRecursion()
    {
        $mock = Mockery::mock('stdClass');
        $mock->shouldReceive('doBar')->with('foo');
        $obj = new ClassWithGetter($mock);
        $obj->getFoo();
    }

    public function formatObjectsDataProvider()
    {
        return array(
            array(
                array(null),
                ''
            ),
            array(
                array('a string', 98768, array('a', 'nother', 'array')),
                ''
            )
        );
    }

}

class ClassWithGetter
{
    private $dep;

    function __construct($dep)
    {
        $this->dep = $dep;
    }
    public function getFoo()
    {
        return $this->dep->doBar('bar', $this);
    }
}
