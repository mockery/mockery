<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP84;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP84\PropertyHooks\AbstractClassWithPropertyHooks;
use PHP84\PropertyHooks\ClassWithoutPropertyHooksExtendingAbstractClassWithPropertyHooks;
use PHP84\PropertyHooks\ClassWithoutPropertyHooksImplementingInterfaceWithPropertyHooks;
use PHP84\PropertyHooks\ClassWithPropertyHooksExtendingAbstractClassWithPropertyHooks;
use PHP84\PropertyHooks\ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks;
use PHP84\PropertyHooks\InterfaceWithPropertyHooks;
use Throwable;

/**
 * @requires PHP 8.4.0-dev
 *
 * @coversDefaultClass \Mockery
 */
final class Php84LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testCanMockAbstractClassGetAndSetPropertyHooks(): void
    {
        $mock = Mockery::mock(AbstractClassWithPropertyHooks::class);

        $mock->expects('$readableAndWriteable::set')->with('qux');
        $mock->expects('$readableAndWriteable::get')->andReturn('baz');

        $mock->readableAndWriteable = 'qux';

        self::assertSame('baz', $mock->readableAndWriteable);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockAbstractClassGetPropertyHook(): void
    {
        $mock = Mockery::mock(AbstractClassWithPropertyHooks::class);

        $mock->expects('$readable::get')->andReturn('bar');

        self::assertSame('bar', $mock->readable);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockAbstractClassProtectedGetAndSetPropertyHooks(): void
    {
        $mock = Mockery::mock(AbstractClassWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedReadableAndWriteable::set')->with('qux');
        $mock->expects('$protectedReadableAndWriteable::get')->andReturn('baz');

        $mock->setProtectedReadableAndWriteable('qux');

        self::assertSame('baz', $mock->getProtectedReadableAndWriteable());
    }

    /**
     * @throws Throwable
     */
    public function testCanMockAbstractClassProtectedGetPropertyHook(): void
    {
        $mock = Mockery::mock(AbstractClassWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedReadable::get')->andReturn('bar');

        self::assertSame('bar', $mock->getProtectedReadable());
    }

    /**
     * @throws Throwable
     */
    public function testCanMockAbstractClassProtectedSetPropertyHook(): void
    {
        $mock = Mockery::mock(AbstractClassWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedWriteable::set')->with('quz');

        $mock->setProtectedWriteable('quz');
    }

    /**
     * @throws Throwable
     */
    public function testCanMockAbstractClassSetPropertyHook(): void
    {
        $mock = Mockery::mock(AbstractClassWithPropertyHooks::class);

        $mock->expects('$writeable::set')->with('quz');

        $mock->writeable = 'quz';
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksExtendingAbstractClassPublicGetAndSetHooks(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksExtendingAbstractClassWithPropertyHooks::class);

        $mock->expects('$readableAndWriteable::get')->andReturn('baz');
        $mock->expects('$readableAndWriteable::set')->with('qux');

        self::assertSame('baz', $mock->readableAndWriteable);
        $mock->readableAndWriteable = 'qux';
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksExtendingAbstractClassPublicGetHook(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksExtendingAbstractClassWithPropertyHooks::class);

        $mock->expects('$readable::get')->andReturn('bar');

        self::assertSame('bar', $mock->readable);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksExtendingAbstractClassPublicSetHook(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksExtendingAbstractClassWithPropertyHooks::class);

        $mock->expects('$writeable::set')->with('quz');

        $mock->writeable = 'quz';
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksImplementingInterfaceProtectedGetAndSetHooks(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedReadableAndWriteable::get')->andReturn('baz');
        $mock->expects('$protectedReadableAndWriteable::set')->with('qux');

        self::assertSame('baz', $mock->getProtectedReadableAndWriteable());
        $mock->setProtectedReadableAndWriteable('qux');
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksImplementingInterfaceProtectedGetHook(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks::class)
            ->makePartial();

        $mock->expects('$protectedReadable::get')->andReturn('bar');

        self::assertSame('bar', $mock->getProtectedReadable());
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksImplementingInterfaceProtectedSetHook(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedWriteable::set')->with('quz');

        $mock->setProtectedWriteable('quz');
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksImplementingInterfacePublicGetAndSetHooks(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks::class);

        $mock->expects('$readableAndWriteable::get')->andReturn('baz');
        $mock->expects('$readableAndWriteable::set')->with('qux');

        self::assertSame('baz', $mock->readableAndWriteable);
        $mock->readableAndWriteable = 'qux';
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksImplementingInterfacePublicGetHook(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks::class);

        $mock->expects('$readable::get')->andReturn('bar');

        self::assertSame('bar', $mock->readable);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithHooksImplementingInterfacePublicSetHook(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks::class);

        $mock->expects('$writeable::set')->with('quz');

        $mock->writeable = 'quz';
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksExtendingAbstractClassProtectedGetAndSetPropertyHooks(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksExtendingAbstractClassWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedReadableAndWriteable::get')->never();
        $mock->expects('$protectedReadableAndWriteable::set')->never();

        $mock->setProtectedReadableAndWriteable('foo');

        self::assertSame('foo', $mock->getProtectedReadableAndWriteable());
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksExtendingAbstractClassProtectedGetPropertyHook(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksExtendingAbstractClassWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedReadable::set')->never();
        $mock->expects('$protectedReadable::get')->never();

        $mock->setProtectedReadable('bar');

        self::assertSame('bar', $mock->getProtectedReadable());
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksExtendingAbstractClassProtectedSetPropertyHook(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksExtendingAbstractClassWithPropertyHooks::class);

        $mock->expects('setProtectedWriteable')->with('quz');

        $mock->setProtectedWriteable('quz');
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksExtendingAbstractClassPublicProperties(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksExtendingAbstractClassWithPropertyHooks::class);

        $mock->readable = 'bar';
        $mock->readableAndWriteable = 'baz';

        self::assertSame('bar', $mock->readable);
        self::assertSame('baz', $mock->readableAndWriteable);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksImplementingInterfaceProtectedGetAndSetPropertyHooks(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksImplementingInterfaceWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedReadableAndWriteable::get')->never();
        $mock->expects('$protectedReadableAndWriteable::set')->never();

        $mock->setProtectedReadableAndWriteable('qux');

        self::assertSame('qux', $mock->getProtectedReadableAndWriteable());

    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksImplementingInterfaceProtectedGetPropertyHook(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksImplementingInterfaceWithPropertyHooks::class)->makePartial();

        $mock->expects('$protectedReadable::set')->never();
        $mock->expects('$protectedReadable::get')->never();

        $mock->setProtectedReadable('bar');

        self::assertSame('bar', $mock->getProtectedReadable());
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksImplementingInterfaceProtectedSetPropertyHook(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksImplementingInterfaceWithPropertyHooks::class)
            ->makePartial();

        $mock->expects('$protectedWriteable::set')->never()->with('quz');

        $mock->setProtectedWriteable('quz');
    }

    /**
     * @throws Throwable
     */
    public function testCanMockConcreteClassWithoutHooksImplementingInterfacePublicProperties(): void
    {
        $mock = Mockery::mock(ClassWithoutPropertyHooksImplementingInterfaceWithPropertyHooks::class);

        $mock->readable = 'bar';
        $mock->readableAndWriteable = 'baz';

        self::assertSame('bar', $mock->readable);
        self::assertSame('baz', $mock->readableAndWriteable);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterfaceGetAndSetPropertyHooks(): void
    {
        $mock = Mockery::mock(InterfaceWithPropertyHooks::class);

        $mock->expects('$readableAndWriteable::get')->andReturn('baz');
        $mock->expects('$readableAndWriteable::set')->with('qux');

        self::assertSame('baz', $mock->readableAndWriteable);
        $mock->readableAndWriteable = 'qux';
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterfaceGetPropertyHook(): void
    {
        $mock = Mockery::mock(InterfaceWithPropertyHooks::class);

        $mock->expects('$readable::get')->andReturn('bar');

        self::assertSame('bar', $mock->readable);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterfaceSetPropertyHook(): void
    {
        $mock = Mockery::mock(InterfaceWithPropertyHooks::class);

        $mock->expects('$writeable::set')->with('quz');

        $mock->writeable = 'quz';
    }

    /**
     * @throws Throwable
     */
    public function testCanMockMultipleInterfacePropertyHooks(): void
    {
        $mock = Mockery::mock(InterfaceWithPropertyHooks::class);

        $mock->expects('$readable::get')->andReturn('bar');
        $mock->expects('$readableAndWriteable::get')->andReturn('baz');
        $mock->expects('$readableAndWriteable::set')->with('qux');
        $mock->expects('$writeable::set')->with('quz');

        self::assertSame('bar', $mock->readable);
        self::assertSame('baz', $mock->readableAndWriteable);
        $mock->readableAndWriteable = 'qux';
        $mock->writeable = 'quz';
    }

    /**
     * @throws Throwable
     */
    public function testConcreteClassWithHooksExtendingAbstractClassPublicMultipleHooks(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksExtendingAbstractClassWithPropertyHooks::class);

        $mock->expects('$readable::get')->andReturn('readable');
        $mock->expects('$readableAndWriteable::get')->andReturn('readableAndWriteable');
        $mock->expects('$readableAndWriteable::set')->with('qux');
        $mock->expects('$writeable::set')->with('quz');

        self::assertSame('readable', $mock->readable);
        self::assertSame('readableAndWriteable', $mock->readableAndWriteable);
        $mock->readableAndWriteable = 'qux';
        $mock->writeable = 'quz';
    }

    /**
     * @throws Throwable
     */
    public function testConcreteClassWithHooksImplementingInterfacePublicMultipleHooks(): void
    {
        $mock = Mockery::mock(ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks::class);

        $mock->expects('$readable::get')->andReturn('readable');
        $mock->expects('$readableAndWriteable::get')->andReturn('readableAndWriteable');
        $mock->expects('$readableAndWriteable::set')->with('qux');
        $mock->expects('$writeable::set')->with('quz');

        self::assertSame('readable', $mock->readable);
        self::assertSame('readableAndWriteable', $mock->readableAndWriteable);
        $mock->readableAndWriteable = 'qux';
        $mock->writeable = 'quz';
    }
}
