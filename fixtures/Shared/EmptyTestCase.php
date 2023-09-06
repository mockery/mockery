<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Fixtures;

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\BaseTestRunner;

class EmptyTestCase extends TestCase
{
    public function getStatus(): int
    {
        return BaseTestRunner::STATUS_PASSED;
    }
}
