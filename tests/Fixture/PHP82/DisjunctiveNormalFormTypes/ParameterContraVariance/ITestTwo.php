<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes\ParameterContraVariance;

use Fixture\PHP82\DisjunctiveNormalFormTypes\C;
use Fixture\PHP82\DisjunctiveNormalFormTypes\D;

interface ITestTwo
{
    public function things(C|D $arg): void;
}
