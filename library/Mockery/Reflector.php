<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

use const PHP_VERSION_ID;

use function array_diff;
use function array_intersect;
use function array_map;
use function array_merge;
use function get_debug_type;
use function implode;
use function in_array;
use function method_exists;
use function sprintf;
use function strpos;
use function strtolower;

class Reflector
{
    /**
     * List of built-in types.
     *
     * @var list<string>
     */
    public const BUILTIN_TYPES = ['array', 'bool', 'int', 'float', 'null', 'object', 'string'];

    /**
     * List of reserved words.
     *
     * @var list<string>
     */
    public const RESERVED_WORDS = [
        'bool',
        'true',
        'false',
        'float',
        'int',
        'iterable',
        'mixed',
        'never',
        'null',
        'object',
        'string',
        'void'
    ];

    /**
     * Iterable.
     *
     * @var list<string>
     */
    private const ITERABLE = ['iterable'];

    /**
     * Traversable array.
     *
     * @var list<string>
     */
    private const TRAVERSABLE_ARRAY = ['\Traversable', 'array'];

    /**
     * Compute the string representation for the return type.
     *
     * @param  bool        $withoutNullable
     * @return null|string
     *
     * @throws InvalidArgumentException
     */
    public static function getReturnType(ReflectionMethod $method, $withoutNullable = false)
    {
        $type = $method->getReturnType();

        if (! $type instanceof ReflectionType && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (! $type instanceof ReflectionType) {
            return null;
        }

        $typeHint = self::getTypeFromReflectionType($type, $method->getDeclaringClass());

        return (! $withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Compute the string representation for the simplest return type.
     *
     * @return null|string
     */
    public static function getSimplestReturnType(ReflectionMethod $method)
    {
        $type = $method->getReturnType();

        if (! $type instanceof ReflectionType && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (! $type instanceof ReflectionType || $type->allowsNull()) {
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
     * Compute the string representation for the paramater type.
     *
     * @param  bool        $withoutNullable
     * @return null|string
     *
     * @throws InvalidArgumentException
     */
    public static function getTypeHint(ReflectionParameter $param, $withoutNullable = false)
    {
        if (! $param->hasType()) {
            return null;
        }

        $type = $param->getType();
        $declaringClass = $param->getDeclaringClass();
        $typeHint = self::getTypeFromReflectionType($type, $declaringClass);

        return (! $withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Determine if the parameter is typed as an array.
     *
     * @return bool
     */
    public static function isArray(ReflectionParameter $param)
    {
        $type = $param->getType();

        return $type instanceof ReflectionNamedType && $type->getName();
    }

    /**
     * Determine if the given type is a reserved word.
     */
    public static function isReservedWord(string $type): bool
    {
        return in_array(strtolower($type), self::RESERVED_WORDS, true);
    }

    /**
     * Format the given type as a nullable type.
     */
    private static function formatNullableType(string $typeHint): string
    {
        if ('mixed' === $typeHint) {
            return $typeHint;
        }

        if (strpos($typeHint, 'null') !== false) {
            return $typeHint;
        }

        if (PHP_VERSION_ID < 80000) {
            return sprintf('?%s', $typeHint);
        }

        return sprintf('%s|null', $typeHint);
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function getTypeFromReflectionType(ReflectionType $type, ReflectionClass $declaringClass): string
    {
        if ($type instanceof ReflectionNamedType) {
            $typeHint = $type->getName();

            if ($type->isBuiltin()) {
                return $typeHint;
            }

            if ('static' === $typeHint) {
                return $typeHint;
            }

            // 'self' needs to be resolved to the name of the declaring class
            if ('self' === $typeHint) {
                $typeHint = $declaringClass->getName();
            }

            // 'parent' needs to be resolved to the name of the parent class
            if ('parent' === $typeHint) {
                $typeHint = $declaringClass->getParentClass()->getName();
            }

            // class names need prefixing with a slash
            return sprintf('\\%s', $typeHint);
        }

        if ($type instanceof ReflectionIntersectionType) {
            $types = array_map(
                static function (ReflectionType $type) use ($declaringClass): string {
                    return self::getTypeFromReflectionType($type, $declaringClass);
                },
                $type->getTypes()
            );

            return implode('&', $types);
        }

        if ($type instanceof ReflectionUnionType) {
            $types = array_map(
                static function (ReflectionType $type) use ($declaringClass): string {
                    return self::getTypeFromReflectionType($type, $declaringClass);
                },
                $type->getTypes()
            );

            $intersect = array_intersect(self::TRAVERSABLE_ARRAY, $types);
            if (self::TRAVERSABLE_ARRAY === $intersect) {
                $types = array_merge(self::ITERABLE, array_diff($types, self::TRAVERSABLE_ARRAY));
            }

            return implode(
                '|',
                array_map(
                    static function (string $type): string {
                        return strpos($type, '&') === false ? $type : sprintf('(%s)', $type);
                    },
                    $types
                )
            );
        }

        throw new InvalidArgumentException('Unknown ReflectionType: ' . get_debug_type($type));
    }

    /**
     * Get the string representation of the given type.
     *
     * @return list<array{typeHint:string,isPrimitive:bool}>
     */
    private static function getTypeInformation(ReflectionType $type, ReflectionClass $declaringClass): array
    {
        // PHP 8 union types and PHP 8.1 intersection types can be recursively processed
        if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
            $types = [];

            foreach ($type->getTypes() as $innerType) {
                foreach (self::getTypeInformation($innerType, $declaringClass) as $info) {
                    if ('null' === $info['typeHint'] && $info['isPrimitive']) {
                        continue;
                    }

                    $types[] = $info;
                }
            }

            return $types;
        }

        // $type must be an instance of \ReflectionNamedType
        /** @var ReflectionNamedType $type */
        $typeHint = $type->getName();

        // builtins can be returned as is
        if ($type->isBuiltin()) {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => in_array($typeHint, self::BUILTIN_TYPES, true),
                ],
            ];
        }

        // 'static' can be returned as is
        if ('static' === $typeHint) {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => false,
                ],
            ];
        }

        // 'self' needs to be resolved to the name of the declaring class
        if ('self' === $typeHint) {
            $typeHint = $declaringClass->getName();
        }

        // 'parent' needs to be resolved to the name of the parent class
        if ('parent' === $typeHint) {
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
}
