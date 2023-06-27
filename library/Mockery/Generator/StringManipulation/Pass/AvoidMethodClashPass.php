<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\Method;
use Mockery\Generator\Parameter;
use Mockery\Generator\MockConfiguration;

class AvoidMethodClashPass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        $names = array_map(function ($method) {
            return $method->getName();
        }, $config->getMethodsToMock());

        foreach (["allows", "expects"] as $method) {
            if (in_array($method, $names)) {
                $code = preg_replace(
                    "#// start method {$method}.*// end method {$method}#ms",
                    "",
                    $code
                );

                $code = str_replace(" implements MockInterface", " implements LegacyMockInterface", $code);
            }
        }

        return $code;
    }
}
