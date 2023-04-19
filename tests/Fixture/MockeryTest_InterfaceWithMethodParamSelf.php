<?php

namespace MockeryTest\Fixture;

interface MockeryTest_InterfaceWithMethodParamSelf
{
    public function foo(self $bar);
}
