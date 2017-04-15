<?php
/**
 * Api身份秘钥计算
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG;


class Crypt {

    /**
     * 根据AppSecret和数据生成相对应的身份认证秘钥
     * @param $appSecret
     * @param $data
     * @return string
     */
    public function getAuthToken( $appSecret, $data ){
        if(empty($data)){
            return '';
        }else{
            $preArr = array_merge($data, array('app_secret' => $appSecret));
            ksort($preArr);
            $preStr = http_build_query($preArr);
            return md5($preStr);
        }
    }

    /**
     * 计算出唯一的身份令牌
     * @param $appId
     * @param $appSecret
     * @return string
     */
    public function getAccessToken( $appId, $appSecret ){
        $preStr = $appSecret.$appId.time().Str::keyGen();
        return md5($preStr);
    }

}