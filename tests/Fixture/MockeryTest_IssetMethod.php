<?php

namespace MockeryTest\Fixture;

class MockeryTest_IssetMethod
{
    protected $_properties = array();
    public function __construct()
    {
    }
    public function __isset($property)
    {
        return isset($this->_properties[$property]);
    }
}
