<?php
/**
 * @since   2017-04-22
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


class ApiKeyController extends BaseController {

    public function index() {
        $this->display();
    }

    public function ajaxGetIndex() {
        $postData = I('post.');
        $start = $postData['start'] ? $postData['start'] : 0;
        $limit = $postData['length'] ? $postData['length'] : 20;
        $draw = $postData['draw'];
        $total = D('ApiStoreAuth')->count();
        $info = D('ApiStoreAuth')->limit($start, $limit)->select();
        $data = array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $info
        );
        $this->ajaxReturn($data, 'json');
    }

    public function edit(){
        if( IS_GET ){
            $id = I('get.id');
            if( $id ){
                $detail = D('ApiStoreAuth')->where(array('id' => $id))->find();
                $this->assign('detail', $detail);
                $this->display('add');
            }else{
                $this->redirect('add');
            }
        }elseif( IS_POST ){
            $data = I('post.');
            $res = D('ApiStoreAuth')->where(array('id' => $data['id']))->save($data);
            if( $res === false ){
                $this->ajaxError('操作失败');
            }else{
                $this->ajaxSuccess('操作成功');
            }
        }
    }

    public function add(){
        if( IS_POST ){
            $data = I('post.');
            $res = D('ApiStoreAuth')->add($data);
            if( $res === false ){
                $this->ajaxError('操作失败');
            }else{
                $this->ajaxSuccess('添加成功');
            }
        }else{
            $this->display();
        }
    }

    public function open(){
        if( IS_POST ){
            $id = I('post.id');
            if( $id ){
                D('ApiStoreAuth')->open(array('id' => $id));
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
                D('ApiStoreAuth')->close(array('id' => $id));
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
                D('ApiStoreAuth')->where(array('id' => $id))->delete();
                $this->ajaxSuccess('操作成功');
            }else{
                $this->ajaxError('缺少参数');
            }
        }
    }

}