<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\BaseTestRunner;

class EmptyTestCase extends TestCase
{
    public function getStatus(): int
    {
        return BaseTestRunner::STATUS_PASSED;
    }
}
