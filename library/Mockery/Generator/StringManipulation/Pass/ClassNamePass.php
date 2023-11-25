<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

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

        $target = $config->getTargetClass();
        if ($target && $target->isReadOnly()) {
            // dd($config, $namespace, $className);
            $code = str_replace(
                'class Mock',
                'readonly class Mock',
                $code
            );

            // remove \AllowDynamicProperties trait
            $code = str_replace(
                '#[\AllowDynamicProperties]',
                '',
                $code
            );
        }

        $code = str_replace(
            'class Mock',
            'class ' . $className,
            $code
        );

        return $code;
    }
}
