<?php

declare(strict_types=1);

namespace test\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;

/**
 * @requires PHP 8.3.0-dev
 */
class Php83LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * Enumerations ( enum ) are final classes and therefore cannot be mocked.
     */
    public function testCanNotMockEnumsFinalClasses(): void
    {
        $this->expectException(Exception::class);

        mock(Enums::class);
    }

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
}

enum Enums {
    const string FOO = "bar";
}

class Classes implements Interfaces {
    use Traits;
}

interface Interfaces {
    const string BAR = Enums::FOO;

    public function foo(): string;
}

trait Traits {
    const string BAR = Enums::FOO;

    public function foo(): string
    {
        return Interfaces::BAR;
    }
}

class ClassName {
    public const CONSTANT = 42;
}
