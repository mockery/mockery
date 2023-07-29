<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use ReflectionType;

/**
 * @internal
 */
class Reflector
{
    private const TRAVERSABLE_ARRAY = ['\Traversable', 'array'];
    private const ITERABLE = ['iterable'];
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
        $typeHint = self::getTypeFromReflectionType($type, $declaringClass);

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
        $type = $method->getReturnType();

        if (!$type instanceof ReflectionType && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();    
        }

        if (!$type instanceof ReflectionType) {
            return null;
        }

        $typeHint = self::getTypeFromReflectionType($type, $method->getDeclaringClass());

        return (!$withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Compute the string representation for the simplest return type.
     *
     * @param \ReflectionParameter $param
     *
     * @return string|null
     */
    public static function getSimplestReturnType(\ReflectionMethod $method)
    {
        $type = $method->getReturnType();

        if (!$type instanceof ReflectionType && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (!$type instanceof ReflectionType || $type->allowsNull()) {
            return null;
        }

        $typeInformation = self::getTypeInformation($type, $method->getDeclaringClass());

        // return the first primitive type hint
        foreach ($typeInformation as $info) {
            if ($info['isPrimitive']) {
                return $info['typeHint'];
            }
        }

        // if no primitive type, return the first type
        foreach ($typeInformation as $info) {
            return $info['typeHint'];
        }

        return null;
    }

    /**
     * Get the string representation of the given type.
     *
     * @param \ReflectionType  $type
     * @param \ReflectionClass $declaringClass
     *
     * @return list<array{typeHint: string, isPrimitive: bool}>
     */
    private static function getTypeInformation(\ReflectionType $type, \ReflectionClass $declaringClass)
    {
        // PHP 8 union types and PHP 8.1 intersection types can be recursively processed
        if ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
            $types = [];

            foreach ($type->getTypes() as $innterType) {
                foreach (self::getTypeInformation($innterType, $declaringClass) as $info) {
                    if ($info['typeHint'] === 'null' && $info['isPrimitive']) {
                        continue;
                    }

                    $types[] = $info;
                }
            }

            return $types;
        }

        // $type must be an instance of \ReflectionNamedType
        $typeHint = $type->getName();

        // builtins can be returned as is
        if ($type->isBuiltin()) {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => in_array($typeHint, ['array', 'bool', 'int', 'float', 'null', 'object', 'string']),
                ],
            ];
        }

        // 'static' can be returned as is
        if ($typeHint === 'static') {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => false,
                ],
            ];
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
        return [
            [
                'typeHint' => sprintf('\\%s', $typeHint),
                'isPrimitive' => false,
            ],
        ];
    }

    /**
     * Format the given type as a nullable type.
     *
     * @param string $typeHint
     *
     * @return string
     */
    private static function formatNullableType($typeHint)
    {
        if ($typeHint === 'mixed') {
            return $typeHint;
        }

        if (strpos($typeHint, 'null') !== false) {
            return $typeHint;
        }

        if (\PHP_VERSION_ID < 80000) {
            return sprintf('?%s', $typeHint);
        }

        return sprintf('%s|null', $typeHint);
    }

    private static function getTypeFromReflectionType(\ReflectionType $type, \ReflectionClass $declaringClass): string
    {
        if ($type instanceof \ReflectionNamedType) {
            $typeHint = $type->getName();

            if ($type->isBuiltin()) {
                return $typeHint;
            }

            if ($typeHint === 'static') {
                return $typeHint;
            }

            // 'self' needs to be resolved to the name of the declaring class
            if ($typeHint === 'self'){
                $typeHint = $declaringClass->getName();
            }

            // 'parent' needs to be resolved to the name of the parent class
            if ($typeHint === 'parent'){
                $typeHint = $declaringClass->getParentClass()->getName();
            }

            // class names need prefixing with a slash
            return sprintf('\\%s', $typeHint);
        }

        if ($type instanceof \ReflectionIntersectionType) {
            $types = array_map(
                static function (\ReflectionType $type) use ($declaringClass): string {
                    return self::getTypeFromReflectionType($type, $declaringClass);
                },
                $type->getTypes()
            );

            return implode(
                '&',
                $types,
            );
        }

        if ($type instanceof \ReflectionUnionType) {
            $types = array_map(
                static function (\ReflectionType $type) use ($declaringClass): string {
                    return self::getTypeFromReflectionType($type, $declaringClass);
                },
                $type->getTypes()
            );

            $intersect = array_intersect(self::TRAVERSABLE_ARRAY, $types);
            if (self::TRAVERSABLE_ARRAY === $intersect) {
                $types = self::ITERABLE + array_diff($types, self::TRAVERSABLE_ARRAY);
            }

            return implode(
                '|',
                array_map(
                    static function (string $type): string
                    {
                        return strpos($type, '&') === false ? $type : sprintf('(%s)', $type);
                    },
                    $types
                )
            );
        }

        throw new \InvalidArgumentException('Unknown ReflectionType: ' . get_debug_type($type));
    }
}
