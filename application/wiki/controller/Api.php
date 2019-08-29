<?php
/**
 * @since   2019-08-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\wiki\controller;


use app\util\ReturnCode;

class Api extends Base {

    public function errorCode() {
        $codeArr = ReturnCode::getConstants();
        $codeArr = array_flip($codeArr);
        $result = [];
        $errorInfo = [
            ReturnCode::SUCCESS              => '请求成功',
            ReturnCode::INVALID              => '非法操作',
            ReturnCode::DB_SAVE_ERROR        => '数据存储失败',
            ReturnCode::DB_READ_ERROR        => '数据读取失败',
            ReturnCode::CACHE_SAVE_ERROR     => '缓存存储失败',
            ReturnCode::CACHE_READ_ERROR     => '缓存读取失败',
            ReturnCode::FILE_SAVE_ERROR      => '文件读取失败',
            ReturnCode::LOGIN_ERROR          => '登录失败',
            ReturnCode::NOT_EXISTS           => '不存在',
            ReturnCode::JSON_PARSE_FAIL      => 'JSON数据格式错误',
            ReturnCode::TYPE_ERROR           => '类型错误',
            ReturnCode::NUMBER_MATCH_ERROR   => '数字匹配失败',
            ReturnCode::EMPTY_PARAMS         => '丢失必要数据',
            ReturnCode::DATA_EXISTS          => '数据已经存在',
            ReturnCode::AUTH_ERROR           => '权限认证失败',
            ReturnCode::OTHER_LOGIN          => '别的终端登录',
            ReturnCode::VERSION_INVALID      => 'API版本非法',
            ReturnCode::CURL_ERROR           => 'CURL操作异常',
            ReturnCode::RECORD_NOT_FOUND     => '记录未找到',
            ReturnCode::DELETE_FAILED        => '删除失败',
            ReturnCode::ADD_FAILED           => '添加记录失败',
            ReturnCode::UPDATE_FAILED        => '更新记录失败',
            ReturnCode::PARAM_INVALID        => '数据类型非法',
            ReturnCode::ACCESS_TOKEN_TIMEOUT => '身份令牌过期',
            ReturnCode::SESSION_TIMEOUT      => 'SESSION过期',
            ReturnCode::UNKNOWN              => '未知错误',
            ReturnCode::EXCEPTION            => '系统异常',
        ];

        foreach ($errorInfo as $key => $value) {
            $result[] = [
                'en_code' => $codeArr[$key],
                'code'    => $key,
                'chinese' => $value,
            ];
        }

        return $this->buildSuccess($result);
    }

}
