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

class ClassPass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        $target = $config->getTargetClass();

        if (!$target) {
            return $code;
        }

        if ($target->isFinal()) {
            return $code;
        }

        $className = ltrim($target->getName(), "\\");

        if (!class_exists($className)) {
            \Mockery::declareClass($className);
        }

        $parts = preg_split("/{/", $code, 2);

        $parts[0] = str_replace(
            "implements MockInterface",
            "extends \\" . $className . " implements MockInterface",
            $parts[0]
        );

        return $parts[0] . "{" . $parts[1];
    }
}
