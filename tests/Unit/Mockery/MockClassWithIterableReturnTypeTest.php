<?php

namespace Mockery\Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockClassWithIterableReturnTypeTest extends MockeryTestCase
{
    public function testMockingIterableReturnType()
    {
        $mock = mock(ReturnTypeIterableTypeHint::class);

        $mock->shouldReceive("returnIterable");
        $this->assertSame([], $mock->returnIterable());
    }
}

abstract class ReturnTypeIterableTypeHint
{
    public function returnIterable(): iterable
    {
    }
}
