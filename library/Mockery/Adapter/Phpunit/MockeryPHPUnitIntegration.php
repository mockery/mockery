<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Adapter\Phpunit;

use Mockery;
use PHPUnit\Event\Facade;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use Throwable;

use function get_class;
use function method_exists;

/**
 * Integrates Mockery into PHPUnit. Ensures Mockery expectations are verified
 * for each test and are included by the assertion counter.
 */
trait MockeryPHPUnitIntegration
{
    use MockeryPHPUnitIntegrationAssertPostConditions;

    protected $mockeryOpen;

    protected function addMockeryExpectationsToAssertionCount()
    {
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
    }

    protected function checkMockeryExceptions()
    {
        if (method_exists($this, 'valueObjectForEvents')) {
            foreach (Mockery::getContainer()->mockery_thrownExceptions() as $exception) {
                if (! $exception->dismissed()) {
                    Facade::emitter()->testConsideredRisky(
                        $this->valueObjectForEvents(),
                        get_class($exception) . ': '. $exception->getMessage()
                    );

                    return;
                }
            }

            return;
        }

        if (method_exists($this, 'markAsRisky')) {
            foreach (Mockery::getContainer()->mockery_thrownExceptions() as $exception) {
                if (! $exception->dismissed()) {
                    $this->markAsRisky();

                    return;
                }
            }
        }
    }

    /**
     * @throws Throwable
     */
    protected function closeMockery()
    {
        Mockery::close();
        $this->mockeryOpen = false;
    }

    /**
     * Performs assertions shared by all tests of a test case. This method is
     * called before execution of a test ends and before the tearDown method.
     *
     * @throws Throwable
     */
    protected function mockeryAssertPostConditions()
    {
        $this->addMockeryExpectationsToAssertionCount();
        $this->checkMockeryExceptions();
        $this->closeMockery();
    }

    /**
     * @after
     *
     * @throws Throwable
     */
    #[After]
    protected function purgeMockeryContainer()
    {
        if ($this->mockeryOpen) {
            // post conditions wasn't called, so test probably failed
            Mockery::close();
        }
    }

    /**
     * @before
     */
    #[Before]
    protected function startMockery()
    {
        $this->mockeryOpen = true;
    }
}
