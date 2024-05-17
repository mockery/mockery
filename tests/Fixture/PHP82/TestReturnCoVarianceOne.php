<?php

declare(strict_types=1);

namespace PHP82;

class TestReturnCoVarianceOne implements IReturnCoVarianceTest
{
// A&B is more restrictive.
    public function stuff(): AInterface&BInterface
    {
        return new YClass();
    }
}
