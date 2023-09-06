<?php

namespace PHP83;

trait Traits {
    const string BAR = Enums::FOO;

    public function foo(): string
    {
        return Interfaces::BAR;
    }
}
