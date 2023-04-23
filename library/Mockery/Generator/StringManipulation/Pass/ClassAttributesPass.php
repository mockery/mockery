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
            $attributes = '#[' . implode(',', $attributes) . ']';

            return str_replace(
                '#[\AllowDynamicProperties]',
                $attributes,
                $code
            );
        }

        return $code;
    }
}
