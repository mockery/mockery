<?php

declare(strict_types=1);

namespace Fixture\PHP81;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class SomeAttribute
{
    public function __construct(
        public object $param = new stdClass()
    ) {
    }
}
