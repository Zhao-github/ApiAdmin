<?php
declare (strict_types=1);
/**
 * 工程基类
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\admin;

use app\model\AdminUser;
use app\model\AdminUserData;
use app\util\ReturnCode;
use app\BaseController;
use think\App;
use think\facade\Env;
use think\Response;

class Base extends BaseController {

    private $debug = [];
    protected $userInfo;

    public function __construct(App $app) {
        parent::__construct($app);
        $this->userInfo = $this->request->API_ADMIN_USER_INFO;
    }

    /**
     * 成功的返回
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function buildSuccess(array $data = [], string $msg = '操作成功', int $code = ReturnCode::SUCCESS): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if (Env::get('APP_DEBUG') && $this->debug) {
            $return['debug'] = $this->debug;
        }

        return json($return);
    }

    /**
     * 更新用户信息
     * @param array $data
     * @param bool $isDetail
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function updateUserInfo(array $data, bool $isDetail = false): void {
        $apiAuth = $this->request->header('apiAuth');
        if ($isDetail) {
            AdminUserData::update($data, ['uid' => $this->userInfo['id']]);
            $this->userInfo['userData'] = (new AdminUserData())->where('uid', $this->userInfo['id'])->find();
        } else {
            AdminUser::update($data, ['id' => $this->userInfo['id']]);
            $detail = $this->userInfo['userData'];
            $this->userInfo = (new AdminUser())->where('id', $this->userInfo['id'])->find();
            $this->userInfo['userData'] = $detail;
        }

        cache('Login:' . $apiAuth, json_encode($this->userInfo), config('apiadmin.ONLINE_TIME'));
    }

    /**
     * 错误的返回
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function buildFailed(int $code, string $msg = '操作失败', array $data = []): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if (Env::get('APP_DEBUG') && $this->debug) {
            $return['debug'] = $this->debug;
        }

        return json($return);
    }

    /**
     * debug参数收集
     * @param $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function debug($data): void {
        if ($data) {
            $this->debug[] = $data;
        }
    }
}
