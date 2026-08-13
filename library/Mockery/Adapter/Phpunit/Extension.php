<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Adapter\Phpunit;

use Mockery\Adapter\Phpunit\Extension\Subscriber\TestFinishedSubscriber;
use Mockery\Adapter\Phpunit\Extension\Subscriber\TestSuiteStartedSubscriber;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class Extension implements \PHPUnit\Runner\Extension\Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscribers(new TestFinishedSubscriber(), new TestSuiteStartedSubscriber());
    }
}
