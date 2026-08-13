<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Adapter\Phpunit\Extension\Subscriber;

use Mockery;
use PHPUnit\Event\TestSuite\Started;
use PHPUnit\Event\TestSuite\StartedSubscriber;
use PHPUnit\Util\ExcludeList;
use ReflectionClass;

use function dirname;

final class TestSuiteStartedSubscriber implements StartedSubscriber
{
    public function notify(Started $event): void
    {
        $excludeList = new ExcludeList();

        $filename = (new ReflectionClass(Mockery::class))->getFileName();

        if (! $excludeList->isExcluded($filename)) {
            ExcludeList::addDirectory(dirname($filename));
        }
    }
}
