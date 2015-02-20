<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('examples')
    ->exclude('docs')
    ->exclude('travis')
    ->exclude('vendor')
    ->exclude('tests/Mockery/_files')
    ->exclude('tests/Mockery/_files')
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->level('psr2')
    ->finder($finder);