<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes\ReturnCoVariance;

use Fixture\PHP82\DisjunctiveNormalFormTypes\C;
use Fixture\PHP82\DisjunctiveNormalFormTypes\D;
use Fixture\PHP82\DisjunctiveNormalFormTypes\Y;

class TestThree implements ITest
{
    public function stuff(): C|D
    {
        return random_int(0, 1)
            ? new Y
            : new class () implements D { };
    }
}
