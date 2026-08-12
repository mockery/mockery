<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
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
use Throwable;
use Traversable;

use function array_shift;

/**
 * @coversDefaultClass \Mockery
 */
class MockConfigurationTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testBlackListedMethodsShouldNotBeInListToBeMocked(): void
    {
        $config = new MockConfiguration([TestSubject::class], ['foo']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('bar', $methods[0]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testBlackListsAreCaseInsensitive(): void
    {
        $config = new MockConfiguration([TestSubject::class], ['FOO']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('bar', $methods[0]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testFinalMethodsAreExcluded(): void
    {
        $config = new MockConfiguration([ClassWithFinalMethod::class]);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('bar', $methods[0]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testOnlyWhiteListedMethodsShouldBeInListToBeMocked(): void
    {
        $config = new MockConfiguration([TestSubject::class], [], ['foo']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testShouldBringIteratorAggregateToHeadOfTargetListIfTraversablePresent(): void
    {
        $config = new MockConfiguration([TestTraversableInterface3::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(2, $interfaces);
        self::assertSame(IteratorAggregate::class, $interfaces[0]->getName());
        self::assertSame(TestTraversableInterface3::class, $interfaces[1]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testShouldBringIteratorToHeadOfTargetListIfTraversablePresent(): void
    {
        $config = new MockConfiguration([TestTraversableInterface2::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(2, $interfaces);
        self::assertSame(Iterator::class, $interfaces[0]->getName());
        self::assertSame(TestTraversableInterface2::class, $interfaces[1]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testShouldIncludeMethodsFromAllTargets(): void
    {
        $config = new MockConfiguration([TestInterface::class, TestInterface2::class]);
        $methods = $config->getMethodsToMock();
        self::assertCount(2, $methods);
    }

    /**
     * @throws Throwable
     */
    public function testShouldTargetIteratorAggregateIfTraversableInTargetsTree(): void
    {
        $config = new MockConfiguration([TestTraversableInterface::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(2, $interfaces);
        self::assertSame(IteratorAggregate::class, $interfaces[0]->getName());
        self::assertSame(TestTraversableInterface::class, $interfaces[1]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testShouldTargetIteratorAggregateIfTryingToMockTraversable(): void
    {
        $config = new MockConfiguration([Traversable::class]);

        $interfaces = $config->getTargetInterfaces();
        self::assertCount(1, $interfaces);
        $first = array_shift($interfaces);
        self::assertSame(IteratorAggregate::class, $first->getName());
    }

    /**
     * @throws Throwable
     */
    public function testShouldThrowIfTargetClassIsFinal(): void
    {
        $this->expectException(Exception::class);
        $config = new MockConfiguration([TestFinal::class]);
        $config->getTargetClass();
    }

    /**
     * @throws Throwable
     */
    public function testWhiteListsAreCaseInsensitive(): void
    {
        $config = new MockConfiguration([TestSubject::class], [], ['FOO']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testWhitelistOverRulesBlackList(): void
    {
        $config = new MockConfiguration([TestSubject::class], ['foo'], ['foo']);

        $methods = $config->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }
}
