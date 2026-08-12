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
use Mockery\Exception;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\TargetClassInterface;
use Override;

use function class_exists;
use function ltrim;
use function str_replace;

class ClassPass implements Pass
{
    /**
     * @param  string $code
     * @return string
     *
     * @throws Exception
     */
    #[Override]
    public function apply($code, MockConfiguration $config)
    {
        $targetClass = $config->getTargetClass();

        if (! $targetClass instanceof TargetClassInterface) {
            return $code;
        }

        if ($targetClass->isFinal()) {
            return $code;
        }

        $className = ltrim($targetClass->getName(), '\\');

        if (! class_exists($className)) {
            /** @var class-string $className */
            Mockery::declareClass($className);
        }

        return str_replace(
            'implements MockInterface',
            'extends \\' . $className . ' implements MockInterface',
            $code
        );
    }
}
