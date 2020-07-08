<?php

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @requires PHP 7.1.0-dev
 */
class Php71LanguageFeaturesTest extends MockeryTestCase
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
