<?php

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
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
    ;
