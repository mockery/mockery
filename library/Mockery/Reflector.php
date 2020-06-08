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
    public static function isArray(\ReflectionParameter $param)
    {
        if (PHP_VERSION_ID < 70100) {
            return $param->isArray();
        }

        return $param->hasType() && $param->getType()->getName() === 'array';
    }

    public static function getTypeHint(\ReflectionParameter $param)
    {
        // Handle HHVM typing
        if (method_exists($param, 'getTypehintText')) {
            $typehint = $param->getTypehintText();

            if (in_array($typehint, array('int', 'integer', 'float', 'string', 'bool', 'boolean'), true)) {
                return '';
            }

            return $typehint;
        }

        // Handle array typing
        if (self::isArray($param)) {
            return 'array';
        }

        // PHP < 5.4.1 has some strange behaviour with a typehint of self and
        // subclass signatures, so we risk the regexp instead. PHP 7 replaces
        // getClass with getType, with 7.1 supporting named types.
        if (PHP_VERSION_ID < 50401) {
            if (preg_match('/^Parameter #[0-9]+ \[ \<(required|optional)\> (?<typehint>\S+ )?.*\$' . $param->getName() . ' .*\]$/', (string) $param, $typehintMatch)) {
                if (!empty($typehintMatch['typehint'])) {
                    return $typehintMatch['typehint'];
                }
            }
        } elseif (PHP_VERSION_ID < 70000) {
            try {
                if ($param->getClass()) {
                    return $param->getClass()->getName();
                }
            } catch (\ReflectionException $re) {
                // noop
            }
        } elseif ($param->hasType()) {
            return PHP_VERSION_ID >= 70100 ? $param->getType()->getName() : (string) $param->getType();
        }

        return '';
    }

    public static function getClass(\ReflectionParameter $param)
    {
        if ($className = self::getTypeHint($param)) {
            try {
                return new \ReflectionClass($className);
            } catch (\ReflectionException $re) {
                // noop
            }
        }
    }
}
