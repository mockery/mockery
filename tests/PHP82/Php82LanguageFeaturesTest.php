<?php

namespace test\PHP82;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class Php82LanguageFeaturesTest extends MockeryTestCase
{

    public function testCanMockUndefinedClasses()
    {
        $class = mock('UndefinedClass');

        $class->foo = 'bar';

        $this->assertSame('bar', $class->foo);
    }
}