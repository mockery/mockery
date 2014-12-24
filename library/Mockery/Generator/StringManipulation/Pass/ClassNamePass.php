<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class ClassNamePass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        $namespace = $config->getNamespaceName();

        $namespace = ltrim($namespace, "\\");

        $className = $config->getShortName();

        $code = str_replace(
            'namespace Mockery;',
            $namespace ? 'namespace ' . $namespace . ';' : '',
            $code
        );

        $code = str_replace(
            'class Mock',
            'class ' . $className,
            $code
        );

        return $code;
    }
}
