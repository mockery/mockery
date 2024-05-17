<?php

declare(strict_types=1);

namespace PHP73;

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
        return false;
    }
}
