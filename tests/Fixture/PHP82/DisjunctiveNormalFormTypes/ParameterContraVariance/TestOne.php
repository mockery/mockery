<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes\ParameterContraVariance;

use Fixture\PHP82\DisjunctiveNormalFormTypes\A;
use Fixture\PHP82\DisjunctiveNormalFormTypes\B;
use Fixture\PHP82\DisjunctiveNormalFormTypes\D;
use Fixture\PHP82\DisjunctiveNormalFormTypes\Z;

class TestOne implements ITest
{
    public function stuff((A&B)|D|Z $arg): void
    {
    }
}
