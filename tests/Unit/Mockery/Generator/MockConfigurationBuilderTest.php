<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Generator;

use Mockery\Generator\MockConfigurationBuilder;
use PHP73\ClassWithDebugInfo;
use PHP73\ClassWithMagicCall;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class MockConfigurationBuilderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testMagicMethodsAreBlackListedByDefault(): void
    {
        $builder = new MockConfigurationBuilder();
        $builder->addTarget(ClassWithMagicCall::class);
        $methods = $builder->getMockConfiguration()
            ->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }

    /**
     * @throws Throwable
     */
    public function testReservedWordsAreBlackListedByDefault(): void
    {
        $builder = new MockConfigurationBuilder();

        self::assertContains('__halt_compiler', $builder->getMockConfiguration()->getBlackListedMethods());

        // need a builtin for this
        self::markTestSkipped('Need a builtin class with a method that is a reserved word');
    }

    /**
     * @throws Throwable
     */
    public function testXDebugsDebugInfoIsBlackListedByDefault(): void
    {
        $builder = new MockConfigurationBuilder();
        $builder->addTarget(ClassWithDebugInfo::class);
        $methods = $builder->getMockConfiguration()
            ->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertSame('foo', $methods[0]->getName());
    }
}
