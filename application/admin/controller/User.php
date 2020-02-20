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
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $type = $this->request->get('type', '', 'intval');
        $keywords = $this->request->get('keywords', '');
        $status = $this->request->get('status', '');

        $obj = new AdminUser();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->whereLike('username', "%{$keywords}%");
                    break;
                case 2:
                    $obj = $obj->whereLike('nickname', "%{$keywords}%");
                    break;
            }
        }

        $listObj = $obj->order('create_time', 'DESC')
            ->paginate($limit, false, ['page' => $start])->each(function($item, $key){
                $item->userData;
            })->toArray();
        $listInfo = $listObj['data'];
        $idArr = array_column($listInfo, 'id');

        $userGroup = AdminAuthGroupAccess::all(function($query) use ($idArr) {
            $query->whereIn('uid', $idArr);
        });
        $userGroup = Tools::buildArrFromObj($userGroup);
        $userGroup = Tools::buildArrByNewKey($userGroup, 'uid');


        foreach ($listInfo as $key => &$value) {
            if ($value['userData']) {
                $value['userData']['last_login_ip'] = long2ip($value['userData']['last_login_ip']);
                $value['userData']['last_login_time'] = date('Y-m-d H:i:s', $value['userData']['last_login_time']);
                $value['create_ip'] = long2ip($value['create_ip']);
            }
            if (isset($userGroup[$value['id']])) {
                $value['group_id'] = explode(',', $userGroup[$value['id']]['group_id']);
            } else {
                $value['group_id'] = [];
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
        $postData['create_ip'] = request()->ip(1);
        $postData['password'] = Tools::userMd5($postData['password']);
        if (isset($postData['group_id']) && $postData['group_id']) {
            $groups = trim(implode(',', $postData['group_id']), ',');
            unset($postData['group_id']);
        }
        $res = AdminUser::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        AdminAuthGroupAccess::create([
            'uid'      => $res->id,
            'group_id' => $groups
        ]);

        return $this->buildSuccess();
    }

    /**
     * 获取当前组的全部用户
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getUsers() {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $page = $this->request->get('page', 1);
        $gid = $this->request->get('gid', 0);
        if (!$gid) {
            return $this->buildFailed(ReturnCode::PARAM_INVALID, '非法操作');
        }

        $totalNum = (new AdminAuthGroupAccess())->where('find_in_set("' . $gid . '", `group_id`)')->count();
        $start = $limit * ($page - 1);
        $sql = "SELECT au.* FROM admin_user as au LEFT JOIN admin_auth_group_access as aaga " .
            " ON aaga.`uid` = au.`id` WHERE find_in_set('{$gid}', aaga.`group_id`) " .
            " ORDER BY au.create_time DESC LIMIT {$start}, {$limit}";
        $userInfo = Db::query($sql);

        $uidArr = array_column($userInfo, 'id');
        $userData = (new AdminUserData())->whereIn('uid', $uidArr)->select();
        $userData = Tools::buildArrByNewKey($userData, 'uid');

        foreach ($userInfo as $key => $value) {
            if (isset($userData[$value['id']])) {
                $userInfo[$key]['last_login_ip'] = long2ip($userData[$value['id']]['last_login_ip']);
                $userInfo[$key]['login_times'] = $userData[$value['id']]['login_times'];
                $userInfo[$key]['last_login_time'] = date('Y-m-d H:i:s', $userData[$value['id']]['last_login_time']);
            }
            $userInfo[$key]['create_ip'] = long2ip($userInfo[$key]['create_ip']);
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
            'id'     => $id,
            'status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        if($oldAdmin = cache('Login:' . $id)) {
            cache('Login:' . $oldAdmin, null);
        }

        return $this->buildSuccess();
    }

    /**
     * 编辑用户
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $groups = '';
        $postData = $this->request->post();
        if ($postData['password'] === 'ApiAdmin') {
            unset($postData['password']);
        } else {
            $postData['password'] = Tools::userMd5($postData['password']);
        }
        if (isset($postData['group_id']) && $postData['group_id']) {
            $groups = trim(implode(',', $postData['group_id']), ',');
            unset($postData['group_id']);
        }
        $res = AdminUser::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $has = AdminAuthGroupAccess::get(['uid' => $postData['id']]);
        if ($has) {
            AdminAuthGroupAccess::update([
                'group_id' => $groups
            ], [
                'uid' => $postData['id'],
            ]);
        } else {
            AdminAuthGroupAccess::create([
                'uid'      => $postData['id'],
                'group_id' => $groups
            ]);
        }
        if($oldAdmin = cache('Login:' . $postData['id'])) {
            cache('Login:' . $oldAdmin, null);
        }

        return $this->buildSuccess();
    }

    /**
     * 修改自己的信息
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function own() {
        $postData = $this->request->post();
        $headImg = $postData['head_img'];

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
        unset($postData['head_img']);
        $res = AdminUser::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $userData = AdminUserData::get(['uid' => $postData['id']]);
        $userData->head_img = $headImg;
        $userData->save();
        if($oldWiki = cache('WikiLogin:' . $postData['id'])) {
            cache('WikiLogin:' . $oldWiki, null);
        }

        return $this->buildSuccess();
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

        $isAdmin = Tools::isAdministrator($id);
        if ($isAdmin) {
            return $this->buildFailed(ReturnCode::INVALID, '超级管理员不能被删除');
        }
        AdminUser::destroy($id);
        AdminAuthGroupAccess::destroy(['uid' => $id]);
        if($oldAdmin = cache('Login:' . $id)) {
            cache('Login:' . $oldAdmin, null);
        }

        return $this->buildSuccess();
    }
}
