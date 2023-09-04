<?php

namespace PHP82;

interface IReturnCoVarianceTest
{
    public function stuff(): (A&B)|D;
}
