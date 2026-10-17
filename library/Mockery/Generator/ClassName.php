<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Mockery\Exception;
use Throwable;

use function preg_match;
use function sprintf;
use function trim;

/**
 * @see \Tests\Unit\Mockery\Generator\ClassNameTest
 */
final class ClassName
{
    /**
     * @var string
     */
    private const ANONYMOUS_CLASS_REGEX = '#^\\\\?(?:[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*\\\\)*[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*@anonymous\x00.+:\d+\$\d+$#';

    /**
     * See http://php.net/manual/en/language.oop5.basic.php
     *
     * @var string
     */
    private const CLASS_NAME_REGEX = '#^\\\\?(?:[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*\\\\)*[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$#';

    /**
     * Checks if the given class name is a valid anonymous class name.
     *
     * @psalm-assert-if-true class-string $name
     */
    public static function isAnonymousClass(string $name): bool
    {
        if (trim($name) === '') {
            return false;
        }

        return preg_match(self::ANONYMOUS_CLASS_REGEX, $name) === 1;
    }

    /**
     * Checks if the given name is a valid class name.
     *
     * @psalm-assert-if-true class-string $name
     */
    public static function isClass(string $name): bool
    {
        if (trim($name) === '') {
            return false;
        }

        return preg_match(self::CLASS_NAME_REGEX, $name) === 1;
    }

    /**
     * Validates fully qualified class names, qualified class names, and non-namespaced class names according to PHP naming rules.
     *
     * example:
     *  - MyClass
     *  - MyNamespace\MyClass
     *  - \MyNamespace\MyClass
     *
     * @psalm-assert class-string $name
     *
     * @return class-string
     *
     * @throws Exception If the class name is invalid
     */
    public static function validate(string $name): string
    {
        if (! self::isClass($name)) {
            try {
                throw new Exception(sprintf('Class name contains invalid characters: "%s"', $name));
            } catch (Throwable $e) {
                throw new Exception($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }

        return $name;
    }

    /**
     * Validates either class or anonymous class names according to PHP naming rules.
     *
     * example:
     *  - MyClass
     *  - MyNamespace\MyClass
     *  - \MyNamespace\MyClass
     *  - class@anonymous\0path:0$0
     *  - MyNamespace\Myclass@anonymous\0path:0$0
     *  - \MyNamespace\Myclass@anonymous\0path:0$0
     *
     * @psalm-assert class-string $name
     *
     * @return class-string
     *
     * @throws Exception If the class name is invalid
     */
    public static function validateClassOrAnonymousClass(string $name): string
    {
        if (! self::isClass($name) && ! self::isAnonymousClass($name)) {
            throw new Exception(sprintf('Class name is not valid or anonymous: "%s"', $name));
        }

        return $name;
    }
}
