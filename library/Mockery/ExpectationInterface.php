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

interface ExpectationInterface
{
    /**
     * @return int
     */
    public function getOrderNumber();

    /**
     * @return LegacyMockInterface|MockInterface
     */
    public function getMock();

    /**
     * Expected argument setter for the expectation
     *
     * @param mixed[] ...$args
     * @return self
     */
    public function with(...$args);

    /**
     * Expected arguments for the expectation passed as an array or a closure that matches each passed argument on
     * each function call.
     *
     * @param array|\Closure $argsOrClosure
     * @return self
     */
    public function withArgs($argsOrClosure);

    /**
     * Set with() as no arguments expected
     *
     * @return self
     */
    public function withNoArgs();

    /**
     * Set expectation that any arguments are acceptable
     *
     * @return self
     */
    public function withAnyArgs();

    /**
     * @param array ...$args
     * @return self
     */
    public function andReturn(...$args);

    /**
     * @return self
     */
    public function andReturns();

    /**
     * Indicates the number of times this expectation should occur
     *
     * @param int $limit
     * @throws \InvalidArgumentException
     * @return self
     */
    public function times($limit = null);

    /**
     * Indicates that this expectation is never expected to be called
     *
     * @return self
     */
    public function never();

    /**
     * Indicates that this expectation is expected exactly once
     *
     * @return self
     */
    public function once();

    /**
     * Indicates that this expectation is expected exactly twice
     *
     * @return self
     */
    public function twice();

    /**
     * Sets next count validator to the AtLeast instance
     *
     * @return self
     */
    public function atLeast();

    /**
     * Sets next count validator to the AtMost instance
     *
     * @return self
     */
    public function atMost();

    /**
     * Indicates this expectation should occur zero or more times
     *
     * @return self
     */
    public function zeroOrMoreTimes();

    /**
     * Shorthand for setting minimum and maximum constraints on call counts
     *
     * @param int $minimum
     * @param int $maximum
     */
    public function between($minimum, $maximum);
}
