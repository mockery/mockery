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

use const PHP_VERSION_ID;

use function strrpos;
use function substr;

/**
 * Internal classes can not be instantiated with the newInstanceWithoutArgs
 * reflection method, so need the serialization hack. If the class also
 * implements Serializable, we need to replace the standard unserialize method
 * definition with a dummy
 */
class RemoveUnserializeForInternalSerializableClassesPass implements Pass
{
    public const DUMMY_METHOD_DEFINITION = 'public function unserialize(string $data): void {} ';

    public const DUMMY_METHOD_DEFINITION_LEGACY = 'public function unserialize($string) {} ';

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

        if (! $targetClass->hasInternalAncestor() || ! $targetClass->implementsInterface('Serializable')) {
            return $code;
        }

        return $this->appendToClass(
            $code,
            PHP_VERSION_ID < 80100 ? self::DUMMY_METHOD_DEFINITION_LEGACY : self::DUMMY_METHOD_DEFINITION
        );
    }

    /**
     * @param  string $class
     * @param  string $code
     * @return string
     */
    protected function appendToClass($class, $code)
    {
        $lastBrace = strrpos($class, '}');

        return substr($class, 0, $lastBrace) . $code . "\n    }\n";
    }
}
