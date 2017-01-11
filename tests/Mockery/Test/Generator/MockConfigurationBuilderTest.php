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
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Generator;

use Mockery as m;
use Mockery\Generator\MockConfigurationBuilder;

class MockConfigurationBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function reservedWordsAreBlackListedByDefault()
    {
        $builder = new MockConfigurationBuilder;
        $this->assertContains('__halt_compiler', $builder->getMockConfiguration()->getBlackListedMethods());

        // need a builtin for this
        $this->markTestSkipped("Need a builtin class with a method that is a reserved word");
    }

    /**
     * @test
     */
    public function magicMethodsAreBlackListedByDefault()
    {
        $builder = new MockConfigurationBuilder;
        $builder->addTarget("Mockery\Generator\ClassWithMagicCall");
        $methods = $builder->getMockConfiguration()->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }
}

class ClassWithMagicCall
{
    public function foo()
    {
    }
    public function __call($method, $args)
    {
    }
}
