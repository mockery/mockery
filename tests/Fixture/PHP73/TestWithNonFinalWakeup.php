<?php

declare(strict_types=1);

namespace PHP73;

class TestWithNonFinalWakeup
{
    public function __wakeup()
    {
        return __METHOD__;
    }
}

