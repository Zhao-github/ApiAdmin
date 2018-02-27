<?php
/**
 * 接口管理
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\ApiFields;
use app\model\ApiList;
use app\util\ReturnCode;
use app\util\Tools;

class InterfaceList extends Base {
    /**
     * 获取接口列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index() {

        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $limit * ($this->request->get('page', 1) - 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');

        $where = [];
        if ($status === '1' || $status === '0') {
            $where['status'] = $status;
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $where['hash'] = $keywords;
                    break;
                case 2:
                    $where['info'] = ['like', "%{$keywords}%"];
                    break;
                case 3:
                    $where['apiClass'] = ['like', "%{$keywords}%"];
                    break;
            }
        }

        $listInfo = (new ApiList())->where($where)->order('id', 'DESC')->limit($start, $limit)->select();
        $count = (new ApiList())->where($where)->count();
        $listInfo = Tools::buildArrFromObj($listInfo);

        return $this->buildSuccess([
            'list'  => $listInfo,
            'count' => $count
        ]);
    }

    /**
     * 获取接口唯一标识
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function getHash() {
        $res['hash'] = uniqid();

        return $this->buildSuccess($res);
    }

    /**
     * 新增接口
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $postData = $this->request->post();
        $res = ApiList::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 接口状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = ApiList::update([
            'status' => $status
        ], [
            'id' => $id
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 编辑应用
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $postData = $this->request->post();
        $res = ApiList::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 删除接口
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        ApiList::destroy(['hash' => $hash]);
        ApiFields::destroy(['hash' => $hash]);

        return $this->buildSuccess([]);
    }

    /**
     * 刷新接口路由
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\exception\DbException
     */
    public function refresh() {
        $apiRoutePath = ROOT_PATH . 'application/apiRoute.php';
        $tplPath = ROOT_PATH . 'data/apiRoute.tpl';
        $methodArr = ['*','POST','GET'];

        $tplStr = file_get_contents($tplPath);
        $listInfo = ApiList::all(['status' => 1]);
        foreach ($listInfo as $value) {
            $tplStr .= 'Route::rule(\'api/'.$value->hash.'\',\'api/'.$value->apiClass.'\', \''.$methodArr[$value->method].'\', [\'after_behavior\' => $afterBehavior]);';
        }

        file_put_contents($apiRoutePath, $tplStr);
        return $this->buildSuccess([]);
    }
}
