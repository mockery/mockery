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
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use Override;
use Throwable;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class ClassNamePassTest extends MockeryTestCase
{
    public const CODE = 'namespace Mockery; class Mock {}';

    protected $pass;

    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->pass = new ClassNamePass();
    }

    /**
     * @throws Throwable
     */
    public function testShouldRemoveLeadingBackslashesFromNamespace(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave\Dave');
        $code = $this->pass->apply(self::CODE, $config);
        self::assertNotFalse(mb_strpos($code, 'namespace Dave;'));
    }

    /**
     * @throws Throwable
     */
    public function testShouldRemoveNamespaceDefinition(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave\Dave');
        $code = $this->pass->apply(self::CODE, $config);

        self::assertFalse(mb_strpos($code, 'namespace Mockery;'));
    }

    /**
     * @throws Throwable
     */
    public function testShouldReplaceClassNameWithSpecifiedName(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave');
        $code = $this->pass->apply(self::CODE, $config);
        self::assertNotFalse(mb_strpos($code, 'class Dave'));
    }

    /**
     * @throws Throwable
     */
    public function testShouldReplaceNamespaceIfClassNameIsNamespaced(): void
    {
        $config = new MockConfiguration([], [], [], 'Dave\Dave');
        $code = $this->pass->apply(self::CODE, $config);
        self::assertFalse(mb_strpos($code, 'namespace Mockery;'));
        self::assertNotFalse(mb_strpos($code, 'namespace Dave;'));
    }
}
