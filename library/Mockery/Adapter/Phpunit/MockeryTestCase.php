<?php

namespace Mockery\Adapter\Phpunit;

use Mockery;

abstract class MockeryTestCase extends \PHPUnit_Framework_TestCase
{
    protected function assertPostConditions()
    {
        $this->addMockeryExpectationsToAssertionCount();
        $this->closeMockery();

        parent::assertPostConditions();
    }

    protected function addMockeryExpectationsToAssertionCount()
    {
        $container = Mockery::getContainer();
        if ($container != null) {
            $count = $container->mockery_getExpectationCount();
            $this->addToAssertionCount($count);
        }
    }

    protected function closeMockery()
    {
        Mockery::close();
    }
}
