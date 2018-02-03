<?php
/**
 * 统一支持跨域
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\behavior;


use think\Config;
use think\Response;

class BuildResponse {

    /**
     * 返回参数过滤（主要是将返回参数的数据类型给规范）
     * @param $response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function run(Response $response) {
        $header = Config::get('apiAdmin.CROSS_DOMAIN');
        $response->header($header);
    }

}
