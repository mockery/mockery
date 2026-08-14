<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Mockery;
use Mockery\Exception\NoMatchingExpectationException;
use Throwable;

use const PHP_EOL;

use function array_pop;
use function array_unshift;

use function end;

class ExpectationDirector
{
    /**
     * Stores an array of all default expectations for this mock
     *
     * @var list<Expectation>
     */
    protected $_defaults = [];

    /**
     * Stores an array of all expectations for this mock
     *
     * @var list<Expectation>
     */
    protected $_expectations = [];

    /**
     * The expected order of next call
     *
     * @var int
     */
    protected $_expectedOrder;

    /**
     * Mock object the director is attached to
     *
     * @var LegacyMockInterface|MockInterface
     */
    protected $_mock;

    /**
     * Method name the director is directing
     *
     * @var string
     */
    protected $_name;

    /**
     * @param string $name
     */
    public function __construct($name, LegacyMockInterface $mock)
    {
        $this->_name = $name;
        $this->_mock = $mock;
    }

    /**
     * Add a new expectation to the director
     */
    public function addExpectation(Expectation $expectation)
    {
        $this->_expectations[] = $expectation;
    }

    /**
     * Handle a method call being directed by this instance
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function call(array $args)
    {
        $expectation = $this->findExpectation($args);
        if (null !== $expectation) {
            return $expectation->verifyCall($args);
        }

        $noMatchingExpectationException = new NoMatchingExpectationException(
            'No matching handler found for '
            . $this->_mock->mockery_getName() . '::'
            . Mockery::formatArgs($this->_name, $args)
            . '. Either the method was unexpected or its arguments matched'
            . ' no expected argument list for this method'
            . PHP_EOL . PHP_EOL
            . Mockery::formatObjects($args)
        );

        $noMatchingExpectationException->setMock($this->_mock)
            ->setMethodName($this->_name)
            ->setActualArguments($args);

        throw $noMatchingExpectationException;
    }

    /**
     * Attempt to locate an expectation matching the provided args
     *
     * @return null|Expectation
     */
    public function findExpectation(array $args)
    {
        $expectation = null;

        if ([] !== $this->_expectations) {
            $expectation = $this->_findExpectationIn($this->_expectations, $args);
        }

        if (null === $expectation && [] !== $this->_defaults) {
            return $this->_findExpectationIn($this->_defaults, $args);
        }

        return $expectation;
    }

    /**
     * Return all expectations assigned to this director
     *
     * @return array<Expectation>
     */
    public function getDefaultExpectations()
    {
        return $this->_defaults;
    }

    /**
     * Return the number of expectations assigned to this director.
     *
     * @return int
     */
    public function getExpectationCount()
    {
        $count = 0;

        $expectations = $this->getExpectations();

        if ([] === $expectations) {
            $expectations = $this->getDefaultExpectations();
        }

        foreach ($expectations as $expectation) {
            if ($expectation->isCallCountConstrained()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Return all expectations assigned to this director
     *
     * @return array<Expectation>
     */
    public function getExpectations()
    {
        return $this->_expectations;
    }

    /**
     * Make the given expectation a default for all others assuming it was correctly created last
     *
     * @return void
     *
     * @throws Exception
     */
    public function makeExpectationDefault(Expectation $expectation)
    {
        if (end($this->_expectations) === $expectation) {
            array_pop($this->_expectations);

            array_unshift($this->_defaults, $expectation);

            return;
        }

        throw new Exception('Cannot turn a previously defined expectation into a default');
    }

    /**
     * Verify all expectations of the director
     *
     * @return void
     *
     * @throws Exception
     */
    public function verify()
    {
        if ([] !== $this->_expectations) {
            foreach ($this->_expectations as $expectation) {
                $expectation->verify();
            }

            return;
        }

        foreach ($this->_defaults as $defaultExpectation) {
            $defaultExpectation->verify();
        }
    }

    /**
     * Search current array of expectations for a match
     *
     * @param  array<Expectation> $expectations
     * @return null|Expectation
     */
    protected function _findExpectationIn(array $expectations, array $args)
    {
        foreach ($expectations as $expectation) {
            if (! $expectation->isEligible()) {
                continue;
            }

            if (! $expectation->matchArgs($args)) {
                continue;
            }

            return $expectation;
        }

        foreach ($expectations as $expectation) {
            if ($expectation->matchArgs($args)) {
                return $expectation;
            }
        }

        return null;
    }
}
