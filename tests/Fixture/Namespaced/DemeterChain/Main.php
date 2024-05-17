<?php

namespace DemeterChain;

class Main
{
    public function callDemeter(A $a)
    {
        return $a->foo()->bar()->baz();
    }
}
