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

class TraitPass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        $traits = $config->getTargetTraits();

        if (empty($traits)) {
            return $code;
        }

        $useStatements = array_map(function ($trait) {
            return "use \\\\" . ltrim($trait->getName(), "\\") . ";";
        }, $traits);

        $code = preg_replace(
            '/^{$/m',
            "{\n    " . implode("\n    ", $useStatements) . "\n",
            $code
        );

        return $code;
    }
}
