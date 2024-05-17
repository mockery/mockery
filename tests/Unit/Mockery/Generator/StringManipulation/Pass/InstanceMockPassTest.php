<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use PHPUnit\Framework\TestCase;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class InstanceMockPassTest extends TestCase
{
    public function testShouldAppendConstructorAndPropertyForInstanceMock(): void
    {
        $builder = new MockConfigurationBuilder();
        $builder->setInstanceMock(true);
        $config = $builder->getMockConfiguration();
        $pass = new InstanceMockPass();
        $code = $pass->apply('class Dave { }', $config);

        self::assertNotFalse(mb_strpos($code, 'public function __construct'));

        self::assertNotFalse(mb_strpos($code, 'protected $_mockery_ignoreVerification'));

        self::assertNotFalse(mb_strpos($code, 'this->_mockery_constructorCalled(func_get_args());'));
    }
}
