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
        $type = $param->getType();

        return $type instanceof \ReflectionNamedType && $type->getName();
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
        $declaringClass = $param->getDeclaringClass();
        $typeHint = self::typeToString($type, $declaringClass);

        return (!$withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
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
        $declaringClass = $method->getDeclaringClass();
        $typeHint = self::typeToString($type, $declaringClass);

        return (!$withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Get the string representation of the given type.
     *
     * @param \ReflectionType $type
     * @param string $declaringClass
     *
     * @return string|null
     */
    private static function typeToString(\ReflectionType $type, \ReflectionClass $declaringClass)
    {
        // PHP 8 union types can be recursively processed
        if ($type instanceof \ReflectionUnionType) {
            return \implode('|', \array_filter(\array_map(function (\ReflectionType $type) use ($declaringClass) {
                $typeHint = self::typeToString($type, $declaringClass);

                return $typeHint === 'null' ? null : $typeHint;
            }, $type->getTypes())));
        }

        // $type must be an instance of \ReflectionNamedType
        $typeHint = $type->getName();

        // builtins and 'static' can be returned as is
        if (($type->isBuiltin() || $typeHint === 'static')) {
            return $typeHint;
        }

        // 'self' needs to be resolved to the name of the declaring class
        if ($typeHint === 'self') {
            $typeHint = $declaringClass->getName();
        }

        // 'parent' needs to be resolved to the name of the parent class
        if ($typeHint === 'parent') {
            $typeHint = $declaringClass->getParentClass()->getName();
        }

        // class names need prefixing with a slash
        return sprintf('\\%s', $typeHint);
    }

    /**
     * Format the given type as a nullable type.
     *
     * This method MUST only be called on PHP 7.1+.
     *
     * @param string $typeHint
     *
     * @return string
     */
    private static function formatNullableType($typeHint)
    {
        if (\PHP_VERSION_ID < 80000) {
            return sprintf('?%s', $typeHint);
        }

        return $typeHint === 'mixed' ? 'mixed' : sprintf('%s|null', $typeHint);
    }
}
