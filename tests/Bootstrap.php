<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

use Composer\Autoload\ClassLoader;
use Mockery\Loader\RequireLoader;

/*
 * Set error reporting to the level to which Mockery code must comply.
 */
\error_reporting(E_ALL);

if (! \function_exists('vdd')) {
    /**
     * @throws Throwable
     */
    function vdd(): void
    {
        \var_dump(\func_get_args());

        $traces = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $trace = $traces[1] ?? null;

        if (! \is_array($trace)) {
            throw new RuntimeException('Unable to find debug backtrace');
        }

        if (\array_key_exists('file', $trace) && \array_key_exists('line', $trace)) {
            echo \implode(PHP_EOL, [
                '',
                \sprintf('// vdd() called from: %s:%s', $trace['file'], $trace['line']),
                '',
            ]);
        }

        exit(42);
    }
}

(static function (string $rootDir): void {
    $classLoader = require \implode(DIRECTORY_SEPARATOR, [$rootDir, 'vendor', 'autoload.php']);
    if (! $classLoader instanceof ClassLoader) {
        throw new RuntimeException('Unable to load ' . ClassLoader::class);
    }

    $hamcrestPath = \implode(
        DIRECTORY_SEPARATOR,
        [$rootDir, 'vendor', 'hamcrest', 'hamcrest-php', 'hamcrest', 'Hamcrest.php']
    );
    if (\file_exists($hamcrestPath)) {
        require_once $hamcrestPath;
    }

    $testsDir = \implode(DIRECTORY_SEPARATOR, [$rootDir, 'tests']);
    if (! \is_dir($testsDir)) {
        throw new RuntimeException('Unable to find tests directory: ' . $testsDir);
    }

    $fixtureDir = \implode(DIRECTORY_SEPARATOR, [$testsDir, 'Fixture']);
    if (! \is_dir($fixtureDir)) {
        throw new RuntimeException('Unable to find fixture directory: ' . $fixtureDir);
    }

    // Add autoloading for the fixture classes
    $classLoader->add('', $fixtureDir . DIRECTORY_SEPARATOR . 'Namespaced');
    $versions = ['PHP73', 'PHP74', 'PHP80', 'PHP81', 'PHP82', 'PHP83', 'PHP84', 'PHP85', 'PHP86'];
    foreach ($versions as $version) {
        $classLoader->addPsr4($version . '\\', $fixtureDir . DIRECTORY_SEPARATOR . $version);
    }
    // $classLoader->addPsr4('Tests\\Unit\\', $testsDir . DIRECTORY_SEPARATOR . 'Unit');

    $debug = false;
    // If debug is enabled, set up a loader for Mockery to use for generated mock classes
    if ($debug) {
        $mocksDirectory = \implode(DIRECTORY_SEPARATOR, [$testsDir, '_mocks']);

        if (! \file_exists($mocksDirectory)) {
            \mkdir($mocksDirectory, 0777, true);
        }

        Mockery::setLoader(new RequireLoader($mocksDirectory));
    }
})(\dirname(__DIR__));
