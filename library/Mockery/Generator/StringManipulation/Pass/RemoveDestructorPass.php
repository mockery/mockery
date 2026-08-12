<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Exception;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\TargetClassInterface;
use Override;

use function preg_replace;

/**
 * Remove mock's empty destructor if we tend to use original class destructor
 */
class RemoveDestructorPass implements Pass
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

        if (! $config->isMockOriginalDestructor()) {
            return preg_replace('/public function __destruct\(\)\s+\{.*?\}/sm', '', $code);
        }

        return $code;
    }
}
