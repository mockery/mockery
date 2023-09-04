<?php

namespace PHP81;

class ReturnTypeWillChangeAttributeNoReturnType extends \DateTime
{
    #[\ReturnTypeWillChange]
    public function getTimestamp()
    {
    }
}
