<?php
/**
 * 用户管理控制器
 * @since   2016-01-21
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace Admin\Controller;

class UserController extends BaseController {

    public function index() {
        $listInfo = D('ApiUser')->select();
        $userData = D('ApiUserData')->select();
        $userData = $this->buildArrByNewKey($userData, 'uid');
        foreach ($listInfo as $key => $value) {
            if ($userData) {
                $listInfo[$key]['lastLoginIp'] = long2ip($userData[$value['id']]['lastLoginIp']);
                $listInfo[$key]['loginTimes'] = $userData[$value['id']]['loginTimes'];
                $listInfo[$key]['lastLoginTime'] = date('Y-m-d H:i:s', $userData[$value['id']]['lastLoginTime']);
            }
        }
        $this->assign('list', $listInfo);
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $has = D('ApiUser')->where(array('username' => $data['username']))->count();
            if ($has) {
                $this->ajaxError('用户名已经存在，请重设！');
            }
            $data['password'] = user_md5($data['password']);
            $data['regIp'] = get_client_ip(1);
            $data['regTime'] = time();
            $res = D('ApiUser')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $this->display();
        }
    }

    public function close() {
        $id = I('post.id');
        $isAdmin = isAdministrator($id);
        if ($isAdmin) {
            $this->ajaxError('超级管理员不可以被操作');
        }
        $res = D('ApiUser')->where(array('id' => $id))->save(array('status' => 0));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('操作成功');
        }
    }

    public function open() {
        $id = I('post.id');
        $isAdmin = isAdministrator($id);
        if ($isAdmin) {
            $this->ajaxError('超级管理员不可以被操作');
        }
        $res = D('ApiUser')->where(array('id' => $id))->save(array('status' => 1));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('操作成功');
        }
    }

    public function del() {
        $id = I('post.id');
        $isAdmin = isAdministrator($id);
        if ($isAdmin) {
            $this->ajaxError('超级管理员不可以被操作');
        }

        $res = D('ApiUser')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('操作成功');
        }
    }

}