<?php
/**
 * wiki路由
 */

return [
    '[wiki]' => [
        'login' => [
            'wiki/index/login',
            ['method' => 'get']
        ],
        'doLogin' => [
            'wiki/index/doLogin',
            ['method' => 'post']
        ],
        'index' => [
            'wiki/index/index',
            ['method' => 'get']
        ],
        'calculation' => [
            'wiki/index/calculation',
            ['method' => 'get']
        ],
        'errorCode' => [
            'wiki/index/errorCode',
            ['method' => 'get']
        ],
        'detail/:groupHash/[:hash]' => [
            'wiki/index/detail',
            ['method' => 'get']
        ],
        'logout' => [
            'wiki/index/logout',
            ['method' => 'get']
        ],
        '__miss__'      => ['wiki/index/index'],
    ],
];
