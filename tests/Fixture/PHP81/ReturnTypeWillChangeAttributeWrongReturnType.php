<?php

declare(strict_types=1);

namespace PHP81;

use DateTime;
use ReturnTypeWillChange;

class ReturnTypeWillChangeAttributeWrongReturnType extends DateTime
{
    #[ReturnTypeWillChange]
    public function getTimestamp(): float
    {
        return 1.0;
    }
}
