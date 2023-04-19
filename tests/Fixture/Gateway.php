<?php

namespace MockeryTest\Fixture;

class Gateway
{
    public function __call($method, $args)
    {
        $m = new \SoCool();
        return \call_user_func_array(array($m, $method), $args);
    }
}
