<?php

declare(strict_types=1);

namespace PHP80;

class ReturnTypeParentTypeHint extends \stdClass
{
    public function foo(): parent
    {
    }
}
