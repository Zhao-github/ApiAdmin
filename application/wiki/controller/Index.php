<?php
/**
 *
 * @since   2017/07/27 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\wiki\controller;


use app\model\ApiApp;
use app\model\ApiFields;
use app\model\ApiGroup;
use app\model\ApiList;
use app\util\DataType;
use app\util\ReturnCode;
use app\util\Tools;

class Index extends Base {

    /**
     * 获取应用列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $this->checkLogin();

        $groupInfo = ApiGroup::all();
        $groupInfo = Tools::buildArrFromObj($groupInfo);
        $groupInfo = Tools::buildArrByNewKey($groupInfo, 'hash');

        $this->appInfo = ApiApp::get(['app_id' => $this->appInfo['app_id']]);
        $this->appInfo['app_api_show'] = json_decode($this->appInfo['app_api_show'], true);

        return view('', [
            'groupInfo' => $groupInfo,
            'appInfo'   => $this->appInfo
        ]);
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
        $this->checkLogin();

        return view();
    }

    public function errorCode() {
        $this->checkLogin();
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
            ReturnCode::CURL_ERROR           => 'CURL操作异常',
            ReturnCode::RECORD_NOT_FOUND     => '记录未找到',
            ReturnCode::DELETE_FAILED        => '删除失败',
            ReturnCode::ADD_FAILED           => '添加记录失败',
            ReturnCode::UPDATE_FAILED        => '添加记录失败',
            ReturnCode::PARAM_INVALID        => '数据类型非法',
            ReturnCode::ACCESS_TOKEN_TIMEOUT => '身份令牌过期',
            ReturnCode::SESSION_TIMEOUT      => 'SESSION过期',
            ReturnCode::UNKNOWN              => '未知错误',
            ReturnCode::EXCEPTION            => '系统异常',
        );

        return view('', [
            'errorInfo' => $errorInfo,
            'codeArr'   => $codeArr
        ]);
    }

    public function login() {
        return view();
    }

    /**
     * 处理wiki登录
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function doLogin() {
        $appId = $this->request->post('appId');
        $appSecret = $this->request->post('appSecret');

        $appInfo = ApiApp::get(['app_id' => $appId, 'app_secret' => $appSecret]);
        if (!empty($appInfo)) {
            if ($appInfo->app_status) {
                //保存用户信息和登录凭证
                session('app_info', json_encode($appInfo));
                $this->success('登录成功', url('/wiki/index'));
            } else {
                $this->error('当前应用已被封禁，请联系管理员');
            }
        } else {
            $this->error('AppId或AppSecret错误');
        }
    }

    public function logout() {
        session('app_info', null);
        $this->success('退出成功', url('/wiki/login'));
    }

}
