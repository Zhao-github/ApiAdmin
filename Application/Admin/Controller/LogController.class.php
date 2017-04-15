<?php
/**
 * @since   2017-04-13
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


class LogController extends BaseController {
    public function index() {
        $this->display();
    }

    public function ajaxGetIndex() {
        $postData = I('post.');
        $start = $postData['start'] ? $postData['start'] : 0;
        $limit = $postData['length'] ? $postData['length'] : 20;
        $draw = $postData['draw'];
        $where = array();
        $getInfo = I('get.');
        if (isset($getInfo['type']) && !empty($getInfo['type'])) {
            if (isset($getInfo['keyword']) && !empty($getInfo['keyword'])) {
                switch ($getInfo['type']) {
                    case 1:
                        $where['url'] = array('like', '%' . $getInfo['keyword'] . '%');
                        break;
                    case 2:
                        $where['nickname'] = array('like', '%' . $getInfo['keyword'] . '%');
                        break;
                    case 3:
                        $where['uid'] = $getInfo['keyword'];
                        break;
                }
            }
        }
        $total = D('ApiUserAction')->where($where)->count();
        $info = D('ApiUserAction')->where($where)->limit($start, $limit)->select();
        $data = array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $info
        );
        $this->ajaxReturn($data, 'json');
    }

    public function del() {
        $id = I('post.id');
        $res = D('ApiUserAction')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('操作成功');
        }
    }

    public function showDetail() {
        if (IS_GET) {
            $id = I('get.id');
            $listInfo = D('ApiUserAction')->where(array('id' => $id))->find();
            $this->assign('detail', $listInfo);
            $this->display();
        }
    }
}