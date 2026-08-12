<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP82;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;
use PHP82\ReadonlyClass;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery\Expectation
 *
 * @requires PHP 8.2
 *
 * @see https://github.com/mockery/mockery/issues/1317
 */
final class TestCase1317Test extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testCanNotMockReadonlyClasses(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The class \PHP82\ReadonlyClass is marked readonly');

        mock(ReadonlyClass::class);
    }
}
