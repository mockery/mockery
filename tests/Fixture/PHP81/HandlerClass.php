<?php

declare(strict_types=1);

namespace Fixture\PHP81;

class HandlerClass
{
    public function doStuff(MockClass $mockClass): string
    {
        return $mockClass->test('test');
    }
}
