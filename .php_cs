<?php

// See https://github.com/FriendsOfPHP/PHP-CS-Fixer for more details

$excludes = [
    'logs',
    'vendor',
];

$finder = PhpCsFixer\Finder::create()
    ->exclude($excludes)
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
    ])
    ->setFinder($finder)
;
