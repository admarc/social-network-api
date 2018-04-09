<?php

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCS(true)
    ->exclude([
        'vendor/',
        'src/Migrations',
        'var/',
    ])
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
    ])
    ->setUsingCache(false)
    ->setFinder($finder);
