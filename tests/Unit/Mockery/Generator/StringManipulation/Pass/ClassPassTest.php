<?php

declare(strict_types=1);

namespace Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\ClassPass;
use function mb_strpos;

final class ClassPassTest extends MockeryTestCase
{
    public const CODE = 'class Mock implements MockInterface {}';

    protected $pass;

    public function mockeryTestSetUp()
    {
        $this->pass = new ClassPass();
    }

    public function testShouldDeclareUnknownClass(): void
    {
        $config = new MockConfiguration(['Testing\TestClass'], [], [], 'Dave\Dave');

        $code = $this->pass->apply(self::CODE, $config);

        self::assertNotFalse(mb_strpos($code, 'class Mock extends \Testing\TestClass implements MockInterface'));
    }
}
