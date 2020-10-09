<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'apiadmin:adminRouter' => 'app\command\FreshAdminRouter',
        'apiadmin:install'     => 'app\command\Install',
        'apiadmin:test'        => 'app\command\ApiAdmin'
    ],
];
