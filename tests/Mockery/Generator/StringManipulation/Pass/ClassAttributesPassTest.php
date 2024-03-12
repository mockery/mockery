<?php

namespace test\Mockery\Generator\StringManipulation\Pass;

use Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\ClassAttributesPass;
use Mockery\Generator\UndefinedTargetClass;

use function mb_strpos;

class ClassAttributesPassTest extends MockeryTestCase
{
    const CODE = "namespace Mockery; class Mock {}";

    /**
     * @dataProvider providerCanApplyClassAttributes
     *
     * @param array $attributes
     * @param string $expected
     * @return void
     */
    public function testCanApplyClassAttributes(
        array $attributes,
        string $expected
    ): void {
        $undefinedTargetClass = mock(UndefinedTargetClass::class);
        $undefinedTargetClass->expects('getAttributes')
            ->once()
            ->andReturn($attributes);

        $config = mock(MockConfiguration::class);
        $config->expects('getTargetClass')
            ->once()
            ->andReturn($undefinedTargetClass);

        $pass = new ClassAttributesPass();

        $code = $pass->apply(file_get_contents(__FILE__), $config);

        self::assertStringContainsString($expected, $code);
    }

    /** @see testCanApplyClassAttributes */
    public static function providerCanApplyClassAttributes(): Generator
    {
        yield 'has no attributes' => [
            'attributes' => [],
            'expected'   => '',
        ];

        yield 'has one attribute' => [
            'attributes' => [
                                'Attribute1'
                            ],
            'expected'   => '#[Attribute1]',
        ];

        yield 'has attributes' => [
            'attributes' => [
                                'Attribute1',
                                'Attribute2',
                                'Attribute3()'
                            ],
            'expected'   => '#[Attribute1,Attribute2,Attribute3()]',
        ];
    }
}
