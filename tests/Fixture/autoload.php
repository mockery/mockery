<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

$rootDir = dirname(__DIR__, 2);

$loader = require $rootDir . '/vendor/autoload.php';

if (! $loader instanceof ClassLoader){
    throw new \RuntimeException('Unable to load ' . ClassLoader::class);
}

$loader->add('', $rootDir . '/tests/Fixture/Namespaced');
$loader->addPsr4('PHP73\\', $rootDir . '/tests/Fixture/PHP73');
$loader->addPsr4('PHP74\\', $rootDir . '/tests/Fixture/PHP74');
$loader->addPsr4('PHP80\\', $rootDir . '/tests/Fixture/PHP80');
$loader->addPsr4('PHP81\\', $rootDir . '/tests/Fixture/PHP81');
$loader->addPsr4('PHP82\\', $rootDir . '/tests/Fixture/PHP82');
$loader->addPsr4('PHP83\\', $rootDir . '/tests/Fixture/PHP83');
$loader->addPsr4('PHP84\\', $rootDir . '/tests/Fixture/PHP84');
$loader->addPsr4('Tests\\', $rootDir . '/tests');
