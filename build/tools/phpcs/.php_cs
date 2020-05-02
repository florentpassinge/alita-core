<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'binary_operator_spaces' => [
            'default' => 'align_single_space_minimal',
        ],
        'declare_equal_normalize' => [
            'space' => 'single',
        ],
        'no_superfluous_phpdoc_tags' => false
    ])
;
