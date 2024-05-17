<?php

declare(strict_types=1);

namespace PHP81;

use ReturnTypeWillChange;

class ReturnTypeWillChangeAttributeNoReturnType extends \DateTime
{
    #[ReturnTypeWillChange]
    public function getTimestamp()
    {
    }
}
