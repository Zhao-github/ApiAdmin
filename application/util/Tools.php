<?php
/**
 *
 * @since   2017-11-01
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


class Tools {

    public static function getDate($timestamp) {
        $now = time();
        $diff = $now - $timestamp;
        if ($diff <= 60) {
            return $diff . '秒前';
        } elseif ($diff <= 3600) {
            return floor($diff / 60) . '分钟前';
        } elseif ($diff <= 86400) {
            return floor($diff / 3600) . '小时前';
        } elseif ($diff <= 2592000) {
            return floor($diff / 86400) . '天前';
        } else {
            return '一个月前';
        }
    }

    public static function userMd5($str, $auth_key = '') {
        if (!$auth_key) {
            $auth_key = config('apiAdmin.AUTH_KEY');
        }

        return '' === $str ? '' : md5(sha1($str) . $auth_key);
    }

}