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
    $items = array(
        'os' => array(
            'title'   => '操作系统',
            'limit'   => '不限制',
            'current' => PHP_OS,
            'icon'    => 'am-text-success am-icon-check',
        ),
        'php' => array(
            'title'   => 'PHP版本',
            'limit'   => '5.3+',
            'current' => PHP_VERSION,
            'icon'    => 'am-text-success am-icon-check',
        ),
        'upload' => array(
            'title'   => '附件上传',
            'limit'   => '不限制',
            'current' => ini_get('file_uploads') ? ini_get('upload_max_filesize'):'未知',
            'icon'    => 'am-text-success am-icon-check',
        ),
//        'gd' => array(
//            'title'   => 'GD库',
//            'limit'   => '2.0+',
//            'current' => '未知',
//            'icon'    => 'am-text-success am-icon-check',
//        ),
        'disk' => array(
            'title'   => '磁盘空间',
            'limit'   => '100M+',
            'current' => '未知',
            'icon'    => 'am-text-success am-icon-check',
        ),
    );

    //PHP环境检测
    if($items['php']['current'] < 5.3){
        $items['php']['icon'] = 'am-text-danger am-icon-close';
        session('error', true);
    }

//    //GD库检测
//    $tmp = function_exists('gd_info') ? gd_info() : array();
//    if(!$tmp['GD Version']){
//        $items['gd']['current'] = '未安装';
//        $items['gd']['icon'] = 'am-text-danger am-icon-close';
//        session('error', true);
//    }else{
//        $items['gd']['current'] = $tmp['GD Version'];
//    }
//    unset($tmp);

    //磁盘空间检测
    if(function_exists('disk_free_space')){
        $disk_size = floor(disk_free_space('./') / (1024*1024)).'M';
        $items['disk']['current'] = $disk_size.'B';
        if($disk_size < 100){
            $items['disk']['icon'] = 'am-text-danger am-icon-close';
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
    $items = array(
        '0' => array(
            'type'  => 'file',
            'path'  => APP_PATH . 'Common/Conf/db.php',
            'title' => '可写',
            'icon'  => 'am-text-success am-icon-check',
        ),
        '1' => array(
            'type'  => 'dir',
            'path'  => APP_PATH . 'Common/Conf',
            'title' => '可写',
            'icon'  => 'am-text-success am-icon-check',
        ),
        '2' => array(
            'type'  => 'dir',
            'path'  => RUNTIME_PATH,
            'title' => '可写',
            'icon'  => 'am-text-success am-icon-check',
        )
    );

    foreach ($items as &$val){
        $path = $val['path'];
        if('dir' === $val['type']){
            if(!is_writable($path)){
                if(is_dir($path)) {
                    $val['title'] = '不可写';
                    $val['icon'] = 'am-text-danger am-icon-close';
                    session('error', true);
                }else{
                    $val['title'] = '不存在';
                    $val['icon'] = 'am-text-danger am-icon-close';
                    session('error', true);
                }
            }
        }else{
            if(file_exists($path)){
                if(!is_writable($path)){
                    $val['title'] = '不可写';
                    $val['icon'] = 'am-text-danger am-icon-close';
                    session('error', true);
                }
            }else{
                if(!is_writable(dirname($path))){
                    $val['title'] = '不存在';
                    $val['icon'] = 'am-text-danger am-icon-close';
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
            'icon'    => 'am-text-success am-icon-check',
        ],
        [
            'type'    => 'ext',
            'name'    => 'mongoDB',
            'title'   => '支持',
            'current' =>  extension_loaded('mongo'),
            'icon'    => 'am-text-success am-icon-check',
        ],
        [
            'type'    => 'ext',
            'name'    => 'Redis',
            'title'   => '支持',
            'current' =>  extension_loaded('redis'),
            'icon'    => 'am-text-success am-icon-check',
        ],
        [
            'type'    => 'func',
            'name'    => 'file_get_contents',
            'title'   => '支持',
            'icon'    => 'am-text-success am-icon-check',
        ],
        [
            'type'    => 'func',
            'name'    => 'mb_strlen',
            'title'   => '支持',
            'icon'    => 'am-text-success am-icon-check',
        ],
        [
            'type'    => 'func',
            'name'    => 'shell_exec',
            'title'   => '支持',
            'icon'    => 'am-text-success am-icon-check',
        ],
        [
            'type'    => 'com',
            'name'    => 'mongodump',
            'title'   => '支持',
            'icon'    => 'am-text-success am-icon-check',
        ],
        [
            'type'    => 'com',
            'name'    => 'mongorestore',
            'title'   => '支持',
            'icon'    => 'am-text-success am-icon-check',
        ]
    ];
    foreach($items as &$val){
        switch($val['type']){
            case 'ext':
                if(!$val['current']){
                    $val['title'] = '不支持';
                    $val['icon'] = 'am-text-danger am-icon-close';
                    session('error', true);
                }
                break;
            case 'func':
                if(!function_exists($val['name'])){
                    $val['title'] = '不支持';
                    $val['icon'] = 'am-text-danger am-icon-close';
                    session('error', true);
                }
                break;
            case 'com':
                $com = 'which '.$val['name'];
                if(shell_exec($com) == null){
                    $val['title'] = '不支持';
                    $val['icon'] = 'am-text-danger am-icon-close';
                    session('error', true);
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
            show_msg('配置文件'.$type.'写入成功', 'am-text-success');
        }else{
            show_msg('配置文件'.$type.'写入失败！', 'am-text-danger');
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
