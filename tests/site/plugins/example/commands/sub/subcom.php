<?php

return [
    'description' => 'Nice sub command',
    'args' => [],
    'command' => static function ($cli): void {
        $cli->success('Nice sub command!');
    },
];
