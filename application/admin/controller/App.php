<?php
/**
 * 应用管理
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\AdminApp;
use app\model\AdminList;
use app\model\AdminGroup;
use app\util\ReturnCode;
use app\util\Strs;
use app\util\Tools;

class App extends Base {
    /**
     * 获取应用列表
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {

        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');

        $where = [];
        if ($status === '1' || $status === '0') {
            $where['app_status'] = $status;
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $where['app_id'] = $keywords;
                    break;
                case 2:
                    $where['app_name'] = ['like', "%{$keywords}%"];
                    break;
            }
        }
        $listObj = (new AdminApp())->where($where)->order('app_addTime DESC')
            ->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取AppId,AppSecret,接口列表,应用接口权限细节
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getAppInfo() {
        $apiArr = AdminList::all();
        foreach ($apiArr as $api) {
            $res['apiList'][$api['groupHash']][] = $api;
        }
        $groupArr = AdminGroup::all();
        $groupArr = Tools::buildArrFromObj($groupArr);
        $res['groupInfo'] = array_column($groupArr, 'name', 'hash');
        $id = $this->request->get('id', 0);
        if ($id) {
            $appInfo = AdminApp::get($id)->toArray();
            $res['app_detail'] = json_decode($appInfo['app_api_show'], true);
        } else {
            $res['app_id'] = mt_rand(1, 9) . Strs::randString(7, 1);
            $res['app_secret'] = Strs::randString(32);
        }

        return $this->buildSuccess($res);
    }

    /**
     * 刷新APPSecret
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function refreshAppSecret() {
        $id = $this->request->get('id', 0);
        $data['app_secret'] = Strs::randString(32);
        if ($id) {
            $res = AdminApp::update($data, ['id' => $id]);
            if ($res === false) {
                return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
            }
        }

        return $this->buildSuccess($data);
    }

    /**
     * 新增应用
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $postData = $this->request->post();
        $data = [
            'app_id'       => $postData['app_id'],
            'app_secret'   => $postData['app_secret'],
            'app_name'     => $postData['app_name'],
            'app_info'     => $postData['app_info'],
            'app_group'    => $postData['app_group'],
            'app_addTime'  => time(),
            'app_api'      => '',
            'app_api_show' => '',
        ];
        if (isset($postData['app_api']) && $postData['app_api']) {
            $appApi = [];
            $data['app_api_show'] = json_encode($postData['app_api']);
            foreach ($postData['app_api'] as $value) {
                $appApi = array_merge($appApi, $value);
            }
            $data['app_api'] = implode(',', $appApi);
        }
        $res = AdminApp::create($data);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 应用状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminApp::update([
            'app_status' => $status
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
        $data = [
            'app_name'     => $postData['app_name'],
            'app_info'     => $postData['app_info'],
            'app_group'    => $postData['app_group'],
            'app_api'      => '',
            'app_api_show' => '',
        ];
        if (isset($postData['app_api']) && $postData['app_api']) {
            $appApi = [];
            $data['app_api_show'] = json_encode($postData['app_api']);
            foreach ($postData['app_api'] as $value) {
                $appApi = array_merge($appApi, $value);
            }
            $data['app_api'] = implode(',', $appApi);
        }
        $res = AdminApp::update($data, ['id' => $postData['id']]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 删除应用
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        AdminApp::destroy($id);

        return $this->buildSuccess([]);
    }
}
