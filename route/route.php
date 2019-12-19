<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

Route::miss('api/Miss/index');

Route::group('admin', function() {
    Route::rule(
        'Login/index', 'admin/Login/index', 'post'
    );
    Route::rule(
        'ThirdLogin/getQQCode', 'admin/ThirdLogin/getQQCode', 'get'
    );
    Route::rule(
        'ThirdLogin/getWxCode', 'admin/ThirdLogin/getWxCode', 'get'
    );
    Route::rule(
        'ThirdLogin/wx', 'admin/ThirdLogin/wx', 'get'
    );
    Route::rule(
        'ThirdLogin/loginByQQ', 'admin/ThirdLogin/loginByQQ', 'get'
    );
    Route::rule(
        'ThirdLogin/loginByWx', 'admin/ThirdLogin/loginByWx', 'get'
    );
    Route::rule(
        'ThirdLogin/checkWxLogin', 'admin/ThirdLogin/checkWxLogin', 'get'
    );
    Route::rule(
        'ThirdLogin/getQr', 'admin/ThirdLogin/getQr', 'get'
    );
    Route::rule(
        'Index/upload', 'admin/Index/upload', 'post'
    )->middleware(['AdminAuth', 'AdminLog']);
    Route::rule(
        'Login/logout', 'admin/Login/logout', 'get'
    )->middleware(['AdminAuth', 'AdminLog']);
    Route::rule(
        'Login/getUserInfo', 'admin/Login/getUserInfo', 'get'
    )->middleware(['AdminAuth', 'AdminLog']);

    //大部分控制器的路由都以分组的形式写到这里
    Route::group('Menu', [
        'index'        => [
            'admin/Menu/index',
            ['method' => 'get']
        ],
        'changeStatus' => [
            'admin/Menu/changeStatus',
            ['method' => 'get']
        ],
        'add'          => [
            'admin/Menu/add',
            ['method' => 'post']
        ],
        'edit'         => [
            'admin/Menu/edit',
            ['method' => 'post']
        ],
        'del'          => [
            'admin/Menu/del',
            ['method' => 'get']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('User', [
        'index'        => [
            'admin/User/index',
            ['method' => 'get']
        ],
        'getUsers'     => [
            'admin/User/getUsers',
            ['method' => 'get']
        ],
        'changeStatus' => [
            'admin/User/changeStatus',
            ['method' => 'get']
        ],
        'add'          => [
            'admin/User/add',
            ['method' => 'post']
        ],
        'own'          => [
            'admin/User/own',
            ['method' => 'post']
        ],
        'edit'         => [
            'admin/User/edit',
            ['method' => 'post']
        ],
        'del'          => [
            'admin/User/del',
            ['method' => 'get']
        ],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('Auth', [
        'index'        => [
            'admin/Auth/index',
            ['method' => 'get']
        ],
        'changeStatus' => [
            'admin/Auth/changeStatus',
            ['method' => 'get']
        ],
        'add'          => [
            'admin/Auth/add',
            ['method' => 'post']
        ],
        'delMember'    => [
            'admin/Auth/delMember',
            ['method' => 'get']
        ],
        'edit'         => [
            'admin/Auth/edit',
            ['method' => 'post']
        ],
        'del'          => [
            'admin/Auth/del',
            ['method' => 'get']
        ],
        'getGroups'    => [
            'admin/Auth/getGroups',
            ['method' => 'get']
        ],
        'getRuleList'  => [
            'admin/Auth/getRuleList',
            ['method' => 'get']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('App', [
        'index'            => [
            'admin/App/index',
            ['method' => 'get']
        ],
        'refreshAppSecret' => [
            'admin/App/refreshAppSecret',
            ['method' => 'get']
        ],
        'changeStatus'     => [
            'admin/App/changeStatus',
            ['method' => 'get']
        ],
        'add'              => [
            'admin/App/add',
            ['method' => 'post']
        ],
        'getAppInfo'       => [
            'admin/App/getAppInfo',
            ['method' => 'get']
        ],
        'edit'             => [
            'admin/App/edit',
            ['method' => 'post']
        ],
        'del'              => [
            'admin/App/del',
            ['method' => 'get']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('InterfaceList', [
        'index'        => [
            'admin/InterfaceList/index',
            ['method' => 'get']
        ],
        'changeStatus' => [
            'admin/InterfaceList/changeStatus',
            ['method' => 'get']
        ],
        'add'          => [
            'admin/InterfaceList/add',
            ['method' => 'post']
        ],
        'refresh'      => [
            'admin/InterfaceList/refresh',
            ['method' => 'get']
        ],
        'edit'         => [
            'admin/InterfaceList/edit',
            ['method' => 'post']
        ],
        'del'          => [
            'admin/InterfaceList/del',
            ['method' => 'get']
        ],
        'getHash'      => [
            'admin/InterfaceList/getHash',
            ['method' => 'get']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('Fields', [
        'index'    => [
            'admin/Fields/index',
            ['method' => 'get']
        ],
        'request'  => [
            'admin/Fields/request',
            ['method' => 'get']
        ],
        'add'      => [
            'admin/Fields/add',
            ['method' => 'post']
        ],
        'response' => [
            'admin/Fields/response',
            ['method' => 'get']
        ],
        'edit'     => [
            'admin/Fields/edit',
            ['method' => 'post']
        ],
        'del'      => [
            'admin/Fields/del',
            ['method' => 'get']
        ],
        'upload'   => [
            'admin/Fields/upload',
            ['method' => 'post']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('InterfaceGroup', [
        'index'        => [
            'admin/InterfaceGroup/index',
            ['method' => 'get']
        ],
        'getAll'       => [
            'admin/InterfaceGroup/getAll',
            ['method' => 'get']
        ],
        'add'          => [
            'admin/InterfaceGroup/add',
            ['method' => 'post']
        ],
        'changeStatus' => [
            'admin/InterfaceGroup/changeStatus',
            ['method' => 'get']
        ],
        'edit'         => [
            'admin/InterfaceGroup/edit',
            ['method' => 'post']
        ],
        'del'          => [
            'admin/InterfaceGroup/del',
            ['method' => 'get']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('AppGroup', [
        'index'        => [
            'admin/AppGroup/index',
            ['method' => 'get']
        ],
        'getAll'       => [
            'admin/AppGroup/getAll',
            ['method' => 'get']
        ],
        'add'          => [
            'admin/AppGroup/add',
            ['method' => 'post']
        ],
        'changeStatus' => [
            'admin/AppGroup/changeStatus',
            ['method' => 'get']
        ],
        'edit'         => [
            'admin/AppGroup/edit',
            ['method' => 'post']
        ],
        'del'          => [
            'admin/AppGroup/del',
            ['method' => 'get']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    Route::group('Log', [
        'index' => [
            'admin/Log/index',
            ['method' => 'get']
        ],
        'del'   => [
            'admin/Log/del',
            ['method' => 'get']
        ]
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    //MISS路由定义
    Route::miss('admin/Miss/index');
})->middleware('AdminResponse');
