<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $filePath = sprintf(
        '%s%s%s.php',
        __DIR__,
        DIRECTORY_SEPARATOR,
        str_replace('\\', DIRECTORY_SEPARATOR, $class)
    );

    if (!file_exists($filePath)) {
        return;
    }

    require_once $filePath;
});
