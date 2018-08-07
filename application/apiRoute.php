<?php
/**
 * Api路由
 */

use think\Route;

Route::miss('api/Miss/index');
$afterBehavior = [
    '\app\api\behavior\ApiAuth',
    '\app\api\behavior\ApiPermission',
    '\app\api\behavior\RequestFilter'
];
