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

namespace MockeryTest\Unit\Mockery\Adapter\Phpunit;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Adapter\Phpunit\TestListener;
use MockeryTest\Mockery\Fixtures\EmptyTestCase;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Util\Blacklist;
use ReflectionClass;

use function method_exists;

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
        $this->container = Mockery::getContainer();
        $this->listener = new TestListener();
        $this->testResult = new TestResult();
        $this->test = new \MockeryTest\Fixture\EmptyTestCase();

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
        Mockery::close();

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
        Mockery::close();
    }

    public function testMockeryIsAddedToBlacklist()
    {
        $suite = Mockery::mock(TestSuite::class);

        if (method_exists(Blacklist::class, 'addDirectory')) {
            $this->assertFalse(
                (new Blacklist())->isBlacklisted(
                    (new ReflectionClass(Mockery::class))->getFileName()
                )
            );

            $this->listener->startTestSuite($suite);

            $this->assertTrue(
                (new Blacklist())->isBlacklisted(
                    (new ReflectionClass(Mockery::class))->getFileName()
                )
            );
        } else {
            $this->assertArrayNotHasKey(Mockery::class, Blacklist::$blacklistedClassNames);
            $this->listener->startTestSuite($suite);
            $this->assertSame(1, Blacklist::$blacklistedClassNames[Mockery::class]);
        }
    }
}
