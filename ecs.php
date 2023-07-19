<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedInterfacesFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();

    $ecsConfig->sets([
        SetList::ARRAY,
        SetList::CLEAN_CODE,
        SetList::COMMON,
        SetList::CONTROL_STRUCTURES,
        SetList::NAMESPACES,
        SetList::PSR_12,
        SetList::DOCBLOCK,
        SetList::PHPUNIT,
        SetList::SPACES,
        SetList::STRICT,
    ]);

    $ecsConfig->rules([
        NoEmptyStatementFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(GlobalNamespaceImportFixer::class, [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ]);

    $ecsConfig->ruleWithConfiguration(OrderedImportsFixer::class, [
        'imports_order' => ['class', 'const', 'function'],
    ]);

    $ecsConfig->ruleWithConfiguration(PhpdocAlignFixer::class, [
        'tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var'],
    ]);

    $ecsConfig->ruleWithConfiguration(PhpUnitTestCaseStaticMethodCallsFixer::class, [
        'call_type' => 'self',
    ]);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->ruleWithConfiguration(ConstantCaseFixer::class, [
        'case' => 'lower',
    ]);

    $ecsConfig->ruleWithConfiguration(OrderedClassElementsFixer::class, [
        'sort_algorithm' => 'alpha',
    ]);

    $ecsConfig->ruleWithConfiguration(OrderedInterfacesFixer::class, [
        'order' => 'alpha',
    ]);

    $ecsConfig->paths([
        __FILE__,
        __DIR__ . '/library',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->skip([
        __DIR__ . '/library/Mockery/Mock.php',
        __DIR__ . '/tests/Fixture/*',
        __DIR__ . '/tests/Mockery/*', // skip temporarily, it still has legacy code
    ]);
};
