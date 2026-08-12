<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Adapter\Phpunit;

use Override;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener as PHPUnitTestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

class TestListener implements PHPUnitTestListener
{
    use TestListenerDefaultImplementation;

    private $trait;

    public function __construct()
    {
        $this->trait = new TestListenerTrait();
    }

    #[Override]
    public function endTest(Test $test, float $time): void
    {
        $this->trait->endTest($test, $time);
    }

    #[Override]
    public function startTestSuite(TestSuite $suite): void
    {
        $this->trait->startTestSuite();
    }
}
