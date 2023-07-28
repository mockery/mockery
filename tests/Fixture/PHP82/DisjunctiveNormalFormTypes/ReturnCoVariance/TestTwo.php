<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes\ReturnCoVariance;

use Fixture\PHP82\DisjunctiveNormalFormTypes\D;

class TestTwo implements ITest
{
    public function stuff(): D
    {
        return new class () implements D { };
    }
}
