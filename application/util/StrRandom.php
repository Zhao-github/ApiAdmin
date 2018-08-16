<?php
/**
 * 构建各类有意义的随机数
 * @since   2018-08-07
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


class StrRandom {

    /**
     * 构建一个随机浮点数
     * @param int $min 整数部分的最小值，默认值为-999999999
     * @param int $max 整数部分的最大值，默认值为999999999
     * @param int $dmin 小数部分位数的最小值，默认值为 0
     * @param int $dmax 小数部分位数的最大值，默认值为 8
     * @return float
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function randomFloat($min = -999999999, $max = 999999999, $dmin = 0, $dmax = 8) {
        if ($max <= $min || $dmax <= $dmin) {
            return 0.0;
        }

        $rand = '';
        $intNum = mt_rand($min, $max);
        $floatLength = mt_rand($dmin, $dmax);
        if ($floatLength > 1) {
            $rand = Strs::randString($floatLength - 1, 1);
        }
        $floatEnd = mt_rand(1, 9);

        return floatval($intNum . '.' . $rand . $floatEnd);
    }

    /**
     * 构建随机IP地址
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function randomIp() {
        $ipLong = [
            ['607649792', '608174079'], // 36.56.0.0-36.63.255.255
            ['1038614528', '1039007743'], // 61.232.0.0-61.237.255.255
            ['1783627776', '1784676351'], // 106.80.0.0-106.95.255.255
            ['2035023872', '2035154943'], // 121.76.0.0-121.77.255.255
            ['2078801920', '2079064063'], // 123.232.0.0-123.235.255.255
            ['-1950089216', '-1948778497'], // 139.196.0.0-139.215.255.255
            ['-1425539072', '-1425014785'], // 171.8.0.0-171.15.255.255
            ['-1236271104', '-1235419137'], // 182.80.0.0-182.92.255.255
            ['-770113536', '-768606209'], // 210.25.0.0-210.47.255.255
            ['-569376768', '-564133889'], // 222.16.0.0-222.95.255.255
        ];
        $randKey = mt_rand(0, 9);

        return $ip = long2ip(mt_rand($ipLong[$randKey][0], $ipLong[$randKey][1]));
    }

}
