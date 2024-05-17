<?php

declare(strict_types=1);

namespace PHP82;

interface ITest
{
    public function stuff((AInterface&BInterface)|DInterface $arg): void;
}
