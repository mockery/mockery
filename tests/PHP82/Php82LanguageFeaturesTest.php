<?php

namespace MockeryTest\PHP82;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @requires PHP 8.2.0-dev
 */
class Php82LanguageFeaturesTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @test */
    public function it_can_mock_an_class_with_null_return_type()
    {
        $mock = \Mockery::mock(\MockeryTest\Mockery\HasNullReturnType::class);

        $this->assertInstanceOf(\MockeryTest\Mockery\HasNullReturnType::class, $mock);
    }
}
