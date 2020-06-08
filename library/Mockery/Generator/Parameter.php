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
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Generator;

use Mockery\Reflector;

class Parameter
{
    private static $parameterCounter;

    private $rfp;

    public function __construct(\ReflectionParameter $rfp)
    {
        $this->rfp = $rfp;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->rfp, $method), $args);
    }

    public function getClass()
    {
        if ($class = Reflector::getClass($this->rfp)) {
            return new DefinedTargetClass($class);
        }
    }

    public function getTypeHintAsString()
    {
        return Reflector::getTypeHint($this->rfp);
    }

    /**
     * Some internal classes have funny looking definitions...
     */
    public function getName()
    {
        $name = $this->rfp->getName();
        if (!$name || $name == '...') {
            $name = 'arg' . static::$parameterCounter++;
        }

        return $name;
    }


    /**
     * Variadics only introduced in 5.6
     */
    public function isVariadic()
    {
        return PHP_VERSION_ID >= 50600 && $this->rfp->isVariadic();
    }
}
