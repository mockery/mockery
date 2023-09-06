<?php

declare(strict_types=1);

namespace Mockery\PhpCsFixerConfig;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedInterfacesFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;

$header = <<<'EOF'
Mockery (https://docs.mockery.io/)

@copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
@license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
@link      https://github.com/mockery/mockery for the canonical source repository
EOF;

$finder = Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->exclude([
        __DIR__ . '/fixtures',
        __DIR__ . '/vendor'
    ])
    ->in(__DIR__ . '/tests/Unit/PHP80')
    ->in(__DIR__ . '/tests/Unit/PHP81')
    ->in(__DIR__ . '/tests/Unit/PHP82')
;

$config = new Config();
$config->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        // '@PhpCsFixer:risky' => true,
        '@PSR12' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'const', 'function'],
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'ordered_class_elements' => [
            'sort_algorithm' => 'alpha',
            'case_sensitive' => true,
        ],
        // 'ordered_interfaces' => [
        //     'case_sensitive' => true,
        //     'order' => 'alpha',
        //     'direction' => 'ascend',
        // ],
        'header_comment' => [
            'header' => $header,
            'comment_type' => 'PHPDoc',
        ],
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_between_import_groups' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'final_class' => true,
        'function_declaration' => true,
        'is_null' => true,
        'lowercase_cast' => true,
        'lowercase_keywords' => true,
        'lowercase_static_reference' => true,
        'method_chaining_indentation' => true,
        'native_constant_invocation' => true,
        'native_function_casing' => true,
        'native_function_invocation' => true,
        'native_function_type_declaration_casing' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_multiple_statements_per_line' => true,
        'no_unneeded_final_method' => true,
        'no_unneeded_import_alias' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'ordered_types' => true,
        'php_unit_data_provider_name' => true,
        'php_unit_data_provider_return_type' => true,
        'php_unit_data_provider_static' => true,
        'php_unit_dedicate_assert_internal_type' => true,
        'php_unit_dedicate_assert' => true,
        'php_unit_expectation' => true,
        'php_unit_fqcn_annotation' => true,
        'php_unit_internal_class' => true,
        'php_unit_method_casing' => true,
        'php_unit_mock_short_will_return' => true,
        'php_unit_mock' => true,
        'php_unit_no_expectation_annotation' => true,
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_test_annotation' => true,
        'php_unit_test_case_static_method_calls' => true,
        'phpdoc_align' => true,
        'phpdoc_order_by_value' => true,
        'phpdoc_param_order' => true,
        'phpdoc_tag_casing' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_trim' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        'protected_to_private' => true,
        'return_type_declaration' => true,
        'simplified_if_return' => true,
        'simplified_null_return' => true,
        'single_blank_line_at_eof' => true,
        'single_import_per_statement' => true,
        'strict_param' => true,
        'string_length_to_empty' => true,
        'switch_case_space' => true,
        'trim_array_spaces' => true,
        'type_declaration_spaces' => true,
        'types_spaces' => true,
        'unary_operator_spaces' => true,
        'visibility_required' => true,
        'void_return' => true,
        // 'constant_case' => ['case' => 'lower'],
        // 'lambda_not_used_import' => true,
        // 'mb_str_functions' => true,
        // 'no_superfluous_elseif' => true,
        // 'phpdoc_order' => true,
        // 'php_unit_strict' => true,
        // 'regular_callable_call' => true,
        // 'return_to_yield_from' => true,
        // 'ternary_to_elvis_operator' => true,
        // 'use_arrow_functions' => true,
        // 'yield_from_array_to_yields' => true,
    ])
    ->setFinder($finder)
;

return $config;
