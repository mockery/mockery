<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes\ReturnCoVariance;

use Fixture\PHP82\DisjunctiveNormalFormTypes\A;
use Fixture\PHP82\DisjunctiveNormalFormTypes\B;
use Fixture\PHP82\DisjunctiveNormalFormTypes\D;

interface ITest
{
    public function stuff(): (A&B)|D;
}
