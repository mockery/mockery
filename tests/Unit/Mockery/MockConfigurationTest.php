<?php

declare(strict_types=1);

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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Tests\Unit\Mockery;

use Iterator;
use IteratorAggregate;
use Mockery\Exception;
use Mockery\Generator\MockConfiguration;
use PHP73\ClassWithFinalMethod;
use PHP73\TestFinal;
use PHP73\TestInterface;
use PHP73\TestInterface2;
use PHP73\TestSubject;
use PHP73\TestTraversableInterface;
use PHP73\TestTraversableInterface2;
use PHP73\TestTraversableInterface3;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @coversDefaultClass \Mockery
 */
class MockConfigurationTest extends TestCase
{
    public function testBlackListedMethodsShouldNotBeInListToBeMocked(): void
    {
        $config = new MockConfiguration([TestSubject::class], ['foo']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('bar', $methods[0]->getName());
    }

    public function testBlackListsAreCaseInsensitive(): void
    {
        $config = new MockConfiguration([TestSubject::class], ['FOO']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('bar', $methods[0]->getName());
    }

    public function testFinalMethodsAreExcluded(): void
    {
        $config = new MockConfiguration([ClassWithFinalMethod::class]);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('bar', $methods[0]->getName());
    }

    public function testOnlyWhiteListedMethodsShouldBeInListToBeMocked(): void
    {
        $config = new MockConfiguration([TestSubject::class], [], ['foo']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }

    public function testShouldBringIteratorAggregateToHeadOfTargetListIfTraversablePresent(): void
    {
        $config = new MockConfiguration([TestTraversableInterface3::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(2, $interfaces);
        self::assertSame(IteratorAggregate::class, $interfaces[0]->getName());
        self::assertSame(TestTraversableInterface3::class, $interfaces[1]->getName());
    }

    public function testShouldBringIteratorToHeadOfTargetListIfTraversablePresent(): void
    {
        $config = new MockConfiguration([TestTraversableInterface2::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(2, $interfaces);
        self::assertSame(Iterator::class, $interfaces[0]->getName());
        self::assertSame(TestTraversableInterface2::class, $interfaces[1]->getName());
    }

    public function testShouldIncludeMethodsFromAllTargets(): void
    {
        $config = new MockConfiguration([TestInterface::class, TestInterface2::class]);
        $methods = $config->getMethodsToMock();
        self::assertCount(2, $methods);
    }

    public function testShouldTargetIteratorAggregateIfTraversableInTargetsTree(): void
    {
        $config = new MockConfiguration([TestTraversableInterface::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(2, $interfaces);
        self::assertSame(IteratorAggregate::class, $interfaces[0]->getName());
        self::assertSame(TestTraversableInterface::class, $interfaces[1]->getName());
    }

    public function testShouldTargetIteratorAggregateIfTryingToMockTraversable(): void
    {
        $config = new MockConfiguration([Traversable::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(1, $interfaces);
        $first = \array_shift($interfaces);
        self::assertSame(IteratorAggregate::class, $first->getName());
    }

    public function testShouldThrowIfTargetClassIsFinal(): void
    {
        $this->expectException(Exception::class);
        $config = new MockConfiguration([TestFinal::class]);
        $config->getTargetClass();
    }

    public function testWhiteListsAreCaseInsensitive(): void
    {
        $config = new MockConfiguration([TestSubject::class], [], ['FOO']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }

    public function testWhitelistOverRulesBlackList(): void
    {
        $config = new MockConfiguration([TestSubject::class], ['foo'], ['foo']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }
}
