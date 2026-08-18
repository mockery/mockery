<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\AnyArgs;
use Mockery\MockInterface;

if (! \function_exists('andAnyOtherArgs')) {
    function andAnyOtherArgs(): AndAnyOtherArgs
    {
        return new AndAnyOtherArgs();
    }
}

if (! \function_exists('andAnyOthers')) {
    function andAnyOthers(): AndAnyOtherArgs
    {
        return new AndAnyOtherArgs();
    }
}

if (! \function_exists('anyArgs')) {
    function anyArgs(): AnyArgs
    {
        return new AnyArgs();
    }
}

if (! \function_exists('get_debug_type')) {
    /**
     * Copied from symfony/polyfill (https://github.com/symfony/polyfill/blob/1.x/src/Php80/Php80.php)
     *
     * @copyright Fabien Potencier
     * @license https://github.com/symfony/polyfill/blob/1.x/src/Php80/LICENSE
     *
     * @param mixed $value
     */
    function get_debug_type($value): string
    {
        switch (true) {
            case null === $value: return 'null';
            case \is_bool($value): return 'bool';
            case \is_string($value): return 'string';
            case \is_array($value): return 'array';
            case \is_int($value): return 'int';
            case \is_float($value): return 'float';
            case \is_object($value): break;
            case $value instanceof \__PHP_Incomplete_Class: return '__PHP_Incomplete_Class';
            default:
                if (null === $type = @\get_resource_type($value)) {
                    return 'unknown';
                }

                if ('Unknown' === $type) {
                    $type = 'closed';
                }

                return "resource ({$type})";
        }

        $class = \get_class($value);

        if (! \str_contains($class, '@')) {
            return $class;
        }

        $parent = \get_parent_class($class);
        if (false !== $parent) {
            return $parent . '@anonymous';
        }

        $interfaces = \class_implements($class);
        if (false === $interfaces) {
            return 'class@anonymous';
        }

        $parent = \key($interfaces);
        if (null === $parent) {
            return 'class@anonymous';
        }

        return $parent . '@anonymous';
    }
}

if (! \function_exists('mock')) {
    /**
     * @param  mixed         ...$args
     * @return MockInterface
     *
     * @throws Throwable
     */
    function mock(...$args)
    {
        return Mockery::mock(...$args);
    }
}

if (! \function_exists('namedMock')) {
    /**
     * @param  mixed         ...$args
     * @return MockInterface
     *
     * @throws Throwable
     */
    function namedMock(...$args)
    {
        return Mockery::namedMock(...$args);
    }
}

if (! \function_exists('spy')) {
    /**
     * @param  mixed         ...$args
     * @return MockInterface
     *
     * @throws Throwable
     */
    function spy(...$args)
    {
        return Mockery::spy(...$args);
    }
}

if (! \function_exists('str_contains')) {
    /**
     * Copied from symfony/polyfill (https://github.com/symfony/polyfill/blob/1.x/src/Php80/Php80.php)
     *
     * @copyright Fabien Potencier
     * @license https://github.com/symfony/polyfill/blob/1.x/src/Php80/LICENSE
     */
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || \strpos($haystack, $needle) !== false;
    }
}

if (! \function_exists('str_ends_with')) {
    /**
     * Modified copy from symfony/polyfill (https://github.com/symfony/polyfill/blob/1.x/src/Php80/Php80.php)
     *
     * @copyright Fabien Potencier
     * @license https://github.com/symfony/polyfill/blob/1.x/src/Php80/LICENSE
     */
    function str_ends_with(string $haystack, string $needle): bool
    {
        if ('' === $needle || $needle === $haystack) {
            return true;
        }

        if ('' === $haystack) {
            return false;
        }

        $needleLength = \strlen($needle);
        if ($needleLength > \strlen($haystack)) {
            return false;
        }

        return \substr_compare($haystack, $needle, -$needleLength) === 0;
    }
}

if (! \function_exists('str_starts_with')) {
    /**
     * Copied from symfony/polyfill (https://github.com/symfony/polyfill/blob/1.x/src/Php80/Php80.php)
     *
     * @copyright Fabien Potencier
     * @license https://github.com/symfony/polyfill/blob/1.x/src/Php80/LICENSE
     */
    function str_starts_with(string $haystack, string $needle): bool
    {
        return \strncmp($haystack, $needle, \strlen($needle)) === 0;
    }
}

if (! \function_exists('array_is_list')) {
    /**
     * Modified copy from https://www.php.net/manual/en/function.array-is-list.php#127044
     *
     * @license https://www.php.net/manual/en/cc.license.php
     *
     * @param array<mixed> $array
     */
    function array_is_list(array $array): bool
    {
        $i = -1;
        foreach ($array as $k => $v) {
            if ($k !== ++$i) {
                return false;
            }
        }

        return true;
    }
}
