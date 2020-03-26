<?php
/**
 * 权限相关配置
 * @since   2018-02-06
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminAuthGroup;
use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\model\AdminMenu;
use app\util\ReturnCode;
use app\util\Tools;

class Auth extends Base {

    /**
     * 获取权限组列表
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $status = $this->request->get('status', '');

        $obj = new AdminAuthGroup();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($keywords) {
            $obj = $obj->whereLike('name', "%{$keywords}%");
        }

        $listObj = $obj->order('id', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取全部已开放的可选组
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getGroups() {
        $listInfo = (new AdminAuthGroup())->where(['status' => 1])->order('id', 'DESC')->select();
        $count = count($listInfo);
        $listInfo = Tools::buildArrFromObj($listInfo);

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
        $groupId = $this->request->get('group_id', 0);

        $list = (new AdminMenu)->order('sort', 'ASC')->select();
        $list = Tools::buildArrFromObj($list);
        $list = Tools::listToTree($list);

        $rules = [];
        if ($groupId) {
            $rules = (new AdminAuthRule())->where(['group_id' => $groupId])->select();
            $rules = Tools::buildArrFromObj($rules);
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
        $res = AdminAuthGroup::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        if ($rules) {
            $insertData = [];
            foreach ($rules as $value) {
                if ($value) {
                    $insertData[] = [
                        'group_id' => $res->id,
                        'url'      => $value
                    ];
                }
            }
            (new AdminAuthRule())->saveAll($insertData);
        }

        return $this->buildSuccess();
    }

    /**
     * 权限组状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminAuthGroup::update([
            'id'     => $id,
            'status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
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
        $res = AdminAuthGroup::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 删除组
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }

        $listInfo = (new AdminAuthGroupAccess())->where('find_in_set("' . $id . '", `group_id`)')->select();
        if ($listInfo) {
            foreach ($listInfo as $value) {
                $oldGroupArr = explode(',', $value->group_id);
                $key = array_search($id, $oldGroupArr);
                unset($oldGroupArr[$key]);
                $newData = implode(',', $oldGroupArr);
                $value->group_id = $newData;
                $value->save();
            }
        }

        AdminAuthGroup::destroy($id);
        AdminAuthRule::destroy(['group_id' => $id]);

        return $this->buildSuccess();
    }

    /**
     * 从指定组中删除指定用户
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function delMember() {
        $gid = $this->request->get('gid', 0);
        $uid = $this->request->get('uid', 0);
        if (!$gid || !$uid) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $oldInfo = AdminAuthGroupAccess::get(['uid' => $uid])->toArray();
        $oldGroupArr = explode(',', $oldInfo['group_id']);
        $key = array_search($gid, $oldGroupArr);
        unset($oldGroupArr[$key]);
        $newData = implode(',', $oldGroupArr);
        $res = AdminAuthGroupAccess::update([
            'group_id' => $newData
        ], [
            'uid' => $uid
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
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
            $newList[$key]['title'] = $value['title'];
            $newList[$key]['key'] = $value['url'];
            if (isset($value['children'])) {
                $newList[$key]['expand'] = true;
                $newList[$key]['children'] = $this->buildList($value['children'], $rules);
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
        $has = (new AdminAuthRule())->where(['group_id' => $postData['id']])->select();
        $has = Tools::buildArrFromObj($has);
        $hasRule = array_column($has, 'url');
        $needDel = array_flip($hasRule);
        foreach ($postData['rules'] as $key => $value) {
            if (!empty($value)) {
                if (!in_array($value, $hasRule)) {
                    $data['url'] = $value;
                    $data['group_id'] = $postData['id'];
                    $needAdd[] = $data;
                } else {
                    unset($needDel[$value]);
                }
            }
        }
        if (count($needAdd)) {
            (new AdminAuthRule())->saveAll($needAdd);
        }
        if (count($needDel)) {
            $urlArr = array_keys($needDel);
            (new AdminAuthRule())->whereIn('url', $urlArr)->where('group_id', $postData['id'])->delete();
        }
    }
}
