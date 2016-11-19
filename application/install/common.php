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
function check_env(){
    $items = [
        'os' => [
            'title'   => '操作系统',
            'limit'   => '不限制',
            'current' => PHP_OS,
            'icon'    => 'fa fa-check',
        ],
        'php' => [
            'title'   => 'PHP版本',
            'limit'   => '5.6+',
            'current' => PHP_VERSION,
            'icon'    => 'fa fa-check',
        ],
        'upload' => [
            'title'   => '附件上传',
            'limit'   => '不限制',
            'current' => ini_get('file_uploads') ? ini_get('upload_max_filesize'):'未知',
            'icon'    => 'fa fa-check',
        ],
        'disk' => [
            'title'   => '磁盘空间',
            'limit'   => '100M+',
            'current' => '未知',
            'icon'    => 'fa fa-check',
        ],
    ];

    //PHP环境检测
    if($items['php']['current'] < 5.6){
        $items['php']['icon'] = 'fa fa-close';
        session('error', true);
    }

    //磁盘空间检测
    if(function_exists('disk_free_space')){
        $disk_size = floor(disk_free_space('./') / (1024*1024)).'M';
        $items['disk']['current'] = $disk_size.'B';
        if($disk_size < 100){
            $items['disk']['icon'] = 'fa fa-close';
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
function check_dirfile(){
    $items = [
        '0' => [
            'type'  => 'file',
            'path'  => ROOT_PATH . 'application/database.php',
            'title' => '可写',
            'icon'  => 'fa fa-check',
        ],
        '2' => [
            'type'  => 'dir',
            'path'  => RUNTIME_PATH,
            'title' => '可写',
            'icon'  => 'fa fa-check',
        ]
    ];

    foreach ($items as &$val){
        $path = $val['path'];
        if('dir' === $val['type']){
            if(!is_writable($path)){
                if(is_dir($path)) {
                    $val['title'] = '不可写';
                    $val['icon'] = 'fa fa-close';
                    session('error', true);
                }else{
                    $val['title'] = '不存在';
                    $val['icon'] = 'fa fa-close';
                    session('error', true);
                }
            }
        }else{
            if(file_exists($path)){
                if(!is_writable($path)){
                    $val['title'] = '不可写';
                    $val['icon'] = 'fa fa-close';
                    session('error', true);
                }
            }else{
                if(!is_writable(dirname($path))){
                    $val['title'] = '不存在';
                    $val['icon'] = 'fa fa-close';
                    session('error', true);
                }
            }
        }
    }
    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func_and_ext(){
    $items = [
        [
            'type'    => 'ext',
            'name'    => 'pdo',
            'title'   => '支持',
            'current' =>  extension_loaded('pdo'),
            'icon'    => 'fa fa-check',
            'isMust'  => true
        ],
        [
            'type'    => 'ext',
            'name'    => 'mongoDB（不必须）',
            'title'   => '支持',
            'current' =>  extension_loaded('mongo'),
            'icon'    => 'fa fa-check',
            'isMust'  => false
        ],
        [
            'type'    => 'ext',
            'name'    => 'Redis（不必须）',
            'title'   => '支持',
            'current' =>  extension_loaded('redis'),
            'icon'    => 'fa fa-check',
            'isMust'  => false
        ],
        [
            'type'    => 'func',
            'name'    => 'file_get_contents',
            'title'   => '支持',
            'icon'    => 'fa fa-check',
            'isMust'  => true
        ],
        [
            'type'    => 'func',
            'name'    => 'mb_strlen',
            'title'   => '支持',
            'icon'    => 'fa fa-check',
            'isMust'  => true
        ],
        [
            'type'    => 'func',
            'name'    => 'shell_exec',
            'title'   => '支持',
            'icon'    => 'fa fa-check',
            'isMust'  => true
        ],
        [
            'type'    => 'com',
            'name'    => 'mongodump（不必须）',
            'title'   => '支持',
            'icon'    => 'fa fa-check',
            'isMust'  => false
        ],
        [
            'type'    => 'com',
            'name'    => 'mongorestore（不必须）',
            'title'   => '支持',
            'icon'    => 'fa fa-check',
            'isMust'  => false
        ]
    ];
    foreach($items as &$val){
        switch($val['type']){
            case 'ext':
                if(!$val['current']){
                    $val['title'] = '不支持';
                    $val['icon'] = 'fa fa-close';
                    if( $val['isMust'] ){
                        session('error', true);
                    }
                }
                break;
            case 'func':
                if(!function_exists($val['name'])){
                    $val['title'] = '不支持';
                    $val['icon'] = 'fa fa-close';
                    if( $val['isMust'] ){
                        session('error', true);
                    }
                }
                break;
            case 'com':
                $com = 'which '.$val['name'];
                if(shell_exec($com) == null){
                    $val['title'] = '不支持';
                    $val['icon'] = 'fa fa-close';
                    if( $val['isMust'] ){
                        session('error', true);
                    }
                }
                break;
        }
    }

    return $items;
}

/**
 * 写入配置文件
 * @param $config
 * @param $type string 配置类型
 * @return bool
 */
function write_config($config, $type){
    if(is_array($config)){
        show_msg('开始写入'.$type.'配置文件');
        //读取配置内容
        $conf = file_get_contents(MODULE_PATH . 'Data/'.$type.'.tpl');
        //替换配置项
        foreach ($config as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }
        //写入应用配置文件
        if(file_put_contents(APP_PATH . 'Common/Conf/'.$type.'.php', $conf)){
            show_msg('配置文件'.$type.'写入成功', 'bg-success');
        }else{
            show_msg('配置文件'.$type.'写入失败！', 'bg-danger');
            session('error', true);
        }
        return true;
    }
}

/**
 * @param $msg
 * @param string $class
 */
function show_msg($msg, $class = ''){
    usleep(20000);
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    ob_flush();
    flush();
}
