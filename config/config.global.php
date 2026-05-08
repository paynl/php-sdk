<?php

declare(strict_types=1);

return [
    'authentication' => [
        'username' => '', # Use AT-Code or SL-Code. Use AT-code together with API-Token.
        'password' => '', # Use API token or secret. Use Secret in combination with SL-Code.
    ],
    'debug' => false,
    'useFileCaching' => true
];
