<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;
use Boundwize\StructArmed\Preset\Presets\CodeQualityPreset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    // still require php 7.3
    ->skipRule(CodeQualityPreset::LARGE_NUMERIC_LITERALS_MUST_USE_SEPARATOR)
    ->layer('Exception', [
        'library/Mockery/Exception.php',
        'library/Mockery/Exception',
        'library/Mockery/CountValidator/Exception.php',
    ])
    ->layer('Matcher', 'library/Mockery/Matcher')
    ->layer('CountValidator', 'library/Mockery/CountValidator', 'library/Mockery/CountValidator/Exception.php')
    ->layer('Generator', 'library/Mockery/Generator')
    ->layer('Loader', 'library/Mockery/Loader')
    ->layer('Phpunit', 'library/Mockery/Adapter/Phpunit')
    ->layer('Core', 'library', [
        'library/Mockery/Exception.php',
        'library/Mockery/Exception',
        'library/Mockery/Matcher',
        'library/Mockery/CountValidator',
        'library/Mockery/Generator',
        'library/Mockery/Loader',
        'library/Mockery/Adapter',
    ])
    ->ruleset([
        'Exception'      => ['Core'],
        'Matcher'        => [],
        'CountValidator' => ['+Exception'],
        'Generator'      => ['+Exception'],
        'Loader'         => ['Generator'],
        'Core'           => ['+CountValidator', '+Generator', 'Matcher', 'Loader'],
        'Phpunit'        => ['Core'],
    ]);
