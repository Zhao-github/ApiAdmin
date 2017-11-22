<?php
/**
 * Api路由
 */

\think\Route::miss('api/Index/index');

$afterBehavior = ['\app\api\behavior\ApiAuth', '\app\api\behavior\RequestFilter'];

return [
    '[api]' => [
        '58bf98c1dcb63' => [
            'api/BuildToken/getAccessToken',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        '__miss__'      => ['api/Miss/index'],
    ],
];
