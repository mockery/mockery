<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Adapter\Phpunit;

trait MockeryTestCaseSetUp
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockeryTestSetUp();
    }

    protected function tearDown(): void
    {
        $this->mockeryTestTearDown();

        parent::tearDown();
    }
}
