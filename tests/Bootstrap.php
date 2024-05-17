<?php

declare(strict_types=1);

/*
 * Set error reporting to the level to which Mockery code must comply.
 */
\error_reporting(E_ALL);

function isAbsolutePath($path)
{
    $windowsPattern = '~^[A-Z]:[\\/]~i';
    return ($path[0] === DIRECTORY_SEPARATOR) || (\preg_match($windowsPattern, $path) === 1);
}

$root = \realpath(\dirname(__FILE__, 2));
$composerVendorDirectory = \getenv('COMPOSER_VENDOR_DIR') ?: 'vendor';

if (! \isAbsolutePath($composerVendorDirectory)) {
    $composerVendorDirectory = $root . DIRECTORY_SEPARATOR . $composerVendorDirectory;
}

/**
 * Check that composer installation was done
 */
$autoloadPath = $composerVendorDirectory . DIRECTORY_SEPARATOR . 'autoload.php';
if (! \file_exists($autoloadPath)) {
    throw new Exception(
        'Please run "php composer.phar install" in root directory '
        . 'to setup unit test dependencies before running the tests'
    );
}

require_once $autoloadPath;

$hamcrestRelativePath = 'hamcrest/hamcrest-php/hamcrest/Hamcrest.php';
if (DIRECTORY_SEPARATOR !== '/') {
    $hamcrestRelativePath = \str_replace('/', DIRECTORY_SEPARATOR, $hamcrestRelativePath);
}
$hamcrestPath = $composerVendorDirectory . DIRECTORY_SEPARATOR . $hamcrestRelativePath;

require_once $hamcrestPath;

Mockery::globalHelpers();

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $autoloadPath, $hamcrestPath, $composerVendorDirectory);

$dev = false;

if ($dev) {
    $mocksDirectory = __DIR__ . '/_mocks/';
    if (! \file_exists($mocksDirectory)) {
        \mkdir($mocksDirectory, 0777, true);
    }

    Mockery::setLoader(new Mockery\Loader\RequireLoader($mocksDirectory));

    function vdd(): void
    {
        \var_dump(\func_get_args());

        $trace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        if (\array_key_exists('file', $trace[1]) && \array_key_exists('line', $trace[1])) {
            echo \sprintf(
                PHP_EOL . '// dd() called from: %s:%s' . PHP_EOL,
                $trace[1]['file'],
                $trace[1]['line']
            ), PHP_EOL;
        }

        exit(42);
    }
}
