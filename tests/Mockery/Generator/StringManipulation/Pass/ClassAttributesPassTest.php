<?php

namespace test\Mockery\Generator\StringManipulation\Pass;

use Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\ClassAttributesPass;
use Mockery\Generator\UndefinedTargetClass;

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
        $undefinedTargetClass = $this->createPartialMock(UndefinedTargetClass::class, ['getAttributes']);
        $undefinedTargetClass->expects($this->once())
            ->method('getAttributes')
            ->willReturn($attributes);

        $config = $this->createPartialMock(MockConfiguration::class, ['getTargetClass']);
        $config->expects($this->once())
            ->method('getTargetClass')
            ->willReturn($undefinedTargetClass);

        $pass = new ClassAttributesPass();

        $code = $pass->apply(static::CODE, $config);

        $this->assertTrue(\mb_strpos($code, $expected) !== false);
    }

    /** @see testCanApplyClassAttributes */
    public function providerCanApplyClassAttributes(): Generator
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
