<?php

namespace MockeryTest\Fixture\PHP80100;

class ReturnTypeWillChangeAttributeWrongReturnType extends \DateTime
{
    #[\ReturnTypeWillChange]
    public function getTimestamp(): float
    {
    }
}
