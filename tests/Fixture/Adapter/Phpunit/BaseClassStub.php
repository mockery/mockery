<?php

namespace MockeryTest\Fixture\Adapter\Phpunit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class BaseClassStub
{
    use MockeryPHPUnitIntegration;
    public function finish()
    {
        $this->checkMockeryExceptions();
    }
    public function markAsRisky()
    {
    }
}
