<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

if (!function_exists('recursiveGlob')) {
    function globRecursively($pattern)
    {
        $files = glob($pattern) ?: [];
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) ?: [] as $dir) {
            $files = array_merge($files, globRecursively($dir . '/' . basename($pattern)));
        }
        return $files;
    }
}

/** @var ClassLoader $autoloader */
$autoloader = array_reduce(
    spl_autoload_functions(),
    static function (?ClassLoader $found, $autoload): ?ClassLoader {
        if ($found instanceof ClassLoader) {
            return $found;
        }

        if (is_array($autoload) && $autoload[0] instanceof ClassLoader) {
            return $autoload[0];
        }

        if ($autoload instanceof ClassLoader) {
            return $autoload;
        }

        return null;
    },
    null
);

// Autoload test fixtures for supported PHP versions (current and previous)
$phpVersions = [
    'PHP83' => 80300,
    'PHP82' => 80200,
    'PHP81' => 80100,
    'PHP80' => 80000,
    'PHP74' => 70400,
    'PHP73' => 70300,
    'PHP72' => 70200,
];

foreach ($phpVersions as $version => $versionId) {
    if (PHP_VERSION_ID < $versionId) {
        continue;
    }

    // Add a directory to the autoloader
    $autoloader->addPsr4($version . '\\', [__DIR__ . '/' . $version]);
}

// Autoload Shared files
array_map(
    static function (string $file): void {
        if (!file_exists($file)) {
            return;
        }
        require_once $file;
    },
    globRecursively(dirname(__DIR__) . '/fixtures/Shared/*.php')
);
