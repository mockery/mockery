<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes\ParameterContraVariance;

use Fixture\PHP82\DisjunctiveNormalFormTypes\A;
use Fixture\PHP82\DisjunctiveNormalFormTypes\D;

class TestTwo implements ITest
{
    public function stuff(A|D $arg): void
    {
    }
}
