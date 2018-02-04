<?php
/**
 *
 * @since   2018-01-16
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\ApiMenu;
use app\util\ReturnCode;

class Menu extends Base {

    /**
     * 获取菜单列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $list = ApiMenu::all();
        $list = json_decode(json_encode($list), true);
        $list = formatTree(listToTree($list));

        return $this->buildSuccess([
            'list' => $list
        ], '登录成功');
    }

    /**
     * 新增菜单
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
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = ApiMenu::update([
            'id'   => $id,
            'hide' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 编辑菜单
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
