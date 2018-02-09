<?php
/**
 * 权限相关配置
 * @since   2018-02-06
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\ApiAuthGroup;
use app\model\ApiAuthRule;
use app\model\ApiMenu;
use app\util\ReturnCode;

class Auth extends Base {

    /**
     * 获取权限组列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {

        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $limit * ($this->request->get('page', 1) - 1);
        $keywords = $this->request->get('keywords', '');
        $status = $this->request->get('status', '');

        $where['name'] = ['like', "%{$keywords}%"];
        if ($status === '1' || $status === '0') {
            $where['status'] = $status;
        }

        $listModel = (new ApiAuthGroup())->where($where)->order('id', 'DESC');
        $listInfo = $listModel->limit($start, $limit)->select();
        $count = $listModel->count();
        $listInfo = $this->buildArrFromObj($listInfo);

        return $this->buildSuccess([
            'list'  => $listInfo,
            'count' => $count
        ]);
    }

    /**
     * 获取组所在权限列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getRuleList() {
        $groupId = $this->request->get('groupId', 0);

        $list = (new ApiMenu)->where([])->order('sort', 'ASC')->select();
        $list = $this->buildArrFromObj($list);
        $list = listToTree($list);

        $rules = [];
        if ($groupId) {
            $rules = (new ApiAuthRule())->where(['groupId' => $groupId])->select();
            $rules = array_column($rules, 'url');
        }
        $newList = $this->buildList($list, $rules);

        return $this->buildSuccess([
            'list' => $newList
        ]);
    }

    /**
     * 新增组
     * @return array
     * @throws \Exception
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $rules = [];
        $postData = $this->request->post();
        if ($postData['rules']) {
            $rules = $postData['rules'];
            $rules = array_filter($rules);
        }
        unset($postData['rules']);
        $res = ApiAuthGroup::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            if ($rules) {
                $insertData = [];
                foreach ($rules as $value) {
                    if ($value) {
                        $insertData[] = [
                            'groupId' => $res->id,
                            'url'     => $value
                        ];
                    }
                }
                (new ApiAuthRule())->saveAll($insertData);
            }

            return $this->buildSuccess([]);
        }
    }

    /**
     * 权限组状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = ApiAuthGroup::update([
            'id'     => $id,
            'status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 编辑用户
     * @return array
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $postData = $this->request->post();
        if ($postData['rules']) {
            $this->editRule();
        }
        unset($postData['rules']);
        $res = ApiAuthGroup::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 删除组
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        ApiAuthGroup::destroy($id);
        ApiAuthRule::destroy(['groupId' => $id]);

        return $this->buildSuccess([]);
    }

    /**
     * 构建适用前端的权限数据
     * @param $list
     * @param $rules
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildList($list, $rules) {
        $newList = [];
        foreach ($list as $key => $value) {
            $newList[$key]['title'] = $value['name'];
            $newList[$key]['key'] = $value['url'];
            if (isset($value['_child'])) {
                $newList[$key]['expand'] = true;
                $newList[$key]['children'] = $this->buildList($value['_child'], $rules);
            } else {
                if (in_array($value['url'], $rules)) {
                    $newList[$key]['checked'] = true;
                }
            }
        }

        return $newList;
    }

    /**
     * 编辑权限细节
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function editRule() {
        $postData = $this->request->post();
        $needAdd = [];
        $has = (new ApiAuthRule())->where(['groupId' => $postData['id']])->select();
        $has = $this->buildArrFromObj($has);
        $hasRule = array_column($has, 'url');
        $needDel = array_flip($hasRule);
        foreach ($postData['rules'] as $key => $value) {
            if (!empty($value)) {
                if (!in_array($value, $hasRule)) {
                    $data['url'] = $value;
                    $data['groupId'] = $postData['id'];
                    $needAdd[] = $data;
                } else {
                    unset($needDel[$value]);
                }
            }
        }
        if (count($needAdd)) {
            (new ApiAuthRule())->saveAll($needAdd);
        }
        if (count($needDel)) {
            $urlArr = array_keys($needDel);
            ApiAuthRule::destroy([
                'groupId' => $postData['id'],
                'url'     => ['in', $urlArr]
            ]);
        }
    }

}
