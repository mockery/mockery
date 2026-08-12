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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\ClassPass;
use Override;
use Throwable;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class ClassPassTest extends MockeryTestCase
{
    public const CODE = 'class Mock implements MockInterface {}';

    protected $pass;

    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->pass = new ClassPass();
    }

    /**
     * @throws Throwable
     */
    public function testShouldDeclareUnknownClass(): void
    {
        $config = new MockConfiguration(['Testing\TestClass'], [], [], 'Dave\Dave');

        $code = $this->pass->apply(self::CODE, $config);

        self::assertNotFalse(mb_strpos($code, 'class Mock extends \Testing\TestClass implements MockInterface'));
    }
}
