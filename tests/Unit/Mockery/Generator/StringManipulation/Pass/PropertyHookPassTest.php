<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\PropertyHook;
use Mockery\Generator\StringManipulation\Pass\PropertyHookPass;
use Override;
use Tests\Unit\AbstractTestCase;
use Throwable;

use function implode;

/**
 * @coversDefaultClass \Mockery
 */
final class PropertyHookPassTest extends AbstractTestCase
{
    public const CODE = 'class Mock implements MockInterface {}';

    public const INVALID_CODE = 'class Mock implements MockInterface {';

    /**
     * @var PropertyHookPass
     */
    protected $pass;

    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->pass = new PropertyHookPass();
    }

    /**
     * @throws Throwable
     */
    public function testShouldDeclarePropertyGetHook(): void
    {
        $mockConfiguration = Mockery::mock(MockConfiguration::class)->makePartial();

        $mockConfiguration->expects('getPropertyHooksToMock')->andReturn([
            new PropertyHook('propertyName', 'public', null, true, false),
        ]);

        self::assertStringContainsString(
            implode("\n", [
                'public $propertyName {',
                '        get {',
                "            return \$this->_mockery_handleMethodCall('\$propertyName::get', []);",
                '        }',
                '    }',
            ]),
            $this->pass->apply(self::CODE, $mockConfiguration),
        );
    }

    /**
     * @throws Throwable
     */
    public function testShouldDeclarePropertyHook(): void
    {
        $mockConfiguration = Mockery::mock(MockConfiguration::class)->makePartial();

        $mockConfiguration->expects('getPropertyHooksToMock')->andReturn([
            new PropertyHook('propertyName', 'public', null, true, true),
        ]);

        self::assertStringContainsString(
            implode("\n", [
                'public $propertyName {',
                '        get {',
                "            return \$this->_mockery_handleMethodCall('\$propertyName::get', []);",
                '        }',
                '        set {',
                "            \$this->_mockery_handleMethodCall('\$propertyName::set', [\$value]);",
                '        }',
                '    }',
            ]),
            $this->pass->apply(self::CODE, $mockConfiguration),
        );
    }

    /**
     * @throws Throwable
     */
    public function testShouldDeclarePropertyHookWithType(): void
    {
        $mockConfiguration = Mockery::mock(MockConfiguration::class)->makePartial();

        $mockConfiguration->expects('getPropertyHooksToMock')->andReturn([
            new PropertyHook('propertyName', 'public', 'string', true, true),
        ]);

        self::assertStringContainsString(
            implode("\n", [
                'public string $propertyName {',
                '        get {',
                "            return \$this->_mockery_handleMethodCall('\$propertyName::get', []);",
                '        }',
                '        set {',
                "            \$this->_mockery_handleMethodCall('\$propertyName::set', [\$value]);",
                '        }',
                '    }',
            ]),
            $this->pass->apply(self::CODE, $mockConfiguration),
        );
    }

    /**
     * @throws Throwable
     */
    public function testShouldDeclarePropertySetHook(): void
    {
        $mockConfiguration = Mockery::mock(MockConfiguration::class)->makePartial();

        $mockConfiguration->expects('getPropertyHooksToMock')->andReturn([
            new PropertyHook('propertyName', 'public', null, false, true),
        ]);

        self::assertStringContainsString(
            implode("\n", [
                'public $propertyName {',
                '        set {',
                "            \$this->_mockery_handleMethodCall('\$propertyName::set', [\$value]);",
                '        }',
                '    }',
            ]),
            $this->pass->apply(self::CODE, $mockConfiguration),
        );
    }

    /**
     * @throws Throwable
     */
    public function testThrowsInvalidArgumentExceptionWhenCodeIsInvalid(): void
    {
        $mockConfiguration = Mockery::mock(MockConfiguration::class)->makePartial();

        $mockConfiguration->expects('getPropertyHooksToMock')->andReturn([
            new PropertyHook('propertyName', 'public', 'string', true, true),
        ]);

        $this->expectException(Mockery\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid class code: no closing brace found');

        $this->pass->apply(self::INVALID_CODE, $mockConfiguration);
    }
}
