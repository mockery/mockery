<?php

namespace PHP82;

class TestReturnCoVarianceOne implements IReturnCoVarianceTest
{
    public function stuff(): A&B
    {
        return new Y;
    }
}
