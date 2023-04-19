<?php

namespace MockeryTest\Fixture;

class MockeryTest_ClassConstructor2
{
    protected $param1;
    public function __construct(\stdClass $param1)
    {
        $this->param1 = $param1;
    }
    public function getParam1()
    {
        return $this->param1;
    }
    public function foo()
    {
        return 'foo';
    }
    public function bar()
    {
        return $this->foo();
    }
}
