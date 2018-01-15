<?php

return [
    '[admin]' => [
        'Login/index' => [
            'admin/Login/index',
            ['method' => 'post']
        ],
        'Login/logout' => [
            'admin/Login/logout',
            ['method' => 'get']
        ],
        '__miss__'      => ['admin/Miss/index'],
    ],
];
