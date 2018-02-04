<?php
$afterBehavior = ['\app\admin\behavior\ApiAuth', '\app\admin\behavior\ApiPermission'];

return [
    '[admin]' => [
        'Login/index'       => [
            'admin/Login/index',
            ['method' => 'post']
        ],
        'Login/logout'      => [
            'admin/Login/logout',
            ['method' => 'get']
        ],
        'Menu/index'        => [
            'admin/Menu/index',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        'Menu/changeStatus' => [
            'admin/Menu/changeStatus',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        'Menu/add' => [
            'admin/Menu/add',
            ['method' => 'post', 'after_behavior' => $afterBehavior]
        ],
        'Menu/edit' => [
            'admin/Menu/edit',
            ['method' => 'post', 'after_behavior' => $afterBehavior]
        ],
        'Menu/del' => [
            'admin/Menu/del',
            ['method' => 'get', 'after_behavior' => $afterBehavior]
        ],
        '__miss__'          => ['admin/Miss/index'],
    ],
];
