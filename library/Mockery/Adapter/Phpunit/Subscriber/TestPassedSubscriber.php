<?php

declare(strict_types=1);

namespace Mockery\Adapter\Phpunit\Subscriber;

use Mockery\Adapter\Phpunit\Extension;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;
use PHPUnit\Framework\ExpectationFailedException;

final class TestPassedSubscriber implements PassedSubscriber
{
    public function notify(Passed $event): void
    {
        if (Extension::isOpen()) {
            throw new ExpectationFailedException(sprintf(
                implode("\n", [
                    "Mockery's expectations have not been verified.",
                    'Make sure that \Mockery::close() is called at the end of the test.',
                    'Consider extending %s.',
                ]),
                MockeryTestCase::class
            ));
        }
    }
}
