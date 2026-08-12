<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery;
use Mockery\Generator\MockConfiguration;
use Override;

use function array_reduce;
use function interface_exists;
use function ltrim;
use function str_replace;

class InterfacePass implements Pass
{
    /**
     * @param  non-empty-string $code
     * @return non-empty-string
     */
    #[Override]
    public function apply($code, MockConfiguration $config)
    {
        $targetInterfaces = $config->getTargetInterfaces();

        foreach ($targetInterfaces as $targetInterface) {
            $name = ltrim($targetInterface->getName(), '\\');

            if (interface_exists($name)) {
                continue;
            }

            /** @var class-string $name */
            Mockery::declareInterface($name);
        }

        $interfaces = array_reduce($targetInterfaces, static function ($code, $targetInterface) {
            return $code . ', \\' . ltrim($targetInterface->getName(), '\\');
        }, '');

        return str_replace('implements MockInterface', 'implements MockInterface' . $interfaces, $code);
    }
}
