<?php

namespace MockeryTest\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use MockeryTest\Fixture\ReturnTypeIterableTypeHint;
use function mock;

class MockClassWithIterableReturnTypeTest extends MockeryTestCase
{
    public function testMockingIterableReturnType()
    {
        $mock = mock(ReturnTypeIterableTypeHint::class);

        $mock->shouldReceive('returnIterable');
        $this->assertSame([], $mock->returnIterable());
    }
}
