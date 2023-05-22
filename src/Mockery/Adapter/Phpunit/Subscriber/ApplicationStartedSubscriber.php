<?php

declare(strict_types=1);

namespace Mockery\Adapter\Phpunit\Subscriber;

use Mockery;
use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Application\StartedSubscriber;
use PHPUnit\Util\ExcludeList;
use ReflectionClass;

final class ApplicationStartedSubscriber implements StartedSubscriber
{
    public function notify(Started $event): void
    {
        static $excludeList = null;
        static $filename = null;

        $excludeList ??= new ExcludeList();
        $filename ??= (new ReflectionClass(Mockery::class))->getFileName();

        if (! $excludeList->isExcluded($filename)) {
            // Add directory after initialize ExcludeList::$directories via isExcluded
            ExcludeList::addDirectory(dirname($filename));
        }
    }
}
