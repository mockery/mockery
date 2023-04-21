<?php

namespace test\Mockery;

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
}

class HasNullReturnType
{
    public function getChildren(): null
    {
        return null;
    }
}
