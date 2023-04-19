<?php

namespace MockeryTest\Fixture;

class MockeryTest_UnsetMethod
{
    protected $_properties = array();
    public function __construct()
    {
    }
    public function __unset($property)
    {
        unset($this->_properties[$property]);
    }
}
