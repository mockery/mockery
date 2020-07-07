<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2017 Dave Marshall https://github.com/davedevelopment
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery;

/**
 * @internal
 */
class Reflector
{
    /**
     * Determine if the parameter is typed as an array.
     *
     * @param \ReflectionParameter $param
     *
     * @return bool
     */
    public static function isArray(\ReflectionParameter $param)
    {
        if (\PHP_VERSION_ID < 70100) {
            return $param->isArray();
        }

        $type = $param->getType();

        return $type instanceof \ReflectionNamedType ? $type->getName() === 'array' : false;
    }

    /**
     * Compute the string representation for the paramater type.
     *
     * @param \ReflectionParameter $param
     * @param bool $withoutNullable
     *
     * @return string|null
     */
    public static function getTypeHint(\ReflectionParameter $param, $withoutNullable = false)
    {
        if (!$param->hasType()) {
            return null;
        }

        $type = $param->getType();
        $declaringClass = $param->getDeclaringClass()->getName();
        $typeHint = self::typeToString($type, $declaringClass);

        // PHP 7.1+ supports nullable types via a leading question mark
        return (!$withoutNullable && \PHP_VERSION_ID >= 70100 && $type->allowsNull()) ? sprintf('?%s', $typeHint) : $typeHint;
    }

    /**
     * Compute the string representation for the return type.
     *
     * @param \ReflectionParameter $param
     * @param bool $withoutNullable
     *
     * @return string|null
     */
    public static function getReturnType(\ReflectionMethod $method, $withoutNullable = false)
    {
        if (!$method->hasReturnType()) {
            return null;
        }

        $type = $method->getReturnType();
        $declaringClass = $method->getDeclaringClass()->getName();
        $typeHint = self::typeToString($type, $declaringClass);

        // PHP 7.1+ supports nullable types via a leading question mark
        return (!$withoutNullable && \PHP_VERSION_ID >= 70100 && $type->allowsNull()) ? sprintf('?%s', $typeHint) : $typeHint;
    }

    /**
     * Get the string representation of the given type.
     *
     * This method MUST only be called on PHP 7+.
     *
     * @param \ReflectionType $type
     * @param string $declaringClass
     *
     * @return string|null
     */
    private static function typeToString(\ReflectionType $type, $declaringClass)
    {
        // PHP 8 union types can be recursively processed
        if ($type instanceof \ReflectionUnionType) {
            return \implode('|', \array_map(function (\ReflectionType $type) use ($declaringClass) {
                return self::typeToString($type, $declaringClass);
            }, $type->getTypes()));
        }

        // PHP 7.0 doesn't have named types, but 7.1+ does
        $typeHint = $type instanceof \ReflectionNamedType ? $type->getName() : (string) $type;

        // 'self' needs to be resolved to the name of the declaring class and
        // 'static' is a special type reserved as a return type in PHP 8
        return ($type->isBuiltin() || $typeHint === 'static') ? $typeHint : sprintf('\\%s', $typeHint === 'self' ? $declaringClass : $typeHint);
    }
}
