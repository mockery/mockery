<?php

declare(strict_types=1);

namespace PHP82;

interface IReturnCoVarianceTest
{
    public function stuff(): (AInterface&BInterface)|DInterface;
}
