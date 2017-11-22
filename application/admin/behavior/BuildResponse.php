<?php
/**
 * 统一支持跨域
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\behavior;


class BuildResponse {

    /**
     * 返回参数过滤（主要是将返回参数的数据类型给规范）
     * @param $response \think\Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @throws \think\exception\DbException
     */
    public function run($response) {
        $header['Access-Control-Allow-Origin'] = '*';
        $header['Access-Control-Allow-Methods'] = 'POST,PUT,GET,DELETE';
        $header['Access-Control-Allow-Headers'] = 'Authorization, User-Agent, Keep-Alive, Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With';
        $header['Access-Control-Allow-Credentials'] = 'true';
        $response->header($header);
    }

}