<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes\ParameterContraVariance;

use Fixture\PHP82\DisjunctiveNormalFormTypes\A;
use Fixture\PHP82\DisjunctiveNormalFormTypes\B;
use Fixture\PHP82\DisjunctiveNormalFormTypes\D;

class TestThree implements ITestTwo
{
    public function things((A&B)|D $arg): void
    {
    }
}
