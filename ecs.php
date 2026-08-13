<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer;
use PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ClassNotation\FinalClassFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\LambdaNotUsedImportFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\GroupImportFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnneededImportAliasFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\GetClassToClassKeywordFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\CleanNamespaceFixer;
use PhpCsFixer\Fixer\Operator\{ConcatSpaceFixer, NewWithParenthesesFixer};
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAliasTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitAttributesFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBetweenImportGroupsFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use Symplify\CodingStandard\Fixer\Annotation\RemoveMethodNameDuplicateDescriptionFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\Commenting\AddMissingParamNameFixer;
use Symplify\CodingStandard\Fixer\Commenting\AddMissingVarNameFixer;
use Symplify\CodingStandard\Fixer\Commenting\DoubleAsteriskInlineVarFixer;
use Symplify\CodingStandard\Fixer\Commenting\FixParamNameTypoFixer;
use Symplify\CodingStandard\Fixer\Commenting\SingleLineInlineVarDocBlockFixer;
use Symplify\CodingStandard\Fixer\Commenting\SwitchedTypeAndNameFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

$cacheDirectory = \implode(DIRECTORY_SEPARATOR, [__DIR__, '.cache', 'ecs']);

return ECSConfig::configure()
    ->withCache($cacheDirectory, \md5($cacheDirectory))
    ->withConfiguredRule(ArraySyntaxFixer::class, [
        'syntax' => 'short'
    ])
    ->withConfiguredRule(ConstantCaseFixer::class, [
        'case' => 'lower'
    ])
    ->withConfiguredRule(OrderedClassElementsFixer::class, [
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
            'method:getInstance',
            'phpunit',
            'method:mockeryTestSetUp',
            'method:mockeryTestTearDown',
            'method_public',
            'method_protected',
            'method_private'
        ]
    ])
    ->withConfiguredRule(GeneralPhpdocAnnotationRemoveFixer::class, [
        'annotations' => ['small', 'internal', 'coversDefaultClass1', 'coversNothing']
    ])
    ->withConfiguredRule(GlobalNamespaceImportFixer::class, [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true
    ])
    ->withConfiguredRule(HeaderCommentFixer::class, [
        'header' => \implode("\n", [
            'Mockery (https://docs.mockery.io/en/stable/)',
            '',
            '@copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md',
            '@license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License',
            '@see       https://github.com/mockery/mockery for the canonical source repository'
        ]),
        'comment_type' => 'PHPDoc',
        'location' => 'after_declare_strict'
    ])
    ->withConfiguredRule(NativeFunctionInvocationFixer::class, [
        'include' => ['@all'],
        'scope' => 'all'
    ])
    ->withConfiguredRule(NoTrailingCommaInSinglelineFixer::class, [
        'elements' => ['arguments', 'array_destructuring', 'array', 'group_import']
    ])
    ->withConfiguredRule(OrderedImportsFixer::class, [
        'imports_order' => ['class', 'const', 'function']
    ])
    ->withConfiguredRule(PhpdocAlignFixer::class, [
        'tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var']
    ])
    ->withConfiguredRule(PhpdocOrderFixer::class, [
        'order' => ['param', 'return', 'throws']
    ])
    ->withConfiguredRule(PhpdocSeparationFixer::class, [
        'groups' => [
            ['deprecated', 'link', 'see', 'since'],
            ['author', 'copyright', 'license'],
            ['category', 'package', 'subpackage'],
            ['property', 'property-read', 'property-write'],
            ['param', 'return']
        ]
    ])
    ->withConfiguredRule(PhpUnitTestCaseStaticMethodCallsFixer::class, [
        'call_type' => 'self'
    ])
    ->withConfiguredRule(YodaStyleFixer::class, [
        'always_move_variable' => true
    ])
    ->withEditorConfig()
    ->withParallel()
    ->withPaths([__FILE__, __DIR__ . '/library', __DIR__ . '/tests'])
    ->withPreparedSets(psr12: true, common: true, cleanCode: true, standaloneLine: true)
    ->withRules([
        AddMissingParamNameFixer::class,
        AddMissingVarNameFixer::class,
        ArrayListItemNewlineFixer::class,
        ArrayOpenerAndCloserNewlineFixer::class,
        BlankLineAfterNamespaceFixer::class,
        BlankLineAfterOpeningTagFixer::class,
        BlankLineAfterStrictTypesFixer::class,
        BlankLineBeforeStatementFixer::class,
        BlankLineBetweenImportGroupsFixer::class,
        CleanNamespaceFixer::class,
        DoubleAsteriskInlineVarFixer::class,
        FinalClassFixer::class,
        FixParamNameTypoFixer::class,
        FullyQualifiedStrictTypesFixer::class,
        LambdaNotUsedImportFixer::class,
        LineLengthFixer::class,
        NoExtraBlankLinesFixer::class,
        NoLeadingImportSlashFixer::class,
        NoUnneededImportAliasFixer::class,
        NoUnusedImportsFixer::class,
        SingleImportPerStatementFixer::class,
        SingleLineAfterImportsFixer::class,
        SingleLineEmptyBodyFixer::class,
        SingleLineInlineVarDocBlockFixer::class,
        SpaceAfterCommaHereNowDocFixer::class,
        StandaloneLinePromotedPropertyFixer::class,
        SwitchedTypeAndNameFixer::class,
    ])
    ->withSkip([
        StrictComparisonFixer::class => [
            __DIR__ . '/library/Mockery/Matcher/IsEqual.php',
            __DIR__ . '/library/Mockery/Matcher/MustBe.php',
            __DIR__ . '/library/Mockery/Matcher/Subset.php',
            __DIR__ . '/library/Mockery/Generator/StringManipulation'
        ],
        FullyQualifiedStrictTypesFixer::class => [__DIR__ . '/library/helpers.php'],
        HeaderCommentFixer::class => [__DIR__ . '/tests/Fixture/*'],
        ConcatSpaceFixer::class,
        GetClassToClassKeywordFixer::class,
        GroupImportFixer::class,
        MethodChainingNewlineFixer::class,
        NewWithParenthesesFixer::class,
        PhpdocNoAliasTagFixer::class,
        PhpdocNoEmptyReturnFixer::class,
        PhpUnitInternalClassFixer::class,
        PhpUnitTestClassRequiresCoversFixer::class,
        RemoveMethodNameDuplicateDescriptionFixer::class,
        TrailingCommaInMultilineFixer::class,
        VoidReturnFixer::class,
        // Enable later
        DeclareStrictTypesFixer::class,
        FinalClassFixer::class,
        PhpUnitAttributesFixer::class,
        __DIR__ . '/library/Mockery/Mock.php',
        __DIR__ . '/tests/Fixture/*',
    ])
    ->withSpacing(Option::INDENTATION_SPACES, "\n");
