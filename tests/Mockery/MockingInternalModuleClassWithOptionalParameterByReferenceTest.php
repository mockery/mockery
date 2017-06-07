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

class MockingInternalModuleClassWithOptionalParameterByReferenceTest extends MockeryTestCase
{
    protected function setUp()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped('ext/memcache not installed');
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
        // this works for macOS
        \Mockery::getConfiguration()
            ->setInternalClassMethodParamMap('Memcache', 'get', array('$id', '&$flags = null'));
        // strange thing is, the reflected class under linux is MemcachePool not Memcache
        \Mockery::getConfiguration()
            ->setInternalClassMethodParamMap('MemcachePool', 'get', array('$id', '&$flags = null'));
        $memcache = \Mockery::mock('Memcache');
        $memcache->shouldReceive('get')
            ->with(
                $id = 'foobar',
                \Mockery::on(
                    function (&$flags) {
                        $valid = null === $flags;
                        $flags = 255;
                        return $valid;
                    }
                )
            )
            ->once()
            ->andReturn($expected = time());
        $paramFlags = null;
        $this->assertSame($expected, $memcache->get($id, $paramFlags));
        \Mockery::close();
        $this->assertSame(255, $paramFlags);
    }
}
