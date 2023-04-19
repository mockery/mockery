<?php

namespace MockeryTest\Fixture;

class MockeryTest_InterMethod1
{
    public function doFirst()
    {
        return $this->doSecond();
    }
    private function doSecond()
    {
        return $this->doThird();
    }
    public function doThird()
    {
        return \false;
    }
}
