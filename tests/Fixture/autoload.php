<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

$rootDir = dirname(__DIR__, 2);

$loader = require \implode(DIRECTORY_SEPARATOR, [$rootDir, 'vendor', 'autoload.php']);

if (! $loader instanceof ClassLoader) {
    throw new \RuntimeException('Unable to load ' . ClassLoader::class);
}

$testsDir = \implode(DIRECTORY_SEPARATOR, [$rootDir, 'tests']);
if (! \is_dir($testsDir)) {
    throw new \RuntimeException('Unable to find tests directory: ' . $testsDir);
}

$loader->addPsr4('Tests\\Unit\\', $testsDir . DIRECTORY_SEPARATOR . 'Unit');

$fixtureDir = \implode(DIRECTORY_SEPARATOR, [$testsDir, 'Fixture']);
if (! \is_dir($fixtureDir)) {
    throw new \RuntimeException('Unable to find fixture directory: ' . $fixtureDir);
}

$loader->add('', $fixtureDir . DIRECTORY_SEPARATOR . 'Namespaced');

$versions = ['PHP73', 'PHP74', 'PHP80', 'PHP81', 'PHP82', 'PHP83', 'PHP84', 'PHP85', 'PHP86'];

foreach ($versions as $version) {
    $loader->addPsr4($version . '\\', $fixtureDir . DIRECTORY_SEPARATOR . $version);
}
