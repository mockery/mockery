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

    /**
     * Stores an array of currently active mocks
     *
     * @var array
     */
    protected static $_mocks = array();
    
    /**
     * Generate a new Mock Object, Partial Mock or Stub
     *
     * @param string|object
     * @return object
     */
    public static function mock($class)
    {
        $mock = new Mockery\Mock($class);
        self::_rememberMock($mock);
        return $mock;
    }
    
    /**
     * Stores a Mock for later reference
     *
     * @param object
     */
    protected static function _rememberMock($mock)
    {
        self::$_mocks[] = $mock;  
    }
    
    /**
     * Cleans up the static store for the next Mockery usage
     *
     * @return void
     */
    public static function close()
    {
        self::$_mocks = array();
    }

}
