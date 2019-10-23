<?php
/**
 * @since   2019-08-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\wiki\controller;

use app\model\AdminApp;
use app\model\AdminFields;
use app\model\AdminGroup;
use app\model\AdminList;
use app\util\DataType;
use app\util\ReturnCode;
use app\util\Tools;

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

        return $this->buildSuccess([
            'data' => $result,
            'co'   => config('apiadmin.APP_NAME') . ' ' . config('apiadmin.APP_VERSION')
        ]);
    }

    public function login() {
        $appId = $this->request->post('username');
        $appSecret = $this->request->post('password');

        $appInfo = AdminApp::get(['app_id' => $appId, 'app_secret' => $appSecret]);
        if (!empty($appInfo)) {
            if ($appInfo->app_status) {
                //保存用户信息和登录凭证
                $appInfo = $appInfo->toArray();

                $apiAuth = md5(uniqid() . time());
                cache('WikiLogin:' . $apiAuth, $appInfo, config('apiadmin.ONLINE_TIME'));
                cache('WikiLogin:' . $appInfo['id'], $apiAuth, config('apiadmin.ONLINE_TIME'));
                $appInfo['apiAuth'] = $apiAuth;

                return $this->buildSuccess($appInfo, '登录成功');
            } else {
                return $this->buildFailed(ReturnCode::LOGIN_ERROR, '当前应用已被封禁，请联系管理员');
            }
        } else {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, 'AppId或AppSecret错误');
        }
    }

    public function groupList() {
        $groupInfo = AdminGroup::all();
        $apiInfo = AdminList::all();

        $listInfo = [];
        if ($this->appInfo['app_id'] === -1) {
            $_apiInfo = [];
            foreach ($apiInfo as $aVal) {
                $_apiInfo[$aVal['group_hash']][] = $aVal;
            }
            foreach ($groupInfo as $gVal ) {
                if (isset($_apiInfo[$gVal['hash']])) {
                    $gVal['api_info'] = $_apiInfo[$gVal['hash']];
                }
                $listInfo[] = $gVal;
            }
        } else {
            $apiInfo = Tools::buildArrFromObj($apiInfo, 'hash');
            $groupInfo = Tools::buildArrFromObj($groupInfo, 'hash');
            $app_api_show = json_decode($this->appInfo['app_api_show'], true);
            foreach ($app_api_show as $key => $item) {
                $_listInfo = $groupInfo[$key];
                foreach ($item as $apiItem) {
                    $_listInfo['api_info'][] = $apiInfo[$apiItem];
                }
                if (isset($_listInfo['api_info'])) {
                    $listInfo[] = $_listInfo;
                }
            }
        }

        return $this->buildSuccess([
            'data' => $listInfo,
            'co'   => config('apiadmin.APP_NAME') . ' ' . config('apiadmin.APP_VERSION')
        ]);
    }

    public function detail() {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::NOT_EXISTS, '缺少必要参数');
        }

        $apiList = (new AdminList())->whereIn('hash', $hash)->find();
        if (!$apiList) {
            return $this->buildFailed(ReturnCode::NOT_EXISTS, '接口hash非法');
        }

        $request = AdminFields::all(['hash' => $hash, 'type' => 0]);
        $response = AdminFields::all(['hash' => $hash, 'type' => 1]);
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

        $groupInfo = AdminGroup::get(['hash' => $apiList['group_hash']]);
        $groupInfo->hot = $groupInfo->hot + 1;
        $groupInfo->save();

        return $this->buildSuccess([
            'request'  => $request,
            'response' => $response,
            'dataType' => $dataType,
            'apiList'  => $apiList,
            'url'      => $this->request->domain() . '/api/' . $hash,
            'co'       => config('apiadmin.APP_NAME') . ' ' . config('apiadmin.APP_VERSION')
        ]);
    }

    public function logout() {
        $ApiAuth = $this->request->header('ApiAuth');
        cache('WikiLogin:' . $ApiAuth, null);
        cache('WikiLogin:' . $this->appInfo['id'], null);

        $oldAdmin = cache('Login:' . $ApiAuth);
        if ($oldAdmin) {
            $oldAdmin = json_decode($oldAdmin, true);
            cache('Login:' . $ApiAuth, null);
            cache('Login:' . $oldAdmin['id'], null);
        }

        return $this->buildSuccess([], '登出成功');
    }
}
