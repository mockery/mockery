<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator;

use Mockery\Generator\MockConfigurationBuilder;
use PHP73\ClassWithDebugInfo;
use PHP73\ClassWithMagicCall;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class MockConfigurationBuilderTest extends TestCase
{
    public function testMagicMethodsAreBlackListedByDefault(): void
    {
        $builder = new MockConfigurationBuilder();
        $builder->addTarget(ClassWithMagicCall::class);
        $methods = $builder->getMockConfiguration()
            ->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertEquals('foo', $methods[0]->getName());
    }

    public function testReservedWordsAreBlackListedByDefault(): void
    {
        $builder = new MockConfigurationBuilder();

        self::assertContains('__halt_compiler', $builder->getMockConfiguration()->getBlackListedMethods());

        // need a builtin for this
        self::markTestSkipped('Need a builtin class with a method that is a reserved word');
    }

    public function testXDebugsDebugInfoIsBlackListedByDefault(): void
    {
        $builder = new MockConfigurationBuilder();
        $builder->addTarget(ClassWithDebugInfo::class);
        $methods = $builder->getMockConfiguration()
            ->getMethodsToMock();
        self::assertCount(1, $methods);
        self::assertEquals('foo', $methods[0]->getName());
    }
}
