<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Closure;
use Throwable;

interface LegacyMockInterface
{
    /**
     * In the event shouldReceive() accepting an array of methods/returns
     * this method will switch them from normal expectations to default
     * expectations
     *
     * @return static
     */
    public function byDefault();

    /**
     * Set mock to defer unexpected methods to its parent if possible
     *
     * @return static
     */
    public function makePartial();

    /**
     * Fetch the next available allocation order number
     *
     * @return int
     */
    public function mockery_allocateOrder();

    /**
     * Find an expectation matching the given method and arguments
     *
     * @param  string           $method
     * @param  array<mixed>     $args
     * @return null|Expectation
     */
    public function mockery_findExpectation($method, array $args);

    /**
     * Return the container for this mock
     *
     * @return Container
     */
    public function mockery_getContainer();

    /**
     * Get current ordered number
     *
     * @return int
     */
    public function mockery_getCurrentOrder();

    /**
     * Gets the count of expectations for this mock
     *
     * @return int
     */
    public function mockery_getExpectationCount();

    /**
     * Return the expectations director for the given method
     *
     * @param  string                   $method
     * @return null|ExpectationDirector
     */
    public function mockery_getExpectationsFor($method);

    /**
     * Fetch array of ordered groups
     *
     * @return array<string,int>
     */
    public function mockery_getGroups();

    /**
     * @return list<string>
     */
    public function mockery_getMockableMethods();

    /**
     * @return array<string,mixed>
     */
    public function mockery_getMockableProperties();

    /**
     * Return the name for this mock
     *
     * @return string
     */
    public function mockery_getName();

    /**
     * Alternative setup method to constructor
     *
     * @param  object $partialObject
     * @return void
     */
    public function mockery_init(?Container $container = null, $partialObject = null);

    /**
     * @return bool
     */
    public function mockery_isAnonymous();

    /**
     * Set current ordered number
     *
     * @param  int  $order
     * @return void
     */
    public function mockery_setCurrentOrder($order);

    /**
     * Return the expectations director for the given method
     *
     * @param  string $method
     * @return void
     */
    public function mockery_setExpectationsFor($method, ExpectationDirector $director);

    /**
     * Set ordering for a group
     *
     * @param  string $group
     * @param  int    $order
     * @return void
     */
    public function mockery_setGroup($group, $order);

    /**
     * Tear down tasks for this mock
     *
     * @return void
     */
    public function mockery_teardown();

    /**
     * Validate the current mock's ordering
     *
     * @param  string $method
     * @param  int    $order
     * @return void
     *
     * @throws Exception
     */
    public function mockery_validateOrder($method, $order);

    /**
     * Iterate across all expectation directors and validate each
     *
     * @return void
     *
     * @throws Throwable
     */
    public function mockery_verify();

    /**
     * Allows additional methods to be mocked that do not explicitly exist on mocked class
     *
     * @param  string $method the method name to be mocked
     * @return static
     */
    public function shouldAllowMockingMethod($method);

    /**
     * @return static
     */
    public function shouldAllowMockingProtectedMethods();

    /**
     * Set mock to defer unexpected methods to its parent if possible
     *
     * @deprecated since 1.4.0. Please use makePartial() instead.
     *
     * @return static
     */
    public function shouldDeferMissing();

    /**
     * @return VerificationDirector
     */
    public function shouldHaveBeenCalled();

    /**
     * @param  string                                                        $method
     * @param  null|array<mixed>|Closure                                     $args
     * @return ($method is null ? HigherOrderMessage : VerificationDirector)
     */
    public function shouldHaveReceived($method, $args = null);

    /**
     * Set mock to ignore unexpected methods and return Undefined class
     *
     * @param  null|mixed $returnValue the default return value for calls to missing functions on this mock
     * @return static
     */
    public function shouldIgnoreMissing($returnValue = null);

    /**
     * @param  null|array<mixed> $args (optional)
     * @return void
     */
    public function shouldNotHaveBeenCalled(?array $args = null);

    /**
     * @param  null|string                                   $method
     * @param  null|array<mixed>|Closure                     $args
     * @return ($method is null ? HigherOrderMessage : void)
     */
    public function shouldNotHaveReceived($method, $args = null);

    /**
     * Shortcut method for setting an expectation that a method should not be called.
     *
     * @param  string|array<string,mixed>                                  ...$methodNames one or many methods that are expected not to be called in this mock
     * @return ($methodNames is list{} ? HigherOrderMessage : Expectation)
     */
    public function shouldNotReceive(...$methodNames);

    /**
     * Set expected method calls
     *
     * @param  string|array<string,mixed>                                  ...$methodNames one or many methods that are expected to be called in this mock
     * @return ($methodNames is list{} ? HigherOrderMessage : Expectation)
     */
    public function shouldReceive(...$methodNames);
}
