<?php

declare(strict_types=1);

namespace PHP73;

class Mockery_Magic
{
    public function __call($method, $args)
    {
        return 42;
    }
}
