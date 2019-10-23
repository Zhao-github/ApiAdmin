<?php
/**
 * 工程基类
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminUser;
use app\model\AdminUserData;
use app\util\ReturnCode;
use think\Controller;

class Base extends Controller {

    private $debug = [];
    protected $userInfo;

    public function __construct() {
        parent::__construct();
        $this->userInfo = $this->request->API_ADMIN_USER_INFO;
    }

    public function buildSuccess($data = [], $msg = '操作成功', $code = ReturnCode::SUCCESS) {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if (config('app.app_debug') && $this->debug) {
            $return['debug'] = $this->debug;
        }

        return $return;
    }

    /**
     * 更新用户信息
     * @param $data
     * @param bool $isDetail
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function updateUserInfo($data, $isDetail = false) {
        $apiAuth = $this->request->header('apiAuth');
        if ($isDetail) {
            AdminUserData::update($data, ['uid' => $this->userInfo['id']]);
            $this->userInfo['userData'] = AdminUserData::get(['uid' => $this->userInfo['id']]);
        } else {
            AdminUser::update($data, ['id' => $this->userInfo['id']]);
            $detail = $this->userInfo['userData'];
            $this->userInfo = AdminUser::get(['id' => $this->userInfo['id']]);
            $this->userInfo['userData'] = $detail;
        }

        cache('Login:' . $apiAuth, json_encode($this->userInfo), config('apiadmin.ONLINE_TIME'));
    }

    public function buildFailed($code, $msg = '操作失败', $data = []) {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if (config('app.app_debug') && $this->debug) {
            $return['debug'] = $this->debug;
        }

        return $return;
    }

    protected function debug($data) {
        if ($data) {
            $this->debug[] = $data;
        }
    }
}
