<?php

namespace Admin\Controller;

/**
 * 菜单管理控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class MenuController extends BaseController {

    /**
     * 获取菜单列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $list = D('ApiMenu')->order('sort asc')->select();
        $list = formatTree(listToTree($list));
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 新增菜单
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['hide'] = isset($data['hide']) ? 1 : 0;
            $res = D('ApiMenu')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $originList = D('ApiMenu')->order('sort asc')->select();
            $fid = '';
            $id = I('get.id');
            if (!empty($id)) {
                $fid = $id;
            }
            $options = array_column(formatTree(listToTree($originList)), 'showName', 'id');
            $this->assign('options', $options);
            $this->assign('fid', $fid);
            $this->display();
        }
    }

    /**
     * 显示菜单
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function open() {
        $id = I('post.id');
        $res = D('ApiMenu')->where(array('id' => $id))->save(array('hide' => 0));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 隐藏菜单
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function close() {
        $id = I('post.id');
        $res = D('ApiMenu')->where(array('id' => $id))->save(array('hide' => 1));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 编辑菜单
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        if (IS_GET) {
            $originList = D('ApiMenu')->order('sort asc')->select();
            $list = $this->buildArrByNewKey($originList);
            $listInfo = $list[I('get.id')];
            $options = array_column(formatTree(listToTree($originList)), 'showName', 'id');

            $this->assign('detail', $listInfo);
            $this->assign('options', $options);
            $this->display('add');
        } elseif (IS_POST) {
            $postData = I('post.');
            $postData['hide'] = isset($postData['hide']) ? 1 : 0;
            $res = D('ApiMenu')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }

    public function del() {
        $id = I('post.id');
        $childNum = D('ApiMenu')->where(array('fid' => $id))->count();
        if ($childNum) {
            $this->ajaxError("当前菜单存在子菜单,不可以被删除!");
        } else {
            D('ApiMenu')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('编辑成功');
        }
    }

}