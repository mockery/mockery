<?php

namespace MockeryTest\Fixture;

class MockeryTest_ReturnByRef
{
    public $i = 0;
    public function &get()
    {
        return $this->{$i};
    }
}
