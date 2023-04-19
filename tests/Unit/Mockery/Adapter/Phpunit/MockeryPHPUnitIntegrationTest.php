<?php

namespace MockeryTest\Unit\Mockery\Adapter\Phpunit;

use Exception;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\BadMethodCallException;
use MockeryTest\Fixture\Adapter\Phpunit\BaseClassStub;
use function mock;
use function spy;

class MockeryPHPUnitIntegrationTest extends MockeryTestCase
{
    /**
     * @test
     */
    public function it_marks_a_passing_test_as_risky_if_we_threw_exceptions()
    {
        $mock = mock();
        try {
            $mock->foobar();
        } catch (Exception $e) {
            // exception swallowed...
        }

        $test = spy(BaseClassStub::class)->makePartial();
        $test->finish();

        $test->shouldHaveReceived()->markAsRisky();
    }

    /**
     * @test
     */
    public function the_user_can_manually_dismiss_an_exception_to_avoid_the_risky_test()
    {
        $mock = mock();
        try {
            $mock->foobar();
        } catch (BadMethodCallException $e) {
            $e->dismiss();
        }

        $test = spy(BaseClassStub::class)->makePartial();
        $test->finish();

        $test->shouldNotHaveReceived()->markAsRisky();
    }
}
