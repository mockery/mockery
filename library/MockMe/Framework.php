<?php

class MockMe_Framework
{
    public static function autoload($class)
    {
        if (substr($class, 0, 7) !== 'MockMe_' && $class !== 'MockMe') {
            return false;
        }
        $path = dirname(dirname(__FILE__));
        include $path . '/' . str_replace('_', '/', $class) . '.php';
    }

}

spl_autoload_register(array('MockMe_Framework', 'autoload'));

function mockme($className, $custom = null)
{
    return MockMe::mock($className, $custom);
}

function mockme_verify()
{
    return MockMe::verify();
}
