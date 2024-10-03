<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ClassNotation\FinalClassFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\GetClassToClassKeywordFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitAssertNewNamesFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitAttributesFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderNameFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderReturnTypeFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderStaticFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertInternalTypeFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitExpectationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMockFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNamespacedFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

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
                        'import_functions' => false,
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
                ->withRootFiles()
                ->withRules([
                    PhpUnitAssertNewNamesFixer::class,
                    PhpUnitConstructFixer::class,
                    PhpUnitDataProviderNameFixer::class,
                    PhpUnitDataProviderReturnTypeFixer::class,
                    PhpUnitDataProviderStaticFixer::class,
                    PhpUnitDedicateAssertFixer::class,
                    PhpUnitDedicateAssertInternalTypeFixer::class,
                    PhpUnitExpectationFixer::class,
                    PhpUnitFqcnAnnotationFixer::class,
                    //        PhpUnitInternalClassFixer::class,
                    PhpUnitMethodCasingFixer::class,
                    PhpUnitMockFixer::class,
                    PhpUnitMockShortWillReturnFixer::class,
                    PhpUnitNamespacedFixer::class,
                    PhpUnitNoExpectationAnnotationFixer::class,
                    PhpUnitSetUpTearDownVisibilityFixer::class,
                    PhpUnitStrictFixer::class,
                    FinalClassFixer::class,
                    PhpUnitTestAnnotationFixer::class,

                    //        PhpUnitTestClassRequiresCoversFixer::class,
                    ParamReturnAndVarTagMalformsFixer::class,
                    // arrays
                    ArrayListItemNewlineFixer::class,
                    ArrayOpenerAndCloserNewlineFixer::class,
                    StandaloneLinePromotedPropertyFixer::class,
                    // newlines
                    SpaceAfterCommaHereNowDocFixer::class,
                    BlankLineAfterStrictTypesFixer::class,
                    LineLengthFixer::class,
                ])
                ->withConfiguredRule(
                    GeneralPhpdocAnnotationRemoveFixer::class,
                    [
                        'annotations' => ['small', 'internal', 'coversDefaultClass1', 'coversNothing'],
                    ]
                )
                ->withSkip(skip: [
                    __DIR__ . '/library/Mockery/Mock.php',
                    __DIR__ . '/tests/Fixture/*',
                    PhpUnitTestClassRequiresCoversFixer::class,
                    PhpUnitInternalClassFixer::class,
                    MethodChainingNewlineFixer::class,
                    GetClassToClassKeywordFixer::class,
                    //        \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer::class,
                    StrictComparisonFixer::class => [
                        __DIR__ . '/library/Mockery/Matcher/IsEqual.php',
                        __DIR__ . '/library/Mockery/Matcher/MustBe.php',
                        __DIR__ . '/library/Mockery/Matcher/Subset.php',
                        __DIR__ . '/library/Mockery/Generator/StringManipulation',
                    ],
                    // Enable later
                    FinalClassFixer::class,
                    PhpUnitAttributesFixer::class,
                ])
                ->withSpacing(indentation: Option::INDENTATION_SPACES, lineEnding: PHP_EOL)
                ->withPaths(paths: [
                    __DIR__ . '/tests',
                    // __DIR__ . '/library',
                ])
    ;
