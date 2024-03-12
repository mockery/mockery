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

class InterfacePass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        foreach ($config->getTargetInterfaces() as $i) {
            $name = ltrim($i->getName(), "\\");
            if (!interface_exists($name)) {
                \Mockery::declareInterface($name);
            }
        }

        $interfaces = array_reduce((array) $config->getTargetInterfaces(), function ($code, $i) {
            return $code . ", \\" . ltrim($i->getName(), "\\");
        }, "");

        $parts = preg_split("/{/", $code, 2);

        $parts[0] = str_replace(
            "implements MockInterface",
            "implements MockInterface" . $interfaces,
            $parts[0]
        );

        return $parts[0] . "{" . $parts[1];
    }
}
