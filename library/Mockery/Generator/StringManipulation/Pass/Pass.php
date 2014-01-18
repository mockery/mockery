<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

interface Pass
{
    public function apply($code, MockConfiguration $config);
}
