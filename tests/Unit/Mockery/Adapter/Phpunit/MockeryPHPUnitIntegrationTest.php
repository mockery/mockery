<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Adapter\Phpunit;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\BadMethodCallException;
use PHP73\BaseClassStub;
use Throwable;

use function mock;
use function spy;

/**
 * @coversDefaultClass \Mockery
 */
final class MockeryPHPUnitIntegrationTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItMarksAPassingTestAsRiskyIfWeThrewExceptions(): void
    {
        $e = null;
        $mock = mock();

        try {
            $mock->foobar();
        } catch (BadMethodCallException $e) {
            // exception swallowed...
        }

        $test = spy(BaseClassStub::class)->makePartial();
        $test->finish();

        $test->shouldHaveReceived()->markAsRisky();

        // We can dismiss the exception to avoid the risky test
        if ($e instanceof BadMethodCallException) {
            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testTheUserCanManuallyDismissAnExceptionToAvoidTheRiskyTest(): void
    {
        $mock = mock();

        try {
            $mock->foobar();
        } catch (BadMethodCallException $e) {
            $e->dismiss();
        }

        $test = spy(BaseClassStub::class)->makePartial();
        $test->finish();

        $test->shouldNotHaveReceived()
            ->markAsRisky();
    }
}
