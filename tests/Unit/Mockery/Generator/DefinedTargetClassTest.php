<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator;

use ArrayObject;
use Mockery\Generator\DefinedTargetClass;
use PHP73\MockeryTest_ClassThatExtendsArrayObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass \Mockery
 */
final class DefinedTargetClassTest extends TestCase
{
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
