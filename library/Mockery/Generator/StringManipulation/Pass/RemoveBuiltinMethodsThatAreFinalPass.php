<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

/**
 * The standard Mockery\Mock class includes some methods to ease mocking, such
 * as __wakeup, however if the target has a final __wakeup method, it can't be
 * mocked. This pass removes the builtin methods where they are final on the
 * target
 */
class RemoveBuiltinMethodsThatAreFinalPass
{
    protected $methods = array(
        '__wakeup' => '/public function __wakeup\(\)\s+\{.*?\}/sm',
    );

    public function apply($code, MockConfiguration $config)
    {
        $target = $config->getTargetClass();

        if (!$target) {
            return $code;
        }

        foreach ($target->getMethods() as $method) {
            if ($method->isFinal() && isset($this->methods[$method->getName()])) {
                $code = preg_replace($this->methods[$method->getName()], '', $code);
            }
        }

        return $code;
    }
}
