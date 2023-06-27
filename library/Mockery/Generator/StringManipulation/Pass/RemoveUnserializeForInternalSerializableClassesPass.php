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

/**
 * Internal classes can not be instantiated with the newInstanceWithoutArgs
 * reflection method, so need the serialization hack. If the class also
 * implements Serializable, we need to replace the standard unserialize method
 * definition with a dummy
 */
class RemoveUnserializeForInternalSerializableClassesPass
{
    const DUMMY_METHOD_DEFINITION_LEGACY = 'public function unserialize($string) {} ';
    const DUMMY_METHOD_DEFINITION = 'public function unserialize(string $data): void {} ';

    public function apply($code, MockConfiguration $config)
    {
        $target = $config->getTargetClass();

        if (!$target) {
            return $code;
        }

        if (!$target->hasInternalAncestor() || !$target->implementsInterface("Serializable")) {
            return $code;
        }

        $code = $this->appendToClass($code, \PHP_VERSION_ID < 80100 ? self::DUMMY_METHOD_DEFINITION_LEGACY : self::DUMMY_METHOD_DEFINITION);

        return $code;
    }

    protected function appendToClass($class, $code)
    {
        $lastBrace = strrpos($class, "}");
        $class = substr($class, 0, $lastBrace) . $code . "\n    }\n";
        return $class;
    }
}
