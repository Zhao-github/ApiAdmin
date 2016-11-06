<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * CURL post数据
 * @param $url
 * @param $data
 * @param array $urlParam
 * @param array $header
 * @return mixed
 */
function curlPost( $url, $data, $urlParam = [], $header = [] ){
    $ch = curl_init();
    if( !empty($urlParam) ){
        $url = $url.'?'.http_build_query($urlParam);
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    if( !empty($header) ){
        $headerStrArr = [];
        foreach ($header as $key => $value){
            $headerStrArr[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerStrArr);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}