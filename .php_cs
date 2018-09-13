<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        // 'ordered_imports' => ['sort_algorithm' => 'length'],
    ])
    ->setFinder($finder)
;