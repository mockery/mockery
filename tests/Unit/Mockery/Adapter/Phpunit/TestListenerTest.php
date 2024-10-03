<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Adapter\Phpunit;

use EmptyTestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Adapter\Phpunit\TestListener;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Util\Blacklist;
use ReflectionClass;

/**
 * @coversDefaultClass \Mockery
 */
final class TestListenerTest extends MockeryTestCase
{
    protected $container;

    protected $listener;

    protected $test;

    protected $testResult;

    protected function mockeryTestSetUp(): void
    {
        // We intentionally test the static container here. That is what the
        // listener will check.
        $this->container = Mockery::getContainer();
        $this->listener = new TestListener();
        $this->testResult = new TestResult();
        $this->test = new EmptyTestCase();

        $this->test->setTestResultObject($this->testResult);
        $this->testResult->addListener($this->listener);

        self::assertTrue(
            $this->testResult->wasSuccessful(),
            'sanity check: empty test results should be considered successful'
        );
    }

    public function testFailureOnMissingClose(): void
    {
        $mock = $this->container->mock();
        $mock->shouldReceive('bar')
            ->once();

        $this->listener->endTest($this->test, 0);
        self::assertFalse($this->testResult->wasSuccessful(), 'expected test result to indicate failure');

        // Satisfy the expectation and close the global container now so we
        // don't taint the environment.
        $mock->bar();
        Mockery::close();
    }

    public function testMockeryIsAddedToBlacklist(): void
    {
        $suite = Mockery::mock(TestSuite::class);

        if (\method_exists(Blacklist::class, 'addDirectory')) {
            self::assertFalse(
                (new Blacklist())->isBlacklisted((new ReflectionClass(Mockery::class))->getFileName())
            );

            $this->listener->startTestSuite($suite);

            self::assertTrue(
                (new Blacklist())->isBlacklisted((new ReflectionClass(Mockery::class))->getFileName())
            );
        } else {
            self::assertArrayNotHasKey(Mockery::class, Blacklist::$blacklistedClassNames);
            $this->listener->startTestSuite($suite);
            self::assertSame(1, Blacklist::$blacklistedClassNames[Mockery::class]);
        }
    }

    public function testSuccessOnClose(): void
    {
        $mock = $this->container->mock();
        $mock->shouldReceive('bar')
            ->once();
        $mock->bar();

        // This is what MockeryPHPUnitIntegration and MockeryTestCase trait
        // will do. We intentionally call the static close method.
        $this->test->addToAssertionCount($this->container->mockery_getExpectationCount());

        Mockery::close();

        $this->listener->endTest($this->test, 0);
        self::assertTrue($this->testResult->wasSuccessful(), 'expected test result to indicate success');
    }
}
