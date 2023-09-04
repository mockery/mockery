<?php

namespace PHP82;

class TestReturnCoVarianceThree implements IReturnCoVarianceTest
{
    public function stuff(): C|D
    {
        return new Y;
    }
}
