<?php

declare(strict_types=1);

namespace PHP81;

class UsesEnums
{
    public SimpleEnum $enum = SimpleEnum::first;

    public function set(SimpleEnum $enum = SimpleEnum::second)
    {
        $this->enum = $enum;
    }
}
