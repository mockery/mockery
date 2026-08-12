<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryTestCase;

use function mock;

abstract class AbstractTestCase extends MockeryTestCase
{
    public function assertInvalidMock(string $class, string $exception, string $message): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        mock($class);
    }

    public function assertValidMock(string $class): void
    {
        self::assertInstanceOf($class, mock($class));
    }
}
