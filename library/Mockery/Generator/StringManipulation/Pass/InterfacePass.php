<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class InterfacePass implements Pass
{
    public function apply($code, MockConfiguration $config, $className)
    {
        return array_reduce((array) $config->getTargetInterfaces(), function ($code, $i) {
            return $code . ", \\" . $i->getName();
        }, $code);
    }
}
