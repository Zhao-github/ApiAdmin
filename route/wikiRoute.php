<?php
/**
 * Wiki路由
 * @since   2019-08-12
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

use think\facade\Route;

Route::group('wiki', function() {
    Route::rule(
        'Api/login', 'wiki.Api/login', 'post'
    );
    Route::group('Api', function() {
        Route::rule('login', 'wiki.Api/login', 'post');
        Route::rule('errorCode', 'wiki.Api/errorCode', 'get');
        Route::rule('groupList', 'wiki.Api/groupList', 'get');
        Route::rule('detail', 'wiki.Api/detail', 'get');
        Route::rule('logout', 'wiki.Api/logout', 'get');
    })->middleware([app\middleware\WikiAuth::class]);

    //MISS路由定义
    Route::miss('admin.Miss/index');
})->middleware(app\middleware\AdminResponse::class);
