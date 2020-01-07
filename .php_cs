<?php

// See https://github.com/FriendsOfPHP/PHP-CS-Fixer for more details

$finder = PhpCsFixer\Finder::create()
    // Folders to exclude
    ->exclude([
        'config',
        'logs',
        'vendor',
    ])
    // Filenames to exclude
    ->notName('_*')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
    ])
    ->setFinder($finder)
;
