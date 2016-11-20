<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'PRODUCT_VERSION' => 'V1.1.0', //项目版本
    'PRODUCT_NAME'   => 'ApiAdmin', //产品名称
    'WEBSITE_DOMAIN' => 'http://zxblog.our-dream.cn', //官方网址
    'COMPANY_NAME'   => 'ApiAdmin开发维护团队', //公司名称
    'SQL_PRIMARY_KEY' => 'id',

    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__STATIC__'=>'/static',
        '__CSS__'=>'/static/css',
        '__JS__'=>'/static/js',
        '__IMG__'=>'/static/img',
        '__PLUGIN__'=>'/static/plugin',
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => 'public/jump',
    'dispatch_error_tmpl'    => 'public/jump',
];
