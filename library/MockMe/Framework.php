<?php

class MockMe_Framework
{
    public static function autoload($class)
    {
        if (substr($class, 0, 6) != 'MockMe') {
            return false;
        }
        $path = dirname(dirname(__FILE__));
        include $path . '/' . str_replace('_', '/', $class) . '.php';
    }

}

spl_autoload_register(array('MockMe_Framework', 'autoload'));

function mockme($className, $customName = null) 
{
    return MockMe::mock($className, $customName);
}
