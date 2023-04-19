<?php

namespace MockeryTest\Fixture;

class CustomValueObject implements CustomValueObjectInterface
{
    public $value;
    public function __construct($value)
    {
        $this->value = $value;
    }
}
