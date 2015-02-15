<?php
namespace Mockery\Adapter\Phpunit;

/**
 * Integrates Mockery into PHPUnit. Ensures Mockery expectations are verified
 * for each test and are included by the assertion counter.
 */
trait MockeryPHPUnitIntegration
{
    /**
     * Performs assertions shared by all tests of a test case. This method is
     * called before execution of a test ends and before the tearDown method.
     */
    protected function assertPostConditions()
    {
        parent::assertPostConditions();

        // Add Mockery expectations to assertion count.
        if (($container = \Mockery::getContainer()) !== null) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        // Verify Mockery expectations.
        \Mockery::close();
    }
}
