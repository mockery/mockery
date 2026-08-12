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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\SimpleTrait;
use PHP73\TraitWithAbstractMethod;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class TraitsTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItCanCreateAnObjectForASimpleTrait(): void
    {
        $trait = mock(SimpleTrait::class);

        self::assertSame('bar', $trait->foo());
    }

    /**
     * @throws Throwable
     */
    public function testItCanCreateAnObjectUsingMultipleTraits(): void
    {
        $trait = mock(SimpleTrait::class, TraitWithAbstractMethod::class, [
            'doBaz' => 123,
        ]);

        self::assertSame('bar', $trait->foo());
        self::assertSame(123, $trait->baz());
    }

    /**
     * @throws Throwable
     */
    public function testItCreatesAbstractMethodsAsNecessary(): void
    {
        $trait = mock(TraitWithAbstractMethod::class, [
            'doBaz' => 'baz',
        ]);

        self::assertSame('baz', $trait->baz());
    }
}
