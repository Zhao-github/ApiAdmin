<?php
/**
 *
 * @since   2017/06/23 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


class DocumentController extends BaseController {
    public function index() {
        $this->display();
    }

    public function ajaxGetIndex() {
        $postData = I('post.');
        $start = $postData['start'] ? $postData['start'] : 0;
        $limit = $postData['length'] ? $postData['length'] : 20;
        $draw = $postData['draw'];
        $total = D('ApiDocument')->count();
        $info = D('ApiDocument')->limit($start, $limit)->select();
        $data = array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $info
        );
        $this->ajaxReturn($data, 'json');
    }

    public function add() {
        if (IS_POST) {
            $data['createTime'] = NOW_TIME;
            $data['endTime'] = I('post.keep') * 3600 + NOW_TIME;
            $data['key'] = I('post.key');
            $data['info'] = I('post.info');
            D('ApiDocument')->add($data);
            $this->ajaxSuccess('添加成功');
        } else {
            $key = md5(microtime());
            $this->assign('key', $key);
            $this->display();
        }
    }

    /**
     * 启用
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function open() {
        $key = I('post.key');
        $res = D('ApiDocument')->where(array('key' => $key))->save(array('status' => 1));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            S($key, null);
            $this->ajaxSuccess('操作成功');
        }
    }

    /**
     * 禁用
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function close() {
        $key = I('post.key');
        $res = D('ApiDocument')->where(array('key' => $key))->save(array('status' => 0));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            S($key, null);
            $this->ajaxSuccess('操作成功');
        }
    }

    /**
     * 删除
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $key = I('post.key');
        $res = D('ApiDocument')->where(array('key' => $key))->delete();
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            S($key, null);
            $this->ajaxSuccess('操作成功');
        }
    }

    /**
     * Key延时
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function addTime() {
        if (IS_POST) {
            $addTime = I('post.keep') * 3600;
            $key = I('post.key');
            S($key, null);
            D('ApiDocument')->where(array('key' => $key))->save(array('endTime' => array('exp', 'endTime+' . $addTime)));
            $this->ajaxSuccess('修改成功');
        } else {
            $key = I('get.key');
            $detail = D('ApiDocument')->where(array('key' => $key))->find();
            $this->assign('key', $key);
            $this->assign('info', $detail['info']);
            $this->display();
        }
    }
}