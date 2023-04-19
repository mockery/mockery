<?php

namespace MockeryTest\Unit\PHP82;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use MockeryTest\Mockery\HasNullReturnType;

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
}
