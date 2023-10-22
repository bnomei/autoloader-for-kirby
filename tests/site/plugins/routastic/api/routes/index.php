<?php

return [
    'pattern' => 'routastic/(:any)',
    'action' => function (string $any) {
        return 'api.index.'.$any;
    },
];
