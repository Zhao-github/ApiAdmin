<?php
/**
 *
 * @since   2017/04/01 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


class FieldsInfoManageController extends BaseController {

    public function index() {
        $list = D('ApiFieldsInfo')->order('id asc')->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function edit() {
        if( IS_GET ) {
            $id = I('get.id');
            if( $id ){
                $detail = D('ApiFieldsInfo')->where(array('id' => $id))->find();
                $this->assign('detail', $detail);
                $this->display('add');
            }else{
                $this->redirect('add');
            }
        }elseif( IS_POST ) {
            $data = I('post.');
            $res = D('ApiFieldsInfo')->where(array('id' => $data['id']))->save($data);
            if( $res === false ) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        }
    }

    public function add() {
        if( IS_POST ) {
            $data = I('post.');
            $res = D('ApiFieldsInfo')->add($data);
            if( $res === false ) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $data['hash'] = uniqid();
            $this->assign('detail', $data);
            $this->display();
        }
    }

    public function del() {
        if( IS_POST ) {
            $id = I('post.id');
            if( $id ) {
                D('ApiFieldsInfo')->where(array('id' => $id))->delete();
                $this->ajaxSuccess('操作成功');
            } else {
                $this->ajaxError('缺少参数');
            }
        }
    }
}