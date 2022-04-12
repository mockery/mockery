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

namespace tests\Mockery\Adapter\Phpunit;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\TestResult;
use Mockery\Adapter\Phpunit\TestListener;
use test\Mockery\Fixtures\EmptyTestCase;

class TestListenerTest extends MockeryTestCase
{
    protected $container;
    protected $listener;
    protected $testResult;
    protected $test;

    protected function mockeryTestSetUp()
    {
        // We intentionally test the static container here. That is what the
        // listener will check.
        $this->container = \Mockery::getContainer();
        $this->listener = new TestListener();
        $this->testResult = new TestResult();
        $this->test = new EmptyTestCase();

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

    public function testMockeryIsAddedToBlacklist()
    {
        $suite = \Mockery::mock(\PHPUnit\Framework\TestSuite::class);

        if (method_exists(\PHPUnit\Util\Blacklist::class, 'addDirectory')) {
            $this->assertFalse(
                (new \PHPUnit\Util\Blacklist())->isBlacklisted(
                    (new \ReflectionClass(\Mockery::class))->getFileName()
                )
            );

            $this->listener->startTestSuite($suite);

            $this->assertTrue(
                (new \PHPUnit\Util\Blacklist())->isBlacklisted(
                    (new \ReflectionClass(\Mockery::class))->getFileName()
                )
            );
        } else {
            $this->assertArrayNotHasKey(\Mockery::class, \PHPUnit\Util\Blacklist::$blacklistedClassNames);
            $this->listener->startTestSuite($suite);
            $this->assertSame(1, \PHPUnit\Util\Blacklist::$blacklistedClassNames[\Mockery::class]);
        }
    }
}
