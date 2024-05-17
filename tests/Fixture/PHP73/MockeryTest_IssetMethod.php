<?php

namespace PHP73;

class MockeryTest_IssetMethod
{
    protected $_properties = [];

    public function __construct()
    {
    }

    public function __isset($property)
    {
        return isset($this->_properties[$property]);
    }
}
