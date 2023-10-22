<?php

// The following line is just necessary in this test setup.
// the classes and helpers will be available from composer autoload in a production setup.
@include_once __DIR__.'/../../../vendor/autoload.php';

use Kirby\Cms\App as Kirby;

autoloader(__DIR__); // testing multi plugin setup

Kirby::plugin('bnomei/another', [
    'options' => [

    ],
]);
