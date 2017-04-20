<?php
/**
 *
 * @since   2017/03/24 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK\JPush;


class Config {

    const APP_KEY = '884fc6fb9fb466e11def006a';
    const MASTER_SECRET = '11c72c7fad4110472a688973';

    //推送API
    const API_PUSH = 'https://api.jpush.cn/v3/push';
    //推送校验 API
    const API_VALIDATE = 'https://api.jpush.cn/v3/push/validate';
    //送达统计
    const API_RECEIVED = 'https://report.jpush.cn/v3/received';
    //消息统计
    const API_MESSAGE = 'https://report.jpush.cn/v3/messages';
    //用户统计
    const API_USERS = 'https://report.jpush.cn/v3/users';
    //查询设备的别名与标签(设置设备的别名与标签)
    const API_DEVICES = 'https://device.jpush.cn/v3/devices';
    //查询别名(删除别名)
    const API_ALIASES = 'https://device.jpush.cn/v3/aliases';
    //查询标签列表(判断设备与标签绑定关系/更新标签/删除标签)
    const API_TAG = 'https://device.jpush.cn/v3/tags';
    //获取用户在线状态
    const API_STATUS = 'https://device.jpush.cn/v3/devices/status/';
    //定时任务相关
    const API_SCHEDULES = 'https://api.jpush.cn/v3/schedules';

    const HTTP_POST = 1;
    const HTTP_GET = 2;
    const HTTP_PUT = 3;
    const HTTP_DELETE = 4;

    const PLATFORM_ANDROID = 'android';
    const PLATFORM_IOS = 'ios';

    const DISABLE_SOUND = "_disable_Sound";
    const DISABLE_BADGE = 0x10000;
    const USER_AGENT = 'JPush-API-PHP-Client';
    const CONNECT_TIMEOUT = 1;
    //请求最长耗时
    const READ_TIMEOUT = 120;
    //重连次数
    const DEFAULT_MAX_RETRY_TIMES = 3;
    const DEFAULT_LOG_FILE = "./jpush.log";

}