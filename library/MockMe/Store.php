<?php

class MockMe_Store
{

    protected static $_instances = array();

    protected $_data = array();

    public function __construct(array $data)
    {
        $this->_data = $data;
    }

    public function getInstance($name)
    {
        if (!isset(self::$_instances[$name])) {
            self::$_instances[$name] = new self(array(
                'expectations' => array(),
                'verified' => false,
                'orderedNumber' => null,
                'orderedNumberNext' => null
            ));
        }
        return self::$_instances[$name];
    }

    public function __set($name, $value)
    {
        if (isset($this->_data[$name])) {
            $this->_data[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
    }

}
