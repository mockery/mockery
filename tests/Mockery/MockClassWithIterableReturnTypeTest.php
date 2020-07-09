<?php

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockClassWithIterableReturnTypeTest extends MockeryTestCase
{
    public function testMockingIterableReturnType()
    {
        $mock = mock("test\Mockery\ReturnTypeIterableTypeHint");

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
