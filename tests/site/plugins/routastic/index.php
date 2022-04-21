<?php

// The following line is just necessary in this test setup.
// the classes and helpers will be available from composer autoload in a production setup.
@include_once __DIR__ . '/../../../vendor/autoload.php';

use Kirby\Cms\App as Kirby;

Kirby::plugin('bnomei/routastic', autoloader(__DIR__)->toArray([
    'options' => [
        'merged' => true,
    ],
    'routes' => [
        [
            'pattern' => 'merged',
            'action' => function () {
                return [];
            },
        ],
    ],
]));
