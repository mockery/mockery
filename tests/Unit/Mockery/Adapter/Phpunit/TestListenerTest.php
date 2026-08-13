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

use EmptyTestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Adapter\Phpunit\TestListener;
use Override;
use PHPUnit\Framework\Attributes\RequiresPhpunit;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Util\Blacklist;
use ReflectionClass;
use Throwable;

use function method_exists;

/**
 * @coversDefaultClass \Mockery
 *
 * @requires PHPUnit < 10.0
 */
#[RequiresPhpunit('<10.0.0')]
final class TestListenerTest extends MockeryTestCase
{
    protected $container;

    protected $listener;

    protected $test;

    protected $testResult;

    /**
     * @throws Throwable
     */
    #[Override]
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

    /**
     * @throws Throwable
     */
    public function testFailureOnMissingClose(): void
    {
        $this->listener->startTestSuite(new TestSuite());

        $mock = Mockery::mock();
        $mock->shouldReceive('bar')
            ->once();

        $this->listener->endTest($this->test, 0);
        self::assertFalse($this->testResult->wasSuccessful(), 'expected test result to indicate failure');

        // Satisfy the expectation and close the global container now so we
        // don't taint the environment.
        $mock->bar();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testMockeryIsAddedToBlacklist(): void
    {
        $suite = Mockery::mock(TestSuite::class);

        if (method_exists(Blacklist::class, 'addDirectory')) {
            $this->listener->startTestSuite($suite);

            self::assertTrue(
                (new Blacklist())->isBlacklisted((new ReflectionClass(Mockery::class))->getFileName()),
                'expected blacklist to contain the Mockery reflection'
            );
        } else {
            self::assertArrayNotHasKey(Mockery::class, Blacklist::$blacklistedClassNames);
            $this->listener->startTestSuite($suite);
            self::assertSame(
                1,
                Blacklist::$blacklistedClassNames[Mockery::class],
                'expected blacklist to contain the Mockery reflection'
            );
        }
    }

    /**
     * @throws Throwable
     */
    public function testSuccessOnClose(): void
    {
        $this->listener->startTestSuite(new TestSuite());

        $container = Mockery::getContainer();

        $mock = $container->mock();
        $mock->shouldReceive('bar')
            ->once();
        $mock->bar();

        // This is what MockeryPHPUnitIntegration and MockeryTestCase trait
        // will do. We intentionally call the static close method.
        $this->test->addToAssertionCount($container->mockery_getExpectationCount());

        Mockery::close();

        $this->listener->endTest($this->test, 0);
        self::assertTrue($this->testResult->wasSuccessful(), 'expected test result to indicate success');
    }

    /**
     * @throws Throwable
     */
    public function testSuccessWhenMockIsUsedBeforeStartingTests(): void
    {
        $mock = Mockery::mock();
        $mock->shouldReceive('bar')
            ->once();

        $this->listener->startTestSuite(new TestSuite());

        $this->listener->endTest($this->test, 0);

        self::assertTrue($this->testResult->wasSuccessful(), 'expected test result to indicate success');
    }
}
