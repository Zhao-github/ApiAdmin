<?php

namespace Admin\Controller;

/**
 * 权限管理控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class PermissionController extends BaseController {

    public function index() {
        $listInfo = D('ApiAuthGroup')->select();
        $this->assign('list', $listInfo);
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $res = D('ApiAuthGroup')->add(I('post.'));
            if ($res === false) {
                $this->ajaxError(L('_OPERATION_FAIL_'));
            } else {
                $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
            }
        } else {
            $this->display();
        }
    }

    public function close() {
        $id = I('post.id');
        if ($id == C('ADMIN_GROUP')) {
            $this->ajaxError(L('_VALID_ACCESS_'));
        }
        $res = D('ApiAuthGroup')->where(array('id' => $id))->save(array('status' => 0));
        if ($res === false) {
            $this->ajaxError(L('_OPERATION_FAIL_'));
        } else {
            $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
        }
    }

    public function open() {
        $id = I('post.id');
        $res = D('ApiAuthGroup')->where(array('id' => $id))->save(array('status' => 1));
        if ($res === false) {
            $this->ajaxError(L('_OPERATION_FAIL_'));
        } else {
            $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
        }
    }

    public function edit() {
        if (IS_GET) {
            $detail = D('ApiAuthGroup')->where(array('id' => I('get.id')))->find();
            $this->assign('detail', $detail);
            $this->display('add');
        } elseif (IS_POST) {
            $res = D('ApiAuthGroup')->where(array('id' => I('post.id')))->save(I('post.'));
            if ($res === false) {
                $this->ajaxError(L('_OPERATION_FAIL_'));
            } else {
                $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
            }
        } else {
            $this->ajaxError(L('_ERROR_ACTION_'));
        }
    }

    public function del() {
        $id = I('post.id');
        if ($id == C('ADMIN_GROUP')) {
            $this->error(L('_VALID_ACCESS_'));
        }
        $res = D('ApiAuthGroup')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError(L('_OPERATION_FAIL_'));
        } else {
            $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
        }
    }

    /**
     * 将管理员加入组的组列表显示
     */
    public function group() {
        if (IS_POST) {
            $data = I('post.');
            $groupAccess = array_keys($data['groupAccess']);
            $groupAccess = implode(',', $groupAccess);
            $oldArr = D('ApiAuthGroupAccess')->where(array('uid' => $data['uid']))->find();
            if ($oldArr) {
                $insert = D('ApiAuthGroupAccess')->where(array('uid' => $data['uid']))->save(array('groupId' => $groupAccess));
            } else {
                $insert = D('ApiAuthGroupAccess')->add(array('uid' => $data['uid'], 'groupId' => $groupAccess));
            }
            if ($insert) {
                $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
            } else {
                $this->ajaxError(L('_OPERATION_FAIL_'));
            }
        } elseif (IS_GET) {
            $uid = I('get.uid');
            $groupAccess = D('ApiAuthGroupAccess')->where(array('uid' => $uid))->find();
            $groupAccess = explode(',', $groupAccess['groupId']);
            $allGroup = D('ApiAuthGroup')->select();
            $this->assign('allGroup', $allGroup);
            $this->assign('groupAccess', $groupAccess);
            $this->display();
        } else {
            $this->ajaxError('非法操作');
        }
    }

    /**
     * 显示当前组下面全部的用户
     */
    public function member() {
        $groupId = I('get.group_id');
        if ($groupId) {
            $uidArr = array();
            $allGroups = D('ApiAuthGroupAccess')->select();
            foreach ($allGroups as $allGroup) {
                $gidArr = explode(',', $allGroup['groupId']);
                if (in_array($groupId, $gidArr)) {
                    $uidArr[] = $allGroup['uid'];
                }
            }
            if (!empty($uidArr)) {
                $res = D('ApiUser')->where(array('api_user.id' => array('in', $uidArr)))->join('api_user_data on api_user.id = api_user_data.uid', 'left')->select();
            } else {
                $res = array();
            }
            $this->assign('list', $res);
            $this->display();
        } else {
            $this->ajaxError('非法操作');
        }
    }

    /**
     * 删除指定组下面的指定用户
     */
    public function delMember() {
        if (IS_POST) {
            $uid = I('post.uid');
            $groupId = I('post.groupId');
            $oldInfo = D('ApiAuthGroupAccess')->where(array('uid' => $uid))->find();
            $oldGroupArr = explode(',', $oldInfo['groupId']);
            $key = array_search($groupId, $oldGroupArr);
            unset($oldGroupArr[$key]);
            $newData = implode(',', $oldGroupArr);
            $res = D('ApiAuthGroupAccess')->where(array('uid' => $uid))->save(array('groupId' => $newData));
            if ($res === false) {
                $this->ajaxError(L('_OPERATION_FAIL_'));
            } else {
                $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
            }
        } else {
            $this->ajaxError('非法操作');
        }
    }

    /**
     * 当前用户组权限节点配置
     */
    public function rule() {
        if (IS_POST) {
            $postData = I('post.');
            $needAdd = array();
            $has = D('ApiAuthRule')->where(array('groupId' => $postData['groupId']))->select();
            $hasRule = array_column($has, 'url');
            $needDel = array_flip($hasRule);
            foreach ($postData['rule'] as $key => $value) {
                if (!empty($value)) {
                    if (!in_array($value, $hasRule)) {
                        $data['url'] = $value;
                        $data['groupId'] = $postData['groupId'];
                        $needAdd[] = $data;
                    } else {
                        unset($needDel[$value]);
                    }
                }
            }
            if (count($needAdd)) {
                D('ApiAuthRule')->addAll($needAdd);
            }
            if (count($needDel)) {
                $urlArr = array_keys($needDel);
                D('ApiAuthRule')->where(array('groupId' => $postData['groupId'], 'url' => array('in', $urlArr)))->delete();
            }
            $this->ajaxSuccess('操作成功');
        } elseif (IS_GET) {
            $groupId = I('get.group_id');
            $has = D('ApiAuthRule')->where(array('groupId' => $groupId))->select();
            $hasRule = array_column($has, 'url');
            $originList = D('ApiMenu')->order('sort asc')->select();
            $list = listToTree($originList);
            $this->assign('hasRule', $hasRule);
            $this->assign('list', $list);
            $this->display();
        } else {
            $this->ajaxError('非法操作');
        }
    }

}