<?php
/**
 * 极光推送Manager
 * @since   2017/03/24 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG;


use Home\ORG\JPush\Device;
use Home\ORG\JPush\Push;
use Home\ORG\JPush\Report;
use Home\ORG\JPush\Schedule;

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