<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

class Mockery_Adapter_Phpunit_TestListenerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // We intentionally test the static container here. That is what the
        // listener will check.
        $this->container = \Mockery::getContainer();
        $this->listener = new \Mockery\Adapter\Phpunit\TestListener();
        $this->testResult = new \PHPUnit_Framework_TestResult();
        $this->test = new \Mockery_Adapter_Phpunit_EmptyTestCase();

        $this->test->setTestResultObject($this->testResult);
        $this->testResult->addListener($this->listener);

        $this->assertTrue($this->testResult->wasSuccessful(), 'sanity check: empty test results should be considered successful');
    }

    public function testSuccessOnClose()
    {
        $mock = $this->container->mock();
        $mock->shouldReceive('bar')->once();
        $mock->bar();

        // This is what MockeryPHPUnitIntegration and MockeryTestCase trait
        // will do. We intentionally call the static close method.
        $this->test->addToAssertionCount($this->container->mockery_getExpectationCount());
        \Mockery::close();

        $this->listener->endTest($this->test, 0);
        $this->assertTrue($this->testResult->wasSuccessful(), 'expected test result to indicate success');
    }

    public function testFailureOnMissingClose()
    {
        $mock = $this->container->mock();
        $mock->shouldReceive('bar')->once();

        $this->listener->endTest($this->test, 0);
        $this->assertFalse($this->testResult->wasSuccessful(), 'expected test result to indicate failure');

        // Satisfy the expectation and close the global container now so we
        // don't taint the environment.
        $mock->bar();
        \Mockery::close();
    }
}

class Mockery_Adapter_Phpunit_EmptyTestCase extends PHPUnit_Framework_TestCase
{
    public function getStatus()
    {
        return \PHPUnit_Runner_BaseTestRunner::STATUS_PASSED;
    }
}
