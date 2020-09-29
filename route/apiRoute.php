<?php
/**
 * Api路由
 */

use think\facade\Route;

Route::group('api', function() {
    //MISS路由定义
    Route::miss('api/Miss/index');
})->middleware('ApiResponse');
