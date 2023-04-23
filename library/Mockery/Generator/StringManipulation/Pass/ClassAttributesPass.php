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

        /** @var array<string> $attributes */
        $attributes = $class->getAttributes();

        if ($attributes !== []) {
            return str_replace(
                '#[\AllowDynamicProperties]',
                '#[' . implode(',', $attributes) . ']',
                $code
            );
        }

        return $code;
    }
}
