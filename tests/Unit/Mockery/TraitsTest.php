<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\SimpleTrait;
use PHP73\TraitWithAbstractMethod;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class TraitsTest extends MockeryTestCase
{
    public function testItCanCreateAnObjectForASimpleTrait(): void
    {
        $trait = mock(SimpleTrait::class);

        self::assertEquals('bar', $trait->foo());
    }

    public function testItCanCreateAnObjectUsingMultipleTraits(): void
    {
        $trait = mock(SimpleTrait::class, TraitWithAbstractMethod::class, [
            'doBaz' => 123,
        ]);

        self::assertEquals('bar', $trait->foo());
        self::assertEquals(123, $trait->baz());
    }

    public function testItCreatesAbstractMethodsAsNecessary(): void
    {
        $trait = mock(TraitWithAbstractMethod::class, [
            'doBaz' => 'baz',
        ]);

        self::assertEquals('baz', $trait->baz());
    }
}
