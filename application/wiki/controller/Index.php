<?php
/**
 *
 * @since   2017/07/27 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\wiki\controller;


use app\model\ApiFields;
use app\model\ApiList;
use app\util\DataType;
use app\util\ReturnCode;

class Index extends Base {

    public function index() {
        return $this->fetch();
    }

    public function detail() {
        $gid = $this->request->route('gid');
        $hash = $this->request->route('hash');
        $newList = [];
        $apiList = ApiList::all(['groupId' => $gid]);
        foreach ($apiList as $value) {
            $newList[$value['hash']] = $value;
        }
        if ($hash) {
            $detail = $newList[$hash];
        } else {
            $detail = $apiList[0];
            $hash = $detail['hash'];
        }
        $request = ApiFields::all(['hash' => $hash, 'type' => 0]);
        $response = ApiFields::all(['hash' => $hash, 'type' => 1]);
        $dataType = array(
            DataType::TYPE_INTEGER => 'Integer',
            DataType::TYPE_STRING  => 'String',
            DataType::TYPE_BOOLEAN => 'Boolean',
            DataType::TYPE_ENUM    => 'Enum',
            DataType::TYPE_FLOAT   => 'Float',
            DataType::TYPE_FILE    => 'File',
            DataType::TYPE_ARRAY   => 'Array',
            DataType::TYPE_OBJECT  => 'Object',
            DataType::TYPE_MOBILE  => 'Mobile'
        );
        $this->assign('request', $request);
        $this->assign('response', $response);
        $this->assign('dataType', $dataType);
        $this->assign('apiList', $apiList);
        $this->assign('detail', $detail);
        $this->assign('hash', $hash);
        $this->assign('gid', $gid);

        return $this->fetch();
    }

    public function calculation() {
        return $this->fetch();
    }

    public function errorCode() {
        $codeArr = ReturnCode::getConstants();
        $errorInfo = array(
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
            ReturnCode::PARAM_INVALID        => '数据类型非法',
            ReturnCode::ACCESS_TOKEN_TIMEOUT => '身份令牌过期',
            ReturnCode::SESSION_TIMEOUT      => 'SESSION过期',
            ReturnCode::UNKNOWN              => '未知错误',
            ReturnCode::EXCEPTION            => '系统异常',
            ReturnCode::CURL_ERROR           => 'CURL操作异常'
        );
        $this->assign('errorInfo', $errorInfo);
        $this->assign('codeArr', $codeArr);

        return $this->fetch();
    }

    public function login() {
        return $this->fetch();
    }

}