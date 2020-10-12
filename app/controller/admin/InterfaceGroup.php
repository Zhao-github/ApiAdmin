<?php
declare (strict_types=1);
/**
 * 接口组维护
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\admin;

use app\model\AdminApp;
use app\model\AdminGroup;
use app\model\AdminList;
use app\util\ReturnCode;
use think\Response;

class InterfaceGroup extends Base {

    /**
     * 获取接口组列表
     * @return Response
     * @throws \think\db\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index(): Response {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');

        $obj = new AdminGroup();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->where('hash', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('name', "%{$keywords}%");
                    break;
            }
        }
        $listObj = $obj->order('create_time', 'desc')->paginate(['page' => $start, 'list_rows' => $limit])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取全部有效的接口组
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getAll(): Response {
        $listInfo = (new AdminGroup())->where(['status' => 1])->select();

        return $this->buildSuccess([
            'list' => $listInfo
        ]);
    }

    /**
     * 接口组状态编辑
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus(): Response {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminGroup::update([
            'id'     => $id,
            'status' => $status,
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 添加接口组
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add(): Response {
        $postData = $this->request->post();
        $res = AdminGroup::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 接口组编辑
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit(): Response {
        $postData = $this->request->post();
        $res = AdminGroup::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 接口组删除
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del(): Response {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        if ($hash === 'default') {
            return $this->buildFailed(ReturnCode::INVALID, '系统预留关键数据，禁止删除！');
        }

        AdminList::update(['group_hash' => 'default'], ['group_hash' => $hash]);
        $hashRule = (new AdminApp())->whereLike('app_api_show', "%$hash%")->select();
        if ($hashRule) {
            foreach ($hashRule as $rule) {
                $appApiShowArr = json_decode($rule->app_api_show, true);
                if (!empty($appApiShowArr[$hash])) {
                    if (isset($appApiShowArr['default'])) {
                        $appApiShowArr['default'] = array_merge($appApiShowArr['default'], $appApiShowArr[$hash]);
                    } else {
                        $appApiShowArr['default'] = $appApiShowArr[$hash];
                    }
                }
                unset($appApiShowArr[$hash]);
                $rule->app_api_show = json_encode($appApiShowArr);
                $rule->save();
            }
        }

        AdminGroup::destroy(['hash' => $hash]);

        return $this->buildSuccess();
    }
}
