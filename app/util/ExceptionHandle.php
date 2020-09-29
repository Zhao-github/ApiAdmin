<?php
/**
 * 应用管理
 * @since   2019-10-02
 * @author  何秀钢 <bstdn@126.com>
 */

namespace app\util;

use Exception;
use think\exception\Handle;

/**
 * Class ExceptionHandle
 * @package app\util
 * 异常处理handle类
 * Detail see: https://www.kancloud.cn/manual/thinkphp5_1/354092
 */
class ExceptionHandle extends Handle {

    public function render(Exception $e) {
        return parent::render($e)->header(config('apiadmin.CROSS_DOMAIN'));
    }
}
