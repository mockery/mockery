<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 */

namespace test\Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;


class MockingInternalModuleClassWithOptionalParameterByReference extends MockeryTestCase
{
    protected function setUp()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped('The memcache extension needs to be loaded in order to run this test');
        }
        parent::setUp();
    }

    protected function tearDown()
    {
        static::closeMockery();
        parent::tearDown();
    }

    /**
     * Regression for {@see https://github.com/mockery/mockery/issues/757 issue#757}
     *
     * @test
     */
    public function mockingInternalModuleClassWithOptionalParameterByReferenceMayNotBreakCodeGeneration()
    {
        \Mockery::getConfiguration()
            ->setInternalClassMethodParamMap(\Memcache::class, 'get', ['$id', '&$flags = null']);

        $memcache = \Mockery::mock(\Memcache::class);
        $memcache->shouldReceive('get')
            ->with(
                $id = 'foobar',
                \Mockery::on(
                    function (&$flags) {
                        $this->assertNull($flags);
                        $flags = 255;
                        return true;
                    }
                )
            )
            ->once()
            ->andReturn($expected = time());
        $paramFlags = null;
        $this->assertSame($expected, $memcache->get($id, $paramFlags));
        $this->assertSame(255, $paramFlags);
    }

}