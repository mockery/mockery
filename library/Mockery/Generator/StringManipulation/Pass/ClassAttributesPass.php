<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class ClassAttributesPass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        $class =  $config->getTargetClass();

        if (!$class) {
            return $code;
        }

        $attributes = $class->getAttributes();

        if (!empty($attributes)) {
            $attributes = '#[' . implode(',', $attributes) . ']' . PHP_EOL;

            return str_replace(
                'class Mock',
                $attributes . ' class Mock',
                $code
            );
        }

        return $code;
    }
}
