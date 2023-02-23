<?php

declare(strict_types=1);

namespace Mockery\Adapter\Phpunit\Subscriber;

use Mockery\Adapter\Phpunit\Extension;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

final class TestPreparedSubscriber implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        Extension::open();
    }
}
