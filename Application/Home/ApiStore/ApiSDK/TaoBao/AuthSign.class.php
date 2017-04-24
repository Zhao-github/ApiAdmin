<?php
namespace Home\ApiStore\ApiSDK\TaoBao;
/**
 * 淘宝开放平台秘钥计算
 * @since   2017/04/20 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
class AuthSign {

    /**
     * 获取身份秘钥
     * @param array $params
     * @param array $appInfo
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return string
     */
    public static function getSign($params, $appInfo) {
        ksort($params);

        $stringToBeSigned = $appInfo['secretKey'];
        foreach ($params as $k => $v) {
            if (is_string($v) && "@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        $stringToBeSigned .= $appInfo['secretKey'];

        return strtoupper(md5($stringToBeSigned));
    }

}