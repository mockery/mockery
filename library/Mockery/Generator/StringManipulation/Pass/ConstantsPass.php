<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use Override;

use const PHP_EOL;

use function array_key_exists;
use function sprintf;
use function strrpos;
use function substr_replace;
use function var_export;

class ConstantsPass implements Pass
{
    /**
     * @param  string $code
     * @return string
     */
    #[Override]
    public function apply($code, MockConfiguration $config)
    {
        $constantsMap = $config->getConstantsMap();
        if ([] === $constantsMap) {
            return $code;
        }

        $name = $config->getName();
        if (! array_key_exists($name, $constantsMap)) {
            return $code;
        }

        $constantsCode = '';
        foreach ($constantsMap[$name] as $constant => $value) {
            $constantsCode .= sprintf("\n    const %s = %s;\n", $constant, var_export($value, true));
        }

        $offset = strrpos($code, '}');
        if (false === $offset) {
            return $code;
        }

        return substr_replace($code, $constantsCode, $offset) . '}' . PHP_EOL;
    }
}
