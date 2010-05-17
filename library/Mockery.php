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

class Mockery
{

    protected static $_container = null;
    
    public static function mock()
    {
        if (is_null(self::$_container)) {
            self::$_container = new \Mockery\Container;
        }
        $args = func_get_args();
        return call_user_func_array(array(self::$_container, 'mock'), $args);
    }
    
    public static function close()
    {
        self::$_container->mockery_close();
        self::$_container = null;
    }

    /**
     * Return instance of ANY matcher
     *
     * @return
     */
    public static function any()
    {
        $return = new \Mockery\Matcher\Any();
        return $return;
    }
    
    /**
     * Return instance of TYPE matcher
     *
     * @return
     */
    public static function type($expected)
    {
        $return = new \Mockery\Matcher\Type($expected);
        return $return;
    }
    
    /**
     * Return instance of DUCKTYPE matcher
     *
     * @return
     */
    public static function ducktype()
    {
        $return = new \Mockery\Matcher\Ducktype(func_get_args());
        return $return;
    }
    
    /**
     * Return instance of ARRAY matcher
     *
     * @return
     */
    public static function contains(array $part)
    {
        $return = new \Mockery\Matcher\Contains($part);
        return $return;
    }
    
    /**
     * Return instance of CLOSURE matcher
     *
     * @return
     */
    public static function on($closure)
    {
        $return = new \Mockery\Matcher\Closure($closure);
        return $return;
    }
    
    /**
     * Utility method to format method name and args into a string
     *
     * @param string $method
     * @param array $args
     * @return string
     */
    public static function formatArgs($method, array $args = null)
    {
        $return = $method . '(';
        if ($args && !empty($args)) {
            $parts = array();
            foreach($args as $arg) {
                if (is_object($arg)) {
                    $parts[] = get_class($arg);
                } elseif (is_int($arg) || is_float($arg)) {
                    $parts[] = $arg;
                } elseif (is_array($arg)) {
                    $parts[] = 'Array';
                } else {
                    $parts[] = '"' . (string) $arg . '"';
                }
            }
            $return .= implode(', ', $parts); // TODO: improve format
            
        }
        $return .= ')';
        return $return;
    }
    
    /**
     * Utility function to parse shouldReceive() arguments and generate
     * expectations from such as needed.
     *
     * @param \Mockery\MockInterface
     * @param array $args
     * @return \Mockery\CompositeExpectation
     */
    public static function parseShouldReturnArgs(\Mockery\MockInterface $mock, $args, $add)
    {
        $composite = new \Mockery\CompositeExpectation;
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach($arg as $k=>$v) {
                    $expectation = self::_buildDemeterChain($mock, $k, $add)->andReturn($v);
                    $composite->add($expectation);
                }
            } elseif (is_string($arg)) {
                $expectation = self::_buildDemeterChain($mock, $arg, $add);
                $composite->add($expectation);
            }
        }
        return $composite;
    }
    
    protected static function _buildDemeterChain(\Mockery\MockInterface $mock, $arg, $add)
    {
        $container = $mock->mockery_getContainer();
        $names = explode('->', $arg);
        $exp = null;
        $nextExp = function ($n) use ($add) {return $add($n);};
        while (true) {
            $method = array_shift($names);
            $exp = $mock->mockery_getExpectationsFor($method);
            $needNew = false;
            if (is_null($exp) || empty($names)) {
                $needNew = true;
            }
            if ($needNew) $exp = $nextExp($method);
            if (empty($names)) break;
            if ($needNew) {
                $mock = $container->mock('demeter_' . $method);
                $exp->withNoArgs()->andReturn($mock);
            }
            $nextExp = function ($n) use ($mock) {return $mock->shouldReceive($n);};
        }
        return $exp;
    }
}
