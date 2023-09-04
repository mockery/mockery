<?php

declare(strict_types=1);

namespace PHP81;

use Fixture\PHP81\stdClass;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class SomeAttribute
{
    public function __construct(
        public object $param = new stdClass()
    ) {
    }
}
