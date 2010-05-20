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

class Recorder
{

    /**
     * Mock object on which all recorded interactions will be set as
     * expectations
     *
     * @var object
     */
    protected $_mock = null;
    
    /**
     * The subject object whose interactions are being recorded
     *
     * @var object
     */
    protected $_subject = null;
    
    /**
     * Flag indicating whether the recording should maintain a strict adherence
     * to the recorded interactions, i.e. the usual Mockery flexibility is
     * suspended, ordering is enforced, and arguments received are set as
     * exact requirements.
     *
     * @var bool
     */
    protected $_strict = false;
    
    /**
     * Construct accepting the mock object on which expectations are to be
     * recorded. The second parameter is the subject object, passed into
     * a \Mockery::mock() call in the same way as a partial mock requires.
     *
     * @param \Mockery\MockInterface $mock
     * @param object $subject
     * @return void
     */
    public function __construct(\Mockery\MockInterface $mock, $subject)
    {
        $this->_mock = $mock;
        $this->_subject = $subject;
    }
    
    /**
     * Sets the recorded into strict mode where method calls are more strictly
     * matched against the argument and call count and ordering is also
     * set as enforceable.
     *
     * @return void
     */
    public function shouldBeStrict()
    {
        $this->_strict = true;
    }

    /**
     * Intercept all calls on the subject, and use the call details to create
     * a new expectation. The actual return value is then returned after being
     * recorded.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args)
    {
        $return = call_user_func_array(array($this->_subject, $method), $args);
        $expectation = $this->_mock->shouldReceive($method)->andReturn($return);
        if ($this->_strict) {
            $exactArgs = array();
            foreach ($args as $arg) {
                $exactArgs[] = \Mockery::mustBe($arg);
            }
            $expectation->once()->ordered();
            call_user_func_array(array($expectation, 'with'), $exactArgs);
        } else {
            call_user_func_array(array($expectation, 'with'), $args);
        }
        return $return;
    }
}
