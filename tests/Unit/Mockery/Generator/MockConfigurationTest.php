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

namespace MockeryTest\Unit\Mockery\Generator;

use Mockery\Exception;
use Mockery\Generator\MockConfiguration;
use MockeryTest\Fixture\Generator\ClassWithFinalMethod;
use MockeryTest\Fixture\Generator\TestFinal;
use MockeryTest\Fixture\Generator\TestInterface;
use MockeryTest\Fixture\Generator\TestInterface2;
use MockeryTest\Fixture\Generator\TestSubject;
use MockeryTest\Fixture\Generator\TestTraversableInterface;
use MockeryTest\Fixture\Generator\TestTraversableInterface2;
use MockeryTest\Fixture\Generator\TestTraversableInterface3;
use PHPUnit\Framework\TestCase;
use function array_shift;

class MockConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function blackListedMethodsShouldNotBeInListToBeMocked()
    {
        $config = new MockConfiguration([TestSubject::class], ['foo']);

        $methods = $config->getMethodsToMock();
        $this->assertCount(1, $methods);
        $this->assertEquals('bar', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function blackListsAreCaseInsensitive()
    {
        $config = new MockConfiguration([TestSubject::class], ['FOO']);

        $methods = $config->getMethodsToMock();
        $this->assertCount(1, $methods);
        $this->assertEquals('bar', $methods[0]->getName());
    }


    /**
     * @test
     */
    public function onlyWhiteListedMethodsShouldBeInListToBeMocked()
    {
        $config = new MockConfiguration([TestSubject::class], [], ['foo']);

        $methods = $config->getMethodsToMock();
        $this->assertCount(1, $methods);
        $this->assertEquals('foo', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function whitelistOverRulesBlackList()
    {
        $config = new MockConfiguration([TestSubject::class], ['foo'], ['foo']);

        $methods = $config->getMethodsToMock();
        $this->assertCount(1, $methods);
        $this->assertEquals('foo', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function whiteListsAreCaseInsensitive()
    {
        $config = new MockConfiguration([TestSubject::class], [], ['FOO']);

        $methods = $config->getMethodsToMock();
        $this->assertCount(1, $methods);
        $this->assertEquals('foo', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function finalMethodsAreExcluded()
    {
        $config = new MockConfiguration([ClassWithFinalMethod::class]);

        $methods = $config->getMethodsToMock();
        $this->assertCount(1, $methods);
        $this->assertEquals('bar', $methods[0]->getName());
    }

    /**
     * @test
     */
    public function shouldIncludeMethodsFromAllTargets()
    {
        $config = new MockConfiguration([TestInterface::class, TestInterface2::class]);
        $methods = $config->getMethodsToMock();
        $this->assertCount(2, $methods);
    }

    /**
     * @test
     */
    public function shouldThrowIfTargetClassIsFinal()
    {
        $this->expectException(Exception::class);
        $config = new MockConfiguration([TestFinal::class]);
        $config->getTargetClass();
    }

    /**
     * @test
     */
    public function shouldTargetIteratorAggregateIfTryingToMockTraversable()
    {
        $config = new MockConfiguration([\Traversable::class]);

        $interfaces = $config->getTargetInterfaces();
        $this->assertCount(1, $interfaces);
        $first = array_shift($interfaces);
        $this->assertEquals(\IteratorAggregate::class, $first->getName());
    }

    /**
     * @test
     */
    public function shouldTargetIteratorAggregateIfTraversableInTargetsTree()
    {
        $config = new MockConfiguration([TestTraversableInterface::class]);

        $interfaces = $config->getTargetInterfaces();
        $this->assertCount(2, $interfaces);
        $this->assertEquals(\IteratorAggregate::class, $interfaces[0]->getName());
        $this->assertEquals(TestTraversableInterface::class, $interfaces[1]->getName());
    }

    /**
     * @test
     */
    public function shouldBringIteratorToHeadOfTargetListIfTraversablePresent()
    {
        $config = new MockConfiguration([TestTraversableInterface2::class]);

        $interfaces = $config->getTargetInterfaces();
        $this->assertCount(2, $interfaces);
        $this->assertEquals(\Iterator::class, $interfaces[0]->getName());
        $this->assertEquals(TestTraversableInterface2::class, $interfaces[1]->getName());
    }

    /**
     * @test
     */
    public function shouldBringIteratorAggregateToHeadOfTargetListIfTraversablePresent()
    {
        $config = new MockConfiguration([TestTraversableInterface3::class]);

        $interfaces = $config->getTargetInterfaces();
        $this->assertCount(2, $interfaces);
        $this->assertEquals(\IteratorAggregate::class, $interfaces[0]->getName());
        $this->assertEquals(TestTraversableInterface3::class, $interfaces[1]->getName());
    }
}
