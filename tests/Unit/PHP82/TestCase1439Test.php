<?php

declare(strict_types=1);

namespace Tests\Unit\PHP82;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use SoapClient;
use Throwable;

/**
 * @coversDefaultClass \Mockery\Expectation
 * @requires PHP 8.2
 * @see https://github.com/mockery/mockery/issues/1439
 */
final class TestCase1439Test extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testDescription(): void
    {
        // Mock the SOAP client
        $soapClientMock = Mockery::mock(SoapClient::class);

        // Method we will be calling
        $methodName = 'testMethod';

        // Expect result
        $expectedResult = 'result';

        // Expected parameters
        $params = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        // Expect the method to be called with specific arguments using withArgs
        $soapClientMock
            ->expects($methodName)
            ->withArgs($params)
            ->andReturn($expectedResult);

        // Simulate the call
        $response = \call_user_func_array([$soapClientMock, $methodName], $params);

        self::assertSame($expectedResult, $response);
    }
}
