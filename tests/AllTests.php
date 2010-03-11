<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once 'MockeryTest.php';
require_once 'MockeryExpectationsTest.php';
require_once 'RegressionTest.php';
require_once 'StubExpectationsTest.php';

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mockery: Cross Framework Mock Objects and Stubs for PHP5');

        $suite->addTestSuite('MockeryTest');
        $suite->addTestSuite('MockeryExpectationsTest');
        //$suite->addTestSuite('StubExpectationsTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
