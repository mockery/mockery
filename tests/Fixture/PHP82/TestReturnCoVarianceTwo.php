<?php

declare(strict_types=1);

namespace PHP82;

class TestReturnCoVarianceTwo implements IReturnCoVarianceTest
{

// D is is a subset of A&B|D
    public function stuff(): DInterface
    {
        return new ZClass();
    }
}
