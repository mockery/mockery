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
 
namespace Mockery;

class Configuration
{

    /**
     * Boolean assertion of whether we can mock methods which do not actually
     * exist for the given class or object (ignored for unreal mocks)
     *
     * @var bool
     */
    protected $_allowMockingNonExistentMethod = true;
    
    /**
     * Boolean assertion of whether we ignore unnecessary mocking of methods,
     * i.e. when method expectations are made, set using a zeroOrMoreTimes()
     * constraint, and then never called. Essentially such expectations are
     * not required and are just taking up test space.
     *
     * @var bool
     */
    protected $_allowMockingMethodsUnnecessarily = true;
    
    /**
     * Parameter map for use with PHP internal classes.
     *
     * @var array
     */
    protected $_internalClassParamMap = array();
    
    /**
     * Set boolean to allow/prevent mocking of non-existent methods
     *
     * @param bool
     */
    public function allowMockingNonExistentMethods($flag = true)
    {
        $this->_allowMockingNonExistentMethod = (bool) $flag;
    }
    
    /**
     * Return flag indicating whether mocking non-existent methods allowed
     *
     * @return bool
     */
    public function mockingNonExistentMethodsAllowed()
    {
        return $this->_allowMockingNonExistentMethod;
    }
    
    /**
     * Set boolean to allow/prevent unnecessary mocking of methods
     *
     * @param bool
     */
    public function allowMockingMethodsUnnecessarily($flag = true)
    {
        $this->_allowMockingMethodsUnnecessarily = (bool) $flag;
    }
    
    /**
     * Return flag indicating whether mocking non-existent methods allowed
     *
     * @return bool
     */
    public function mockingMethodsUnnecessarilyAllowed()
    {
        return $this->_allowMockingMethodsUnnecessarily;
    }
    
    /**
     * Set a parameter map (array of param signature strings) for the method
     * of an internal PHP class.
     *
     * @param string $class
     * @param string $method
     * @param array $map
     */
    public function setInternalClassMethodParamMap($class, $method, array $map)
    {
        if (!isset($this->_internalClassParamMap[strtolower($class)])) {
            $this->_internalClassParamMap[strtolower($class)] = array();
        }
        $this->_internalClassParamMap[strtolower($class)][strtolower($method)] = $map;
    }
    
    /**
     * Get the parameter map of an internal PHP class method
     *
     * @return array
     */
    public function getInternalClassMethodParamMap($class, $method)
    {
        if (isset($this->_internalClassParamMap[strtolower($class)][strtolower($method)])) {
            return $this->_internalClassParamMap[strtolower($class)][strtolower($method)];
        }
    }

}
