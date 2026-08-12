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

use ArrayObject;
use Mockery\Generator\DefinedTargetClass;
use PHP73\MockeryTest_ClassThatExtendsArrayObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class DefinedTargetClassTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testItKnowsIfOneOfItsAncestorsIsInternal(): void
    {
        $target = new DefinedTargetClass(new ReflectionClass(ArrayObject::class));
        self::assertTrue($target->hasInternalAncestor());

        $target = new DefinedTargetClass(new ReflectionClass(MockeryTest_ClassThatExtendsArrayObject::class));
        self::assertTrue($target->hasInternalAncestor());

        $target = new DefinedTargetClass(new ReflectionClass(self::class));
        self::assertFalse($target->hasInternalAncestor());
    }
}
