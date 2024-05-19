<?php

declare(strict_types=1);

namespace Unit\PHP74;

use Mockery;
use Mockery\Exception\NoMatchingExpectationException;
use PHP74\DummyClass;
use Tests\Unit\AbstractTestCase;

/**
 * @coversDefaultClass Mockery
 * @requires PHP 7.4
 * @see https://github.com/mockery/mockery/issues/1132
 */
final class TestCase1132Test extends AbstractTestCase
{
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
