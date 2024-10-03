<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\SimpleTrait;
use PHP73\TraitWithAbstractMethod;

/**
 * @coversDefaultClass \Mockery
 */
final class TraitsTest extends MockeryTestCase
{
    public function testItCanCreateAnObjectForASimpleTrait(): void
    {
        $trait = \mock(SimpleTrait::class);

        self::assertSame('bar', $trait->foo());
    }

    public function testItCanCreateAnObjectUsingMultipleTraits(): void
    {
        $trait = \mock(SimpleTrait::class, TraitWithAbstractMethod::class, [
            'doBaz' => 123,
        ]);

        self::assertSame('bar', $trait->foo());
        self::assertSame(123, $trait->baz());
    }

    public function testItCreatesAbstractMethodsAsNecessary(): void
    {
        $trait = \mock(TraitWithAbstractMethod::class, [
            'doBaz' => 'baz',
        ]);

        self::assertSame('baz', $trait->baz());
    }
}
