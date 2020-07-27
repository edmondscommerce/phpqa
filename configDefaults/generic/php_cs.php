<?php
/**
 * This is the default PHP-CS-Fixer configs for PHPQA projects
 *
 * You can override this file by copying it into your qaConfig folder and editing as you see fit
 *
 * For rules, suggest you have a look at
 *
 * @see https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16
 */

use Composer\Autoload\ClassLoader;

$rules = [
    '@PhpCsFixer'                  => true,
    '@Symfony'                     => true,
    '@DoctrineAnnotation'          => true,
    'align_multiline_comment'      => true,
    'array_indentation'            => true,
    'array_syntax'                 => ['syntax' => 'short'],
    'blank_line_after_opening_tag' => true,
    'binary_operator_spaces'       => [
        'align_double_arrow' => true,
        'align_equals'       => true,
    ],
    'cast_spaces'                  => ['space' => 'none'],
    'concat_space'                 => ['spacing' => 'one'],
    'declare_strict_types'         => true,
    'final_class'                  => true,
    'ordered_class_elements'       => [
        'use_trait',
        'constant_public',
        'constant_protected',
        'constant_private',
        'property_public',
        'property_protected',
        'property_private',
        'construct',
        'destruct',
        'magic',
        'phpunit',
        'method_public',
        'method_protected',
        'method_private',
    ],
    # fights with PSR-12 in phpcs/phpcbf
    'ordered_imports'              => [
        'sort_algorithm' => 'alpha',
        # this is the PSR12 order, do not change
        'imports_order'  => [
            'class',
            'function',
            'const',
        ],
    ],
    'modernize_types_casting'      => true,
    'php_unit_strict'              => [
        'assertAttributeEquals',
        'assertAttributeNotEquals',
        'assertEquals',
        'assertNotEquals',
    ],
    'php_unit_size_class'          => ['group' => 'small'],
    'phpdoc_to_return_type'        => true,
    'psr4'                         => true,
    'return_assignment'            => true,
    'self_accessor'                => true,
    'static_lambda'                => true,
    'strict_comparison'            => true,
    'strict_param'                 => true,
    'ternary_to_null_coalescing'   => true,
    'void_return'                  => true,
    'yoda_style'                   => [
        'equal'     => false,
        'identical' => false,
    ],
    'fully_qualified_strict_types' => true,
    'method_argument_space'        => [
        'ensure_fully_multiline'           => true,
        'keep_multiple_spaces_after_comma' => true,
        'on_multiline'                     => 'ensure_fully_multiline',
    ],
    'single_line_throw'            => false,
    'global_namespace_import'      => true,
];


$projectRoot = (
function () {
    $reflection = new ReflectionClass(ClassLoader::class);

    return dirname($reflection->getFileName(), 3);
}
)();

$finder = PhpCsFixer\Finder::create()
                           ->in($projectRoot)
                           ->exclude('var');

return PhpCsFixer\Config::create()
                        ->setRules($rules)
                        ->setFinder($finder);
