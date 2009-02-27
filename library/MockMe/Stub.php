<?php

class MockMe_Stub
{

    protected $_mockme_data = array();

    public function __call($method, array $args)
    {
        if (isset($this->_mockme_data[$method])) {
            return $this->_mockme_data[$method];
        }
        throw new MockMe_Exception('Method called, ' . $method . ', has not been stubbed');
    }

    public function mockme_set(array $data)
    {
        $this->_mockme_data = $data;
    }

}
