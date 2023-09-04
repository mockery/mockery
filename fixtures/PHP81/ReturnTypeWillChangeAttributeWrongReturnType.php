<?php

namespace PHP81;

class ReturnTypeWillChangeAttributeWrongReturnType extends \DateTime
{
    #[\ReturnTypeWillChange]
    public function getTimestamp(): float
    {
    }
}
