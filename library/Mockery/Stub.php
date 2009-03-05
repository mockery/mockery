<?php

class Mockery_Stub
{

    protected $_mockery_data = array();

    public function __call($method, array $args)
    {
        if (isset($this->_mockery_data[$method])) {
            return $this->_mockery_data[$method];
        }
        throw new Mockery_Exception('Method called, ' . $method . ', has not been stubbed');
    }

    public function mockery_set(array $data)
    {
        $this->_mockery_data = $data;
    }

}
