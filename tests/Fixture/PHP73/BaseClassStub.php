<?php

declare(strict_types=1);

namespace PHP73;

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
