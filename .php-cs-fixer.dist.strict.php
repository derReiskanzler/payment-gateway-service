<?php

/**
 * This is the strict code style of allmyhomes platform engineering teams.
 * You are not required to use it (except if you're working with PET-projects like the boilerplate itself)
 *
 * It performs several optimizations, removes redundancies, ensures type-safety and may satisfies some peoples OCD
 * But it can break your applications in certain circumstances, therefore you may have to invest some time into fixing
 * some type-errors etc. or rewriting some magic methods.
 */

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('src/Infrastructure/Boilerplate/Laravel/storage')
    ->exclude('src/Infrastructure/Boilerplate/Laravel/bootstrap')
    ->exclude('src/Infrastructure/Boilerplate/Laravel/resources')
    ->notPath('tests/_output')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHPUnit84Migration:risky' => true,
        '@Symfony:risky' => true,
        '@Symfony' => true,
        '@PHP80Migration:risky' => true,
        '@PHP80Migration' => true,
        // Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
        'align_multiline_comment' => true,
        // Using `isset($var) &&` multiple times should be done in one call.
        'combine_consecutive_issets' => true,
        // Calling `unset` on multiple items should be done in one call.
        'combine_consecutive_unsets' => true,
        // Comments with annotation should be docblock when used on structural elements.
        'comment_to_phpdoc' => true,
        // Escape implicit backslashes in strings and heredocs to ease the understanding of which are special chars interpreted by PHP and which not.
        'escape_implicit_backslashes' => true,
        // Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.
        'explicit_indirect_variable' => true,
        // Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.
        'explicit_string_variable' => true,
        // Internal classes should be `final`.
        'final_internal_class' => true,
        // All `public` methods of `abstract` classes should be `final`.
        'final_public_method_for_abstract_class' => true,
        // Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.
        'method_chaining_indentation' => true,
        // DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.
        'multiline_comment_opening_closing' => true,
        // Replaces superfluous `elseif` with `if`.
        'no_superfluous_elseif' => true,
        // There should not be useless `else` cases.
        'no_useless_else' => true,
        // There should not be an empty `return` statement at the end of a function.
        'no_useless_return' => true,
        // Adds or removes `?` before type declarations for parameters with a default `null` value.
        'nullable_type_declaration_for_default_null_value' => true,
        // Literal octal must be in `0o` notation.
        'octal_notation' => true,
        // PHPUnit methods like `assertSame` should be used instead of `assertEquals`.
        'php_unit_strict' => true,
        // Calls to `PHPUnit\Framework\TestCase` static methods must all be of the same type, either `$this->`, `self::` or `static::`.
        'php_unit_test_case_static_method_calls' => true,
        // Changes doc blocks from single to multi line, or reversed. Works for class constants, properties and methods only.
        'phpdoc_line_span' => true,
        // `@return void` and `@return null` annotations should be omitted from PHPDoc.
        'phpdoc_no_empty_return' => true,
        // Annotations in PHPDoc should be ordered so that `@param` annotations come first, then `@throws` annotations, then `@return` annotations.
        'phpdoc_order' => true,
        // Order phpdoc tags by value.
        'phpdoc_order_by_value' => true,
        // Fixes casing of PHPDoc tags.
        'phpdoc_tag_casing' => true,
        // `@var` and `@type` annotations must have type and name in the correct order.
        'phpdoc_var_annotation_correct_order' => true,
        // Callables must be called without using `call_user_func*` when possible.
        'regular_callable_call' => true,
        // Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.
        'return_assignment' => true,
        // Inside a `final` class or anonymous class `self` should be preferred to `static`.
        'self_static_accessor' => true,
        // Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).
        'simple_to_complex_string_variable' => true,
        // Simplify `if` control structures that return the boolean result of their condition.
        'simplified_if_return' => true,
        // A return statement wishing to return `void` should not return `null`.
        'simplified_null_return' => true,
        // Lambdas not (indirect) referencing `$this` must be declared `static`.
        'static_lambda' => true,
        // Comparisons should be strict.
        'strict_comparison' => true,
        // Functions should be used with `$strict` param set to `true`.
        'strict_param' => true,
        // Orders the elements of a class
        'ordered_class_elements' => [
            'order'=> [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'property_public_readonly',
                'property_protected_readonly',
                'property_private_readonly',
                'property_public',
                'property_protected',
                'property_private',
                'method_public_static',
                'method_protected_static',
                'method_private_static',
                'method_public_abstract_static',
                'method_protected_abstract_static',
                'construct',
                'phpunit',
                'method_public_abstract',
                'method_protected_abstract',
                'method_public',
                'method_protected',
                'method_private',
                'magic',
                'destruct',
            ],
        ],
    ])
    ->setFinder($finder)
    ;
