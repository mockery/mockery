<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class InterfacePass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        $interfaces = array_reduce((array) $config->getTargetInterfaces(), function ($code, $i) {
            return $code . ", \\" . $i->getName();
        }, "");

        $code = str_replace(
            "implements MockInterface",
            "implements MockInterface" . $interfaces,
            $code
        );

        return $code;
    }
}
