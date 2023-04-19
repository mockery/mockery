<?php

namespace MockeryTest\Fixture;

class Mockery_UseDemeter
{
    protected $demeter;
    public function __construct($demeter)
    {
        $this->demeter = $demeter;
    }
    public function doit()
    {
        return $this->demeter->foo()->bar()->baz();
    }
    public function doitWithArgs()
    {
        return $this->demeter->foo("foo")->bar("bar")->baz("baz");
    }
}
