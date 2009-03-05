<?php

/*
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Extensions/PhptTestSuite.php';

/*
 * Reporting level for Mockery
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$root = dirname(__FILE__) . '/..';
$library = "$root/library";
$tests = "$root/tests";

/*
 * Ignore test files in code coverage
 */
foreach (array('php') as $suffix) {
    PHPUnit_Util_Filter::addDirectoryToFilter($tests, ".$suffix");
}

/*
 * Add suitable include_path value
 */
$path = array($library,$tests,get_include_path());
set_include_path(implode(PATH_SEPARATOR, $path));

/*
 * Enlist the entire library for code coverage analysis
 */
if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
    PHPUnit_Util_Filter::addDirectoryToWhitelist($library);
}

/*
 * Remove globals
 */
unset($root, $library, $tests, $path);
