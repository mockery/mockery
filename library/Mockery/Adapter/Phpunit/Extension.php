<?php

declare(strict_types=1);

namespace Mockery\Adapter\Phpunit;

use Mockery;
use Mockery\Adapter\Phpunit\Subscriber\ApplicationStartedSubscriber;
use Mockery\Adapter\Phpunit\Subscriber\TestFinishedSubscriber;
use Mockery\Adapter\Phpunit\Subscriber\TestPassedSubscriber;
use Mockery\Adapter\Phpunit\Subscriber\TestPreparedSubscriber;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Extension\Extension as PHPUnitExtension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class Extension implements PHPUnitExtension
{
    private static bool $enabled = false;

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscribers(
            new ApplicationStartedSubscriber(),
            new TestFinishedSubscriber(),
            new TestPassedSubscriber(),
            new TestPreparedSubscriber(),
        );
    }

    public static function close(): void
    {
        if (self::$enabled) {
            Mockery::close();
            self::$enabled = false;
        }
    }

    public static function isOpen(): bool
    {
        return self::$enabled;
    }

    public static function open(): void
    {
        self::$enabled = true;
    }

    public static function reset(): void
    {
        if (self::$enabled) {
            self::close();
        }
    }

    public static function verify(TestCase $test): void
    {
        if (! self::$enabled) {
            throw new ExpectationFailedException('Mockery is not enabled.');
        }

        $expectationCount = Mockery::getContainer()->mockery_getExpectationCount();

        if (0 !== $expectationCount) {
            $test->addToAssertionCount($expectationCount);
        }

        self::close();
    }
}
