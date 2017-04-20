<?php
/**
 * 极光推送Manager
 * @since   2017/03/24 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK;


use Home\ApiStore\ApiSDK\JPush\Device;
use Home\ApiStore\ApiSDK\JPush\Push;
use Home\ApiStore\ApiSDK\JPush\Report;
use Home\ApiStore\ApiSDK\JPush\Schedule;

class JPushSDK {

    public static function push() {
        return new Push();
    }

    public static function report() {
        return new Report();
    }

    public static function device() {
        return new Device();
    }

    public static function schedule() {
        return new Schedule();
    }
}