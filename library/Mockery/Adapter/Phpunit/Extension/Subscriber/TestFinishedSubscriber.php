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
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use Throwable;

use function implode;
use function sprintf;

final class TestFinishedSubscriber implements FinishedSubscriber
{
    public function notify(Finished $event): void
    {
        try {
            // The self() call is used as a sentinel. Anything that throws if
            // the container is closed already will do.
            Mockery::self();
        } catch (Throwable $_) {
            return;
        }

        Facade::emitter()->testConsideredRisky($event->test(), sprintf(
            implode("\n", [
                'Mockery\'s expectations have not been verified for this test.',
                'Make sure that \Mockery::close() is called at the end of the test.',
                '* Consider extending "%s" class or using "%s" trait to automatically verify expectations at the end of each test.',
            ]),
            MockeryTestCase::class,
            MockeryPHPUnitIntegration::class
        ));
    }
}
