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
     * 获取随机的时间
     * @param string $format PHP的时间日期格式化字符
     * @return false|string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function randomDate($format = 'Y-m-d H:i:s') {
        $timestamp = time() - mt_rand(0, 86400 * 3650);

        return date($format, $timestamp);
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

    /**
     * 随机生成一个 URL 协议
     * @return mixed
     * @author zhaoxiang <zhaoxiang051405@gmail','com>
     */
    public static function randomProtocol() {
        $proArr = [
            'http',
            'ftp',
            'gopher',
            'mailto',
            'mid',
            'cid',
            'news',
            'nntp',
            'prospero',
            'telnet',
            'rlogin',
            'tn3270',
            'wais'
        ];
        shuffle($proArr);

        return $proArr[0];
    }

    /**
     * 随机生成一个顶级域名
     * @author zhaoxiang <zhaoxiang051405@gmail','com>
     */
    public static function randomTld() {
        $tldArr = [
            'com', 'cn', 'xin', 'net', 'top', '在线',
            'xyz', 'wang', 'shop', 'site', 'club', 'cc',
            'fun', 'online', 'biz', 'red', 'link', 'ltd',
            'mobi', 'info', 'org', 'edu', 'com.cn', 'net.cn',
            'org.cn', 'gov.cn', 'name', 'vip', 'pro', 'work',
            'tv', 'co', 'kim', 'group', 'tech', 'store', 'ren',
            'ink', 'pub', 'live', 'wiki', 'design', '中文网',
            '我爱你', '中国', '网址', '网店', '公司', '网络', '集团', 'app'
        ];
        shuffle($tldArr);

        return $tldArr[0];
    }

    /**
     * 获取一个随机的域名
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function randomDomain() {
        $len = mt_rand(6, 16);

        return strtolower(Strs::randString($len)) . '.' . self::randomTld();
    }

    /**
     * 随机生成一个URL
     * @param string $protocol 协议名称，可以不用指定
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function randomUrl($protocol = '') {
        $protocol = $protocol ? $protocol : self::randomProtocol();

        return $protocol . '://' . self::randomDomain();
    }

    /**
     * 随机生成一个邮箱地址
     * @param string $domain 可以指定邮箱域名
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function randomEmail($domain = '') {
        $len = mt_rand(6, 16);
        $domain = $domain ? $domain : self::randomDomain();

        return Strs::randString($len) . '@' . $domain;

    }


    public static function randomPhone() {
        $prefixArr = [133, 153, 173, 177, 180, 181, 189, 199, 134, 135,
            136, 137, 138, 139, 150, 151, 152, 157, 158, 159, 172, 178,
            182, 183, 184, 187, 188, 198, 130, 131, 132, 155, 156, 166,
            175, 176, 185, 186, 145, 147, 149, 170, 171];
        shuffle($prefixArr);

        return $prefixArr[0] . Strs::randString(8, 1);
    }

    /**
     * 随机创建一个身份证号码
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function randomId() {
        $prefixArr = [
            11, 12, 13, 14, 15,
            21, 22, 23,
            31, 32, 33, 34, 35, 36, 37,
            41, 42, 43, 44, 45, 46,
            50, 51, 52, 53, 54,
            61, 62, 63, 64, 65,
            71, 81, 82
        ];
        shuffle($prefixArr);

        $suffixArr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'X'];
        shuffle($suffixArr);

        return $prefixArr[0] . '0000' . self::randomDate('Ymd') . Strs::randString(3, 1) . $suffixArr[0];
    }

}
