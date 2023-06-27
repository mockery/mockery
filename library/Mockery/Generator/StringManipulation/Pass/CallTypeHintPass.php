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

class CallTypeHintPass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        if ($config->requiresCallTypeHintRemoval()) {
            $code = str_replace(
                'public function __call($method, array $args)',
                'public function __call($method, $args)',
                $code
            );
        }

        if ($config->requiresCallStaticTypeHintRemoval()) {
            $code = str_replace(
                'public static function __callStatic($method, array $args)',
                'public static function __callStatic($method, $args)',
                $code
            );
        }

        return $code;
    }
}
