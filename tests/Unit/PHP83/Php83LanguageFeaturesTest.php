<?php

declare(strict_types=1);

namespace Mockery\Tests\Unit\PHP83;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;
use PHP83\Classes;
use PHP83\ClassName;
use PHP83\Enums;
use PHP83\Interfaces;
use PHP83\Traits;

/**
 * @requires PHP 8.3.0-dev
 */
final class Php83LanguageFeaturesTest extends MockeryTestCase
{
    public function testCanMockClassTypedClassConstants(): void
    {
        $mock = mock(Classes::class);

        self::assertInstanceOf(Classes::class, $mock);
        self::assertSame(Enums::FOO, $mock::BAR);
    }

    public function testCanMockInterfaceTypedClassConstants(): void
    {
        $mock = mock(Interfaces::class);

        self::assertInstanceOf(Interfaces::class, $mock);
        self::assertSame(Enums::FOO, $mock::BAR);
    }

    public function testCanMockTraitTypedClassConstants(): void
    {
        $mock = mock(Traits::class);

        self::assertSame(Enums::FOO, $mock->foo());
        self::assertSame(Enums::FOO, $mock::BAR);
    }

    public function testCanMockWithDynamicClassConstantFetch(): void
    {
        $mock = mock(ClassName::class);

        $constant = 'CONSTANT';

        self::assertSame(ClassName::CONSTANT, $mock::CONSTANT);
        self::assertSame(ClassName::CONSTANT, $mock::{$constant});
        self::assertSame(ClassName::{$constant}, $mock::CONSTANT);
        self::assertSame(ClassName::{$constant}, $mock::{$constant});
    }

    /**
     * Enumerations ( enum ) are final classes and therefore cannot be mocked.
     */
    public function testCanNotMockEnumsFinalClasses(): void
    {
        $this->expectException(Exception::class);

        mock(Enums::class);
    }
}
