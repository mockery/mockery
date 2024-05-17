<?php

declare(strict_types=1);

namespace PHP73;

class ClassWithGetter
{
    private $dep;

    public function __construct($dep)
    {
        $this->dep = $dep;
    }

    public function getFoo()
    {
        return $this->dep->doBar('bar', $this);
    }
}

