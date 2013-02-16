<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/master/LICENSE New BSD License
 */

/*
 * Set error reporting to the level to which Mockery code must comply.
 */
error_reporting(E_ALL);

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$root    = realpath(dirname(dirname(__FILE__)));
$library = "$root/library";
$tests   = "$root/tests";

/**
 * Check that --dev composer installation was done
 */
if (!file_exists($root . '/vendor/autoload.php')) {
    throw new Exception(
        'Please run "php composer.phar install --dev" in root directory '
        . 'to setup unit test dependencies before running the tests'
    );
}

/*
 * Prepend the Mutateme library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the code and tests that would supercede
 * this copy.
 */
$path = array(
    $library,
    $tests,
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {

    /*
     * Add Mutateme library/ directory to the PHPUnit code coverage
     * whitelist. This has the effect that only production code source files
     * appear in the code coverage report and that all production code source
     * files, even those that are not covered by a test yet, are processed.
     */
    PHPUnit_Util_Filter::addDirectoryToWhitelist($library);

    /*
     * Omit from code coverage reports the contents of the tests directory
     */
    foreach (array('.php', '.phtml', '.csv', '.inc') as $suffix) {
        PHPUnit_Util_Filter::addDirectoryToFilter($tests, $suffix);
    }
    PHPUnit_Util_Filter::addDirectoryToFilter(PEAR_INSTALL_DIR);
    PHPUnit_Util_Filter::addDirectoryToFilter(PHP_LIBDIR);
}

require __DIR__.'/../vendor/autoload.php';

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $library, $tests, $path);

