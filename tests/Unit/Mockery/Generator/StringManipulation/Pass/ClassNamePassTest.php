<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class ClassNamePassTest extends MockeryTestCase
{
    public const CODE = 'namespace Mockery; class Mock {}';

    protected $pass;

    protected function mockeryTestSetUp(): void
    {
        $this->pass = new ClassNamePass();
    }

    public function testShouldRemoveLeadingBackslashesFromNamespace(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave\Dave');
        $code = $this->pass->apply(static::CODE, $config);
        self::assertNotFalse(mb_strpos($code, 'namespace Dave;'));
    }

    public function testShouldRemoveNamespaceDefinition(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave\Dave');
        $code = $this->pass->apply(self::CODE, $config);

        self::assertFalse(mb_strpos($code, 'namespace Mockery;'));
    }

    public function testShouldReplaceClassNameWithSpecifiedName(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave');
        $code = $this->pass->apply(static::CODE, $config);
        self::assertNotFalse(mb_strpos($code, 'class Dave'));
    }

    public function testShouldReplaceNamespaceIfClassNameIsNamespaced(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave\Dave');
        $code = $this->pass->apply(self::CODE, $config);
        self::assertFalse(mb_strpos($code, 'namespace Mockery;'));
        self::assertNotFalse(mb_strpos($code, 'namespace Dave;'));
    }
}
