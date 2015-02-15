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
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Adapter\Phpunit;

class TestListener implements \PHPUnit_Framework_TestListener
{

    /**
     * After each test, perform Mockery verification tasks and cleanup the
     * statically stored Mockery container for the next test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        try {
            $container = \Mockery::getContainer();
            // check addToAssertionCount is important to avoid mask test errors
            if ($container != null && method_exists($test, 'addToAssertionCount')) {
                $expectation_count = $container->mockery_getExpectationCount();
                $test->addToAssertionCount($expectation_count);
            }
            \Mockery::close();
        } catch (\Exception $e) {
            $result = $test->getTestResultObject();
            $result->addError($test, $e, $time);
        }
    }

    /**
     * Add Mockery files to PHPUnit's blacklist so they don't showup on coverage reports
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if (class_exists('\\PHP_CodeCoverage_Filter')
        && method_exists('\\PHP_CodeCoverage_Filter', 'getInstance')) {
            \PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(
                 __DIR__.'/../../../Mockery/', '.php', '', 'PHPUNIT'
            );

            \PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__DIR__.'/../../../Mockery.php', 'PHPUNIT');
        }
    }
    /**
     *  The Listening methods below are not required for Mockery
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }
}
