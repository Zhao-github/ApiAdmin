<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

/**
 * 系统环境检测
 * @return array 系统环境数据
 * @author jry <598821125@qq.com>
 */
function checkEnv(){
    $items = [
        'os' => [
            'title'   => '操作系统',
            'limit'   => '不限制',
            'current' => PHP_OS,
            'icon'    => 'fa fa-check text-success',
        ],
        'php' => [
            'title'   => 'PHP版本',
            'limit'   => '5.4+',
            'current' => PHP_VERSION,
            'icon'    => 'fa fa-check text-success',
        ],
        'upload' => [
            'title'   => '附件上传',
            'limit'   => '不限制',
            'current' => ini_get('file_uploads') ? ini_get('upload_max_filesize'):'未知',
            'icon'    => 'fa fa-check text-success',
        ],
        'disk' => [
            'title'   => '磁盘空间',
            'limit'   => '100M+',
            'current' => '未知',
            'icon'    => 'fa fa-check text-success',
        ],
    ];

    //PHP环境检测
    if($items['php']['current'] < 5.4){
        $items['php']['icon'] = 'fa fa-close text-danger';
        session('error', true);
    }

    //磁盘空间检测
    if(function_exists('disk_free_space')){
        $disk_size = floor(disk_free_space('./') / (1024*1024)).'M';
        $items['disk']['current'] = $disk_size.'B';
        if($disk_size < 100){
            $items['disk']['icon'] = 'fa fa-close text-danger';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 * @author jry <598821125@qq.com>
 */
function checkDirFile(){
    $items = [
        [
            'type'  => 'file',
            'path'  => realpath(APP_PATH) .DS. 'database.php',
            'title' => '可写',
            'icon'  => 'fa fa-check text-success',
        ],
        [
            'type'  => 'dir',
            'path'  => RUNTIME_PATH,
            'title' => '可写',
            'icon'  => 'fa fa-check text-success',
        ],
        [
            'type'  => 'dir',
            'path'  => realpath(APP_PATH) .DS. 'extra' . DS,
            'title' => '可写',
            'icon'  => 'fa fa-check text-success',
        ]
    ];

    foreach ($items as &$val){
        $path = $val['path'];
        if('dir' === $val['type']){
            if(!is_writable($path)){
                if(is_dir($path)) {
                    $val['title'] = '不可写';
                    $val['icon'] = 'fa fa-close text-danger';
                    session('error', true);
                }else{
                    $val['title'] = '不存在';
                    $val['icon'] = 'fa fa-close text-danger';
                    session('error', true);
                }
            }
        }else{
            if(file_exists($path)){
                if(!is_writable($path)){
                    $val['title'] = '不可写';
                    $val['icon'] = 'fa fa-close text-danger';
                    session('error', true);
                }
            }else{
                if(!is_writable(dirname($path))){
                    $val['title'] = '不存在';
                    $val['icon'] = 'fa fa-close text-danger';
                    session('error', true);
                }
            }
        }
    }
    return $items;
}

/**
 * MySQL数据库依赖检测
 * @return array 检测数据
 */
function checkMySQL(){
    $items = [
        [
            'type'    => 'ext',
            'name'    => 'pdo',
            'title'   => '支持',
            'current' =>  extension_loaded('pdo'),
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'ext',
            'name'    => 'pdo_mysql',
            'title'   => '支持',
            'current' =>  extension_loaded('pdo_mysql'),
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ]
    ];
    return baseCheck($items);
}

/**
 * MongoDB数据库依赖检测
 * @return array 检测数据
 */
function checkMongoDB(){
    $items = [
        [
            'type'    => 'ext',
            'name'    => 'mongoDB',
            'title'   => '支持',
            'current' =>  extension_loaded('mongo'),
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'ext',
            'name'    => 'ZipArchive',
            'title'   => '支持',
            'current' =>  class_exists('ZipArchive'),
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'func',
            'name'    => 'shell_exec',
            'title'   => '支持',
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'com',
            'name'    => 'zip',
            'title'   => '支持',
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'com',
            'name'    => 'mongodump',
            'title'   => '支持',
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'com',
            'name'    => 'mongorestore',
            'title'   => '支持',
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ]
    ];
    return baseCheck($items);
}

/**
 * Redis缓存依赖检测
 * @return array 检测数据
 */
function checkRedis(){
    $items = [
        [
            'type'    => 'ext',
            'name'    => 'Redis',
            'title'   => '支持',
            'current' =>  extension_loaded('redis'),
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ]
    ];
    return baseCheck($items);
}

/**
 * 其他公共部分检测
 * @return mixed
 */
function checkOther(){
    $items = [
        [
            'type'    => 'func',
            'name'    => 'file_get_contents',
            'title'   => '支持',
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'func',
            'name'    => 'mb_strlen',
            'title'   => '支持',
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
        [
            'type'    => 'func',
            'name'    => 'curl_init',
            'title'   => '支持',
            'icon'    => 'fa fa-check text-success',
            'isMust'  => true
        ],
    ];
    return baseCheck($items);
}

/**
 * 基础函数检测
 * @param $items
 * @return mixed
 */
function baseCheck($items){
    foreach($items as &$val){
        switch($val['type']){
            case 'ext':
                if(!$val['current']){
                    $val['title'] = '不支持';
                    $val['icon'] = 'fa fa-close text-danger';
                    if( $val['isMust'] ){
                        session('error', true);
                    }
                }
                break;
            case 'func':
                if(!function_exists($val['name'])){
                    $val['title'] = '不支持';
                    $val['icon'] = 'fa fa-close text-danger';
                    if( $val['isMust'] ){
                        session('error', true);
                    }
                }
                break;
            case 'com':
                $com = 'which '.$val['name'];
                if( !function_exists('shell_exec') ){
                    $val['title'] = '不支持';
                    $val['icon'] = 'fa fa-close text-danger';
                    session('error', true);
                }else{
                    if(shell_exec($com) == null){
                        $val['title'] = '不支持';
                        $val['icon'] = 'fa fa-close text-danger';
                        if( $val['isMust'] ){
                            session('error', true);
                        }
                    }
                }
                break;
        }
    }
    return $items;
}

/**
 * @param $msg
 * @param string $class
 */
function showMsg($msg, $class = ''){
    usleep(20000);
    echo "<script type=\"text/javascript\">showMsg(\"{$msg}\", \"{$class}\")</script>";
    ob_flush();
    flush();
}
