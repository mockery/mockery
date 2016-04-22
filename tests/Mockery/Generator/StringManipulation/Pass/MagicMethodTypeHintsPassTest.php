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

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\DefinedTargetClass;
use Mockery\Generator\StringManipulation\Pass\MagicMethodTypeHintsPass;

class MagicMethodTypeHintsPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MagicMethodTypeHintsPass
     */
    private $pass;

    /**
     * @var MockConfiguration
     */
    private $mockedConfiguration;

    /**
     * Setup method
     * @return void
     */
    public function setup()
    {
        $this->pass = new MagicMethodTypeHintsPass;
        $this->mockedConfiguration = m::mock(
            'Mockery\Generator\MockConfiguration'
        );
    }

    /**
     * @test
     */
    public function itShouldWork()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function itShouldGrabClassMagicMethods()
    {
        $targetClass = DefinedTargetClass::factory(
            'Mockery\Test\Generator\StringManipulation\Pass\MagicDummy'
        );
        $magicMethods = $this->pass->getMagicMethods($targetClass);

        $this->assertCount(6, $magicMethods);
        $this->assertEquals('__isset', $magicMethods[0]->getName());
    }

    /**
     * @test
     */
    public function itShouldAddStringTypeHintOnMagicMethod()
    {
        $targetClass = DefinedTargetClass::factory(
            'Mockery\Test\Generator\StringManipulation\Pass\MagicDummy'
        );
        $this->mockedConfiguration
             ->shouldReceive('getTargetClass')
             ->andReturn($targetClass);

        $code = $this->pass->apply(
            'public function __isset($name) {}',
            $this->mockedConfiguration
        );
        $this->assertContains('string $name', $code);
    }

    /**
     * @test
     */
    public function itShouldAddBooleanReturnOnMagicMethod()
    {
        $targetClass = DefinedTargetClass::factory(
            'Mockery\Test\Generator\StringManipulation\Pass\MagicDummy'
        );
        $this->mockedConfiguration
             ->shouldReceive('getTargetClass')
             ->andReturn($targetClass);

        $code = $this->pass->apply(
            'public function __isset($name) {}',
            $this->mockedConfiguration
        );
        $this->assertContains(' : bool', $code);
    }

    /**
     * @test
     */
    public function itShouldAddTypeHintsOnToStringMethod()
    {
        $targetClass = DefinedTargetClass::factory(
            'Mockery\Test\Generator\StringManipulation\Pass\MagicDummy'
        );
        $this->mockedConfiguration
             ->shouldReceive('getTargetClass')
             ->andReturn($targetClass);

        $code = $this->pass->apply(
            'public function __toString() {}',
            $this->mockedConfiguration
        );
        $this->assertContains(' : string', $code);
    }
}

class MagicDummy
{
    public function __isset(string $name) : bool
    {
        return false;
    }

    public function __tostring() : string
    {
        return '';
    }

    public function __wakeup()
    {
    }

    public function __destruct()
    {
    }

    public function __call(string $name, array $arguments) : string
    {
    }

    public function __callStatic(string $name, array $arguments) : int
    {
    }

    public function nonMagicMethod()
    {
    }
}
