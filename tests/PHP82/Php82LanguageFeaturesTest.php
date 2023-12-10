<?php

namespace Mockery\Tests\PHP82;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @requires PHP 8.2.0-dev
 */
class Php82LanguageFeaturesTest extends MockeryTestCase
{
    /** @test */
    public function it_can_mock_an_class_with_null_return_type()
    {
        $mock = Mockery::mock(HasNullReturnType::class);

        $this->assertInstanceOf(HasNullReturnType::class, $mock);
    }

    public function testCanMockUndefinedClasses()
    {
        $class = mock('UndefinedClass');

        $class->foo = 'bar';

        $this->assertSame('bar', $class->foo);
    }

    public function testCanMockReservedWordFalse(): void
    {
        $mock = mock(HasReservedWordFalse::class);

        $mock->expects('testFalseMethod')->once();

        self::assertFalse($mock->testFalseMethod());
        self::assertInstanceOf(HasReservedWordFalse::class, $mock);
    }

    public function testCanMockReservedWordTrue(): void
    {
        $mock = mock(HasReservedWordTrue::class);

        $mock->expects('testTrueMethod')->once();

        self::assertTrue($mock->testTrueMethod());
        self::assertInstanceOf(HasReservedWordTrue::class, $mock);
    }
}

class HasReservedWordFalse
{
    public function testFalseMethod(): false
    {
        return false;
    }
}

class HasReservedWordTrue
{
    public function testTrueMethod(): true
    {
        return true;
    }
}

class HasNullReturnType
{
    public function getChildren(): null
    {
        return null;
    }
}
