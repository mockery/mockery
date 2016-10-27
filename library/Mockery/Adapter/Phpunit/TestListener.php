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

namespace Mockery\Adapter\Phpunit;

class TestListener extends \PHPUnit_Framework_BaseTestListener
{
    /**
     * endTest is called after each test and checks if \Mockery::close() has
     * been called, and will let the test fail if it hasn't.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        try {
            // The self() call is used as a sentinel. Anything that throws if
            // the container is closed already will do.
            \Mockery::self();
        } catch (\LogicException $_) {
            return;
        }

        $e = new \PHPUnit_Framework_ExpectationFailedException(sprintf(
            "Mockery's expectations have not been verified. Make sure that \Mockery::close() is called at the end of the test. Consider using %s\MockeryPHPUnitIntegration or extending %s\MockeryTestCase.",
            __NAMESPACE__,
            __NAMESPACE__
        ));
        $result = $test->getTestResultObject();
        $result->addFailure($test, $e, $time);
    }
}
