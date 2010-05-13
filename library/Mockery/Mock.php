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

class Mock
{

    /**
     * Stores an array of all expectation directors for this mock
     *
     * @var array
     */
    protected $_expectations = array();
    
    /**
     * Last expectation that was set
     *
     * @var object
     */
    protected $_lastExpectation = null;
    
    /**
     * Flag to indicate whether we can ignore method calls missing from our
     * expectations
     *
     * @var bool
     */
    protected $_ignoreMissing = false;
    
    /**
     * Flag to indicate whether this mock was verified
     *
     * @var bool
     */
    protected $_verified = false;
    
    /**
     * Given name of the mock
     *
     * @var string
     */
    protected $_name = null;

    /**
     * Constructor
     *
     * @param string $class
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }
    
    /**
     * Set expected method calls
     *
     * @param string $method
     * @return
     */
    public function shouldReceive($method)
    {
        $this->_expectations[$method] = new Mockery\ExpectationDirector($method);
        $expectation = new Mockery\Expectation($this, $method);
        $this->_expectations[$method]->addExpectation($expectation);
        $this->_lastExpectation = $expectation;
    }

}
