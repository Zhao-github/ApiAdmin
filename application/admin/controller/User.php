<?php
/**
 *
 * @since   2018-02-06
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\ApiUser;
use app\model\ApiUserData;
use app\util\ReturnCode;

class User extends Base {

    /**
     * 获取用户列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {

        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $limit * ($this->request->get('page', 1) - 1);

        $key = $this->request->get('key', '');
        $order = $this->request->get('order', '');

        $listModel = (new ApiUser())->where([])->order('regTime', 'DESC');
        $listInfo = $listModel->limit($start, $limit)->select();
        $count = $listModel->count();

        $listInfo = $this->buildArrFromObj($listInfo);
        $idArr = array_column($listInfo, 'id');

        $userData = ApiUserData::all(function($query) use($idArr) {
            $query->whereIn('uid', $idArr);
        });
        $userData = $this->buildArrFromObj($userData);
        $userData = $this->buildArrByNewKey($userData, 'uid');

        foreach ($listInfo as $key => $value) {
            if (isset($userData[$value['id']])) {
                $listInfo[$key]['lastLoginIp'] = long2ip($userData[$value['id']]['lastLoginIp']);
                $listInfo[$key]['loginTimes'] = $userData[$value['id']]['loginTimes'];
                $listInfo[$key]['lastLoginTime'] = date('Y-m-d H:i:s', $userData[$value['id']]['lastLoginTime']);
            }
            $listInfo[$key]['regIp'] = long2ip($listInfo[$key]['regIp']);
        }

        return $this->buildSuccess([
            'list'  => $listInfo,
            'count' => $count
        ]);
    }

    /**
     * 新增菜单
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $postData = $this->request->post();
        $res = ApiMenu::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 菜单状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = ApiUser::update([
            'id'   => $id,
            'status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 编辑菜单
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $postData = $this->request->post();
        $res = ApiMenu::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 删除菜单
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $childNum = ApiMenu::where(['fid' => $id])->count();
        if ($childNum) {
            return $this->buildFailed(ReturnCode::INVALID, '当前菜单存在子菜单,不可以被删除!');
        } else {
            ApiMenu::destroy($id);

            return $this->buildSuccess([]);
        }
    }

}
