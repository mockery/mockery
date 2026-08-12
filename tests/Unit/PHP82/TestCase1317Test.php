<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP82;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;
use Mockery\Generator\DefinedTargetClass;
use Mockery\Generator\UndefinedTargetClass;
use PHP82\ReadonlyClass;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery\Expectation
 *
 * @requires PHP 8.2.0-dev
 *
 * @see https://github.com/mockery/mockery/issues/1317
 */
final class TestCase1317Test extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testCanNotMockReadonlyClasses(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The class \PHP82\ReadonlyClass is marked readonly');

        mock(ReadonlyClass::class);
    }

    /**
     * @throws Throwable
     */
    public function testDefinedTargetClassIsReadOnlyReturnsTrueForReadonlyClasses(): void
    {
        $definedTargetClass = DefinedTargetClass::factory(ReadonlyClass::class);

        self::assertTrue($definedTargetClass->isReadOnly());
    }

    /**
     * @throws Throwable
     */
    public function testUndefinedTargetClassIsReadOnlyReturnsFalseForReadonlyClasses(): void
    {
        /** @var class-string $className */
        $className = 'Undefined\\ReadonlyClass';

        $undefinedTargetClass = UndefinedTargetClass::factory($className);

        self::assertFalse($undefinedTargetClass->isReadOnly());
    }
}
