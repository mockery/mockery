<?php

declare(strict_types=1);

namespace PHP82;

interface ITestTwo
{
    public function things(CInterface|DInterface $arg): void;
}
