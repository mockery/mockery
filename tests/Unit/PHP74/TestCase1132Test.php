<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP74;

use Mockery;
use Mockery\Exception\NoMatchingExpectationException;
use PHP74\DummyClass;
use Tests\Unit\AbstractTestCase;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 *
 * @requires PHP 7.4
 *
 * @see https://github.com/mockery/mockery/issues/1132
 */
final class TestCase1132Test extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testCase(): void
    {
        $mock = Mockery::mock('TestCase1132');

        $expectedDummy = new DummyClass();
        $expectedDummy->number = 1;

        $mock->allows()->saveDummy($expectedDummy);

        $this->expectException(NoMatchingExpectationException::class);

        $actualDummy = new DummyClass();
        $mock->saveDummy($actualDummy);
    }
}
