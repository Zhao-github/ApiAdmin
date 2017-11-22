<?php
/**
 * 处理app_id接入接口权限
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\api\behavior;


use app\model\ApiFields;
use app\util\ApiLog;
use app\util\ReturnCode;
use app\util\DataType;
use think\Request;
use think\Validate;

class ApiPermission {

    /**
     * 默认行为函数
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return \think\Request
     * @throws \think\exception\DbException
     */
    public function run() {

    }


}
