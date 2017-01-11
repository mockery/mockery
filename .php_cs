<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;

$finder = DefaultFinder::create()->in(
    [
        'library',
        'tests',
    ]);

return Config::create()
    ->level('psr2')
    ->setUsingCache(true)
    ->finder($finder);
