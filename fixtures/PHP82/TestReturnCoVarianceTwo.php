<?php

namespace PHP82;

class TestReturnCoVarianceTwo implements IReturnCoVarianceTest
{
    public function stuff(): D
    {
        return new Z;
    }
}
