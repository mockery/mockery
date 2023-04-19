<?php

namespace MockeryTest\Fixture\PHP80100;

class ReturnTypeWillChangeAttributeNoReturnType extends \DateTime
{
    #[\ReturnTypeWillChange]
    public function getTimestamp()
    {
    }
}
