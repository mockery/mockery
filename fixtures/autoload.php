<?php

declare(strict_types=1);

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

$allVersions = implode('|', array_keys($phpVersions));

$currentSupportedVersion = implode('|', array_keys(array_filter($phpVersions, static function (int $version): bool {
    return PHP_VERSION_ID >= $version;
})));

$allTestsAndFixtures = globRecursively(dirname(__DIR__) . '/fixtures/*.php');

$currentSupportedVersionTestsAndFixtures = array_filter(
    $allTestsAndFixtures,
    static function (string $filePath) use ($currentSupportedVersion): bool {
        return preg_match('#' . $currentSupportedVersion . '#', $filePath) === 1;
    }
);

$allPhpVersionTestsAndFixtures = array_filter(
    $allTestsAndFixtures,
    static function (string $filePath) use ($allVersions): bool {
        return preg_match('#' . $allVersions . '#', $filePath) === 1;
    }
);

$excludedPhpVersions = array_diff($allPhpVersionTestsAndFixtures, $currentSupportedVersionTestsAndFixtures);

$autoloadTestsAndFixtures = array_diff($allTestsAndFixtures, $excludedPhpVersions);

array_map(
    static function (string $file): void {
        if (!file_exists($file)) {
            return;
        }

        require_once $file;
    },
    $autoloadTestsAndFixtures
);
