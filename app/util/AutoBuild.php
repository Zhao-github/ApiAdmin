<?php
/**
 *
 * @since   2021-02-18
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


class AutoBuild {

    private $config = [
        'model'   => 0,  // 是否需要构建模型
        'control' => 1,  // 是否需要构建控制器
        'menu'    => 1,  // 是否需要构建目录
        'route'   => 1,  // 是否需要构建路由
        'name'    => '', // 唯一标识
        'module'  => 1,  // 构建类型 1：admin；2：api
        'table'   => ''  // 表名称
    ];

    public function run($config) {

    }

    private function buildControl() {

    }

    private function buildModel() {

    }

    private function createTable() {

    }

    private function buildMenu() {

    }

    private function buildRoute() {

    }
}
