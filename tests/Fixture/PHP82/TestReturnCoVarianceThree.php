<?php

declare(strict_types=1);

namespace PHP82;

class TestReturnCoVarianceThree implements IReturnCoVarianceTest
{
// Since C is a subset of A&B, even though it is not identical.
    public function stuff(): CInterface|DInterface
    {
        return new YClass();
    }
}
