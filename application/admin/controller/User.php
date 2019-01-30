<?php
/**
 * 用户管理
 * @since   2018-02-06
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\AdminAuthGroupAccess;
use app\model\AdminUser;
use app\model\AdminUserData;
use app\util\ReturnCode;
use app\util\Tools;
use think\Db;

class User extends Base {

    /**
     * 获取用户列表
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {

        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $type = $this->request->get('type', '');
        $keywords = $this->request->get('keywords', '');
        $status = $this->request->get('status', '');

        $where = [];
        if ($status === '1' || $status === '0') {
            $where['status'] = $status;
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $where['username'] = ['like', "%{$keywords}%"];
                    break;
                case 2:
                    $where['nickname'] = ['like', "%{$keywords}%"];
                    break;
            }
        }

        $listObj = (new AdminUser())->where($where)->order('regTime DESC')
            ->paginate($limit, false, ['page' => $start])->toArray();
        $listInfo = $listObj['data'];
        $idArr = array_column($listInfo, 'id');

        $userData = AdminUserData::all(function($query) use ($idArr) {
            $query->whereIn('uid', $idArr);
        });
        $userData = Tools::buildArrFromObj($userData);
        $userData = Tools::buildArrByNewKey($userData, 'uid');

        $userGroup = AdminAuthGroupAccess::all(function($query) use ($idArr) {
            $query->whereIn('uid', $idArr);
        });
        $userGroup = Tools::buildArrFromObj($userGroup);
        $userGroup = Tools::buildArrByNewKey($userGroup, 'uid');

        foreach ($listInfo as $key => $value) {
            if (isset($userData[$value['id']])) {
                $listInfo[$key]['lastLoginIp'] = long2ip($userData[$value['id']]['lastLoginIp']);
                $listInfo[$key]['loginTimes'] = $userData[$value['id']]['loginTimes'];
                $listInfo[$key]['lastLoginTime'] = date('Y-m-d H:i:s', $userData[$value['id']]['lastLoginTime']);
            }
            $listInfo[$key]['regIp'] = long2ip($listInfo[$key]['regIp']);
            if (isset($userGroup[$value['id']])) {
                $listInfo[$key]['groupId'] = explode(',', $userGroup[$value['id']]['groupId']);
            } else {
                $listInfo[$key]['groupId'] = [];
            }
        }

        return $this->buildSuccess([
            'list'  => $listInfo,
            'count' => $listObj['total']
        ]);
    }

    /**
     * 新增用户
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $groups = '';
        $postData = $this->request->post();
        $postData['regIp'] = request()->ip(1);
        $postData['regTime'] = time();
        $postData['password'] = Tools::userMd5($postData['password']);
        if ($postData['groupId']) {
            $groups = trim(implode(',', $postData['groupId']), ',');
        }
        unset($postData['groupId']);
        $res = AdminUser::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            AdminAuthGroupAccess::create([
                'uid'     => $res->id,
                'groupId' => $groups
            ]);

            return $this->buildSuccess([]);
        }
    }

    /**
     * 获取当前组的全部用户
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getUsers() {
        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $page = $this->request->get('page', 1);
        $gid = $this->request->get('gid', 0);
        if (!$gid) {
            return $this->buildFailed(ReturnCode::PARAM_INVALID, '非法操作');
        }

        $totalNum = (new AdminAuthGroupAccess())->where('find_in_set("' . $gid . '", `groupId`)')->count();
        $start = $limit * ($page - 1);
        $sql = "SELECT au.* FROM admin_user as au LEFT JOIN admin_auth_group_access as aaga " .
            " ON aaga.`uid` = au.`id` WHERE find_in_set('{$gid}', aaga.`groupId`) " .
            " ORDER BY au.regTime DESC LIMIT {$start}, {$limit}";
        $userInfo = Db::query($sql);

        $uidArr = array_column($userInfo, 'id');
        $userData = (new AdminUserData())->whereIn('uid', $uidArr)->select();
        $userData = Tools::buildArrByNewKey($userData, 'uid');

        foreach ($userInfo as $key => $value) {
            if (isset($userData[$value['id']])) {
                $userInfo[$key]['lastLoginIp'] = long2ip($userData[$value['id']]['lastLoginIp']);
                $userInfo[$key]['loginTimes'] = $userData[$value['id']]['loginTimes'];
                $userInfo[$key]['lastLoginTime'] = date('Y-m-d H:i:s', $userData[$value['id']]['lastLoginTime']);
            }
            $userInfo[$key]['regIp'] = long2ip($userInfo[$key]['regIp']);
        }

        return $this->buildSuccess([
            'list'  => $userInfo,
            'count' => $totalNum
        ]);
    }

    /**
     * 用户状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminUser::update([
            'id'         => $id,
            'status'     => $status,
            'updateTime' => time()
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 编辑用户
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\exception\DbException
     */
    public function edit() {
        $groups = '';
        $postData = $this->request->post();
        if ($postData['password'] === 'ApiAdmin') {
            unset($postData['password']);
        } else {
            $postData['password'] = Tools::userMd5($postData['password']);
        }
        if ($postData['groupId']) {
            $groups = trim(implode(',', $postData['groupId']), ',');
        }
        $postData['updateTime'] = time();
        unset($postData['groupId']);
        $res = AdminUser::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            $has = AdminAuthGroupAccess::get(['uid' => $postData['id']]);
            if ($has) {
                AdminAuthGroupAccess::update([
                    'groupId' => $groups
                ], [
                    'uid' => $postData['id'],
                ]);
            } else {
                AdminAuthGroupAccess::create([
                    'uid'     => $postData['id'],
                    'groupId' => $groups
                ]);
            }

            return $this->buildSuccess([]);
        }
    }

    /**
     * 修改自己的信息
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\exception\DbException
     */
    public function own() {
        $postData = $this->request->post();
        $headImg = $postData['headImg'];
        if ($postData['password'] && $postData['oldPassword']) {
            $oldPass = Tools::userMd5($postData['oldPassword']);
            unset($postData['oldPassword']);
            if ($oldPass === $this->userInfo['password']) {
                $postData['password'] = Tools::userMd5($postData['password']);
            } else {
                return $this->buildFailed(ReturnCode::INVALID, '原始密码不正确');
            }
        } else {
            unset($postData['password']);
            unset($postData['oldPassword']);
        }
        $postData['id'] = $this->userInfo['id'];
        $postData['updateTime'] = time();
        unset($postData['headImg']);
        $res = AdminUser::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            $userData = AdminUserData::get(['uid' => $postData['id']]);
            $userData->headImg = $headImg;
            $userData->save();

            return $this->buildSuccess([]);
        }
    }

    /**
     * 删除用户
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        AdminUser::destroy($id);
        AdminAuthGroupAccess::destroy(['uid' => $id]);

        return $this->buildSuccess([]);

    }

}
