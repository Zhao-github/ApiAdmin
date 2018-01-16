<?php
$afterBehavior = ['\app\admin\behavior\ApiAuth', '\app\admin\behavior\ApiPermission'];

return [
    '[admin]' => [
        'Login/index'  => [
            'admin/Login/index',
            ['method' => 'post']
        ],
        'Login/logout' => [
            'admin/Login/logout',
            ['method' => 'get']
        ],
        'Menu/index'   => [
            'admin/Menu/index',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        '__miss__'     => ['admin/Miss/index'],
    ],
];
