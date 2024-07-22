<?php

// The following line is just necessary in this test setup.
// the classes and helpers will be available from composer autoload in a production setup.
@include_once __DIR__.'/../../../vendor/autoload.php';

use Kirby\Cms\App as Kirby;

// disallow additional dots for testcases
autoloader(__DIR__, [
    'blueprints' => ['name' => \Bnomei\Autoloader::PHP_OR_YML],
    'snippets' => ['name' => \Bnomei\Autoloader::PHP_OR_HTMLPHP],
]);

autoloader(__DIR__)->classes();
$a = autoloader(__DIR__)->toArray([
    'options' => [
        'test' => 'Test',
    ],
    'blueprints' => [
        'fields/test' => [ // <-- this must be merged
            'type' => 'info',
            'text' => 'Test',
        ],
    ],
]);

ray($a)->red();
Kirby::plugin('bnomei/example', $a);
