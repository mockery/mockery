<?php

declare(strict_types=1);

namespace PHP73;

class ClassWithToString
{
    public function __toString()
    {
        return 'foo';
    }
}
