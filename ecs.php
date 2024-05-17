<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\GetClassToClassKeywordFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

$cacheDirectory = __DIR__ . '/.cache/ecs';
$cacheNamespace = \str_replace(search: \DIRECTORY_SEPARATOR, replace: '_', subject: $cacheDirectory);

return ECSConfig::configure()
    ->withCache(directory: $cacheDirectory, namespace: $cacheNamespace)
    ->withPhpCsFixerSets(
        doctrineAnnotation: true,
        per: true,
        perCS: true,
        perCS10: true,
        perCS10Risky: true,
        perCS20: true,
        perCS20Risky: true,
        perCSRisky: true,
        perRisky: true,
        php54Migration: true,
        php56MigrationRisky: true,
        php70Migration: true,
        php70MigrationRisky: true,
        php71Migration: true,
        php71MigrationRisky: true,
        php73Migration: true,
        php74Migration: false,
        php74MigrationRisky: false,
        php80Migration: false,
        php80MigrationRisky: false,
        php81Migration: false,
        php82Migration: false,
        php83Migration: false,
        phpunit30MigrationRisky: true,
        phpunit32MigrationRisky: true,
        phpunit35MigrationRisky: true,
        phpunit43MigrationRisky: true,
        phpunit48MigrationRisky: true,
        phpunit50MigrationRisky: true,
        phpunit52MigrationRisky: true,
        phpunit54MigrationRisky: true,
        phpunit55MigrationRisky: true,
        phpunit56MigrationRisky: true,
        phpunit57MigrationRisky: true,
        phpunit60MigrationRisky: true,
        phpunit75MigrationRisky: true,
        phpunit84MigrationRisky: true,
        phpunit100MigrationRisky: true,
        psr1: true,
        psr2: true,
        psr12: true,
        psr12Risky: true,
        phpCsFixer: false,
        phpCsFixerRisky: false,
        symfony: false,
        symfonyRisky: false,
    )
    ->withPreparedSets(
        psr12: true,
        common: false,
        symplify: true,
        arrays: true,
        comments: true,
        docblocks: true,
        spaces: true,
        namespaces: true,
        controlStructures: true,
        phpunit: true,
        strict: true,
        cleanCode: true,
    )
    ->withConfiguredRule(NativeFunctionInvocationFixer::class, [
        'include' => ['@all'],
        'scope' => 'all',
    ])
    ->withConfiguredRule(
        checkerClass: GlobalNamespaceImportFixer::class,
        configuration: [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ]
    )
    ->withConfiguredRule(checkerClass: OrderedImportsFixer::class, configuration: [
        'imports_order' => ['class', 'const', 'function'],
    ])

    ->withConfiguredRule(checkerClass: PhpdocAlignFixer::class, configuration: [
        'tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var'],
    ])

    ->withConfiguredRule(checkerClass: PhpUnitTestCaseStaticMethodCallsFixer::class, configuration: [
        'call_type' => 'self',
    ])

    ->withConfiguredRule(checkerClass: ArraySyntaxFixer::class, configuration: [
        'syntax' => 'short',
    ])

    ->withConfiguredRule(checkerClass: ConstantCaseFixer::class, configuration: [
        'case' => 'lower',
    ])

    ->withConfiguredRule(checkerClass: OrderedClassElementsFixer::class, configuration: [
        'case_sensitive' => true,
        'sort_algorithm' => 'alpha',
        'order' => [
            'use_trait',
            'case',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'method:mockeryTestSetUp',
            'method:mockeryTestTearDown',
            'phpunit',
            'method_public',
            'method_protected',
            'method_private',
        ],
    ])
    ->withParallel()
    ->withPaths(paths: [__DIR__ . '/tests'])
    ->withSkip(skip: [
        __DIR__ . '/library/Mockery/Mock.php',
        __DIR__ . '/tests/Fixture/*',
        GetClassToClassKeywordFixer::class,
    ])
    ->withRootFiles()
    ->withRules([PhpUnitTestClassRequiresCoversFixer::class])
;
