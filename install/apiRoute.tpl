<?php
// +---------------------------------------------------------------------------------
// | ApiAdmin [ JUST FOR API ]
// +---------------------------------------------------------------------------------
// | Copyright (c) 2017~2020 https://www.apiadmin.org/ All rights reserved.
// +---------------------------------------------------------------------------------
// | Licensed ( https://gitee.com/apiadmin/ApiAdmin/blob/master/LICENSE.txt )
// +---------------------------------------------------------------------------------
// | Author: zhaoxiang <zhaoxiang051405@gmail.com>
// +---------------------------------------------------------------------------------

use think\facade\Route;

Route::group('api', function() {
    {$API_RULE}
    //MISS路由定义
    Route::miss('api.Miss/index');
})->middleware(app\middleware\ApiResponse::class);
