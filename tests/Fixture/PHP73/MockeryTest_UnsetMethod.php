<?php

namespace PHP73;

class MockeryTest_UnsetMethod
{
    protected $_properties = [];

    public function __construct()
    {
    }

    public function __unset($property)
    {
        unset($this->_properties[$property]);
    }
}
