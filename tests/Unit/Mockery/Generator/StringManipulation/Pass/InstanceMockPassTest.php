<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use PHPUnit\Framework\TestCase;
use Throwable;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class InstanceMockPassTest extends TestCase
{
    /**
     * @throws Throwable
     */
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
