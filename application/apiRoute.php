<?php
/**
 * Api路由
 */

\think\Route::miss('api/Index/index');

$afterBehavior = ['\app\api\behavior\ApiAuth', '\app\api\behavior\ApiPermission', '\app\api\behavior\RequestFilter'];

return [
    '[api]' => [
        '5a9363c133719' => [
            'api/BuildToken/getAccessToken',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        '5a93646b40ab5' => [
            'api/BuildToken/e1',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        '5a93648c769f8' => [
            'api/BuildToken/e2',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        '__miss__'      => ['api/Miss/index'],
    ],
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
