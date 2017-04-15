<?php
/**
 *
 * @since   2017/03/06 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


use Home\ORG\Str;

class AppController extends BaseController {

    public function index(){
        $list = D('ApiApp')->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function edit(){
        if( IS_GET ){
            $id = I('get.id');
            if( $id ){
                $detail = D('ApiApp')->where(array('id' => $id))->find();
                $this->assign('detail', $detail);
                $this->display('add');
            }else{
                $this->redirect('add');
            }
        }elseif( IS_POST ){
            $data = I('post.');
            $res = D('ApiApp')->where(array('id' => $data['id']))->save($data);
            if( $res === false ){
                $this->ajaxError('操作失败');
            }else{
                $this->ajaxSuccess('添加成功');
            }
        }
    }

    public function add(){
        if( IS_POST ){
            $data = I('post.');
            $res = D('ApiApp')->add($data);
            if( $res === false ){
                $this->ajaxError('操作失败');
            }else{
                $this->ajaxSuccess('添加成功');
            }
        }else{
            $data['app_id'] = Str::randString(8, 1);
            $data['app_secret'] = Str::randString(32);
            $this->assign('detail', $data);
            $this->display();
        }
    }

    public function open(){
        if( IS_POST ){
            $id = I('post.id');
            if( $id ){
                D('ApiApp')->open(array('id' => $id));
                $this->ajaxSuccess('操作成功');
            }else{
                $this->ajaxError('缺少参数');
            }
        }
    }

    public function close(){
        if( IS_POST ){
            $id = I('post.id');
            if( $id ){
                D('ApiApp')->close(array('id' => $id));
                $this->ajaxSuccess('操作成功');
            }else{
                $this->ajaxError('缺少参数');
            }
        }
    }

    public function del(){
        if( IS_POST ){
            $id = I('post.id');
            if( $id ){
                D('ApiApp')->del(array('id' => $id));
                $this->ajaxSuccess('操作成功');
            }else{
                $this->ajaxError('缺少参数');
            }
        }
    }
}