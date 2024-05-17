<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $directory = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $filePath = sprintf(
        '%s%s%s.php',
        __DIR__,
        DIRECTORY_SEPARATOR,
        $directory
    );

    if (! file_exists($filePath)) {
        $filePath = sprintf(
            '%s%sNamespaced%s%s.php',
            __DIR__,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $directory
        );

        if (! file_exists($filePath)) {
            return;
        }
    }

    require_once $filePath;
});

$rootDir = dirname(__DIR__, 2);
$loader = require $rootDir . '/vendor/autoload.php';
$loader->addPsr4('Tests\\', $rootDir . '/tests');
//$loader->addPsr4('Mockery\\Tests\\', $rootDir . '/tests');
//$loader->addPsr4('test\\', $rootDir . '/tests');
