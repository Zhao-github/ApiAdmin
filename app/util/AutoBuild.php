<?php
/**
 *
 * @since   2021-02-18
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


class AutoBuild {

    private $config = [
        'model'     => 0,  // 是否需要构建模型
        'control'   => 1,  // 是否需要构建控制器
        'menu'      => 1,  // 是否需要构建目录
        'route'     => 1,  // 是否需要构建路由
        'name'      => '', // 唯一标识
        'module'    => 1,  // 构建类型 1：admin；2：api
        'table'     => 0,  // 是否创建表
        'modelName' => '', // 表名称
        'fid'       => 0   // 父级ID
    ];

    private $basePath = '';

    public function run($config = []) {
        $this->config = array_merge($this->config, $config);

        if ($this->config['module'] == 1) {

        }
        if ($this->config['control'] && $this->config['name']) {
            $this->buildControl();
        }
    }

    /**
     * 驼峰命名转下划线命名
     * @param $camelCaps
     * @param string $separator
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function unCamelize($camelCaps, $separator = '_'): string {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
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
