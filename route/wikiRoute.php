<?php
/**
 * Wiki路由
 * @since   2019-08-12
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

use think\facade\Route;

Route::group('wiki', function() {
    Route::rule(
        'Api/errorCode', 'wiki/Api/errorCode', 'get'
    );

    //MISS路由定义
    Route::miss('admin/Miss/index');
})->middleware('AdminResponse');
