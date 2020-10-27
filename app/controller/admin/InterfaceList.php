<?php
declare (strict_types=1);
/**
 * 接口管理
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\admin;

use app\model\AdminApp;
use app\model\AdminFields;
use app\model\AdminList;
use app\util\ReturnCode;
use think\facade\Env;
use think\Response;

class InterfaceList extends Base {

    /**
     * 获取接口列表
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

        $obj = new AdminList();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->where('hash', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('info', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->whereLike('api_class', "%{$keywords}%");
                    break;
            }
        }
        $listObj = $obj->order('id', 'DESC')->paginate(['page' => $start, 'list_rows' => $limit])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取接口唯一标识
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getHash(): Response {
        $res['hash'] = uniqid();

        return $this->buildSuccess($res);
    }

    /**
     * 新增接口
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add(): Response {
        $postData = $this->request->post();
        if (!preg_match("/^[A-Za-z0-9_\/]+$/", $postData['api_class'])) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '真实类名只允许填写字母，数字和/');
        }

        $res = AdminList::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 接口状态编辑
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus(): Response {
        $hash = $this->request->get('hash');
        $status = $this->request->get('status');
        $res = AdminList::update([
            'status' => $status
        ], [
            'hash' => $hash
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        cache('ApiInfo:' . $hash, null);

        return $this->buildSuccess();
    }

    /**
     * 编辑接口
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit(): Response {
        $postData = $this->request->post();
        if (!preg_match("/^[A-Za-z0-9_\/]+$/", $postData['api_class'])) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '真实类名只允许填写字母，数字和/');
        }

        $res = AdminList::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        cache('ApiInfo:' . $postData['hash'], null);

        return $this->buildSuccess();
    }

    /**
     * 删除接口
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

        $hashRule = (new AdminApp())->whereLike('app_api', "%$hash%")->select();
        if ($hashRule) {
            $oldInfo = (new AdminList())->where('hash', $hash)->find();
            foreach ($hashRule as $rule) {
                $appApiArr = explode(',', $rule->app_api);
                $appApiIndex = array_search($hash, $appApiArr);
                array_splice($appApiArr, $appApiIndex, 1);
                $rule->app_api = implode(',', $appApiArr);

                $appApiShowArrOld = json_decode($rule->app_api_show, true);
                $appApiShowArr = $appApiShowArrOld[$oldInfo->groupHash];
                $appApiShowIndex = array_search($hash, $appApiShowArr);
                array_splice($appApiShowArr, $appApiShowIndex, 1);
                $appApiShowArrOld[$oldInfo->groupHash] = $appApiShowArr;
                $rule->app_api_show = json_encode($appApiShowArrOld);

                $rule->save();
            }
        }

        AdminList::destroy(['hash' => $hash]);
        AdminFields::destroy(['hash' => $hash]);

        cache('ApiInfo:' . $hash, null);

        return $this->buildSuccess();
    }

    /**
     * 刷新接口路由
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function refresh(): Response {
        $rootPath = root_path();
        $apiRoutePath = $rootPath . 'route/apiRoute.php';
        $tplPath = $rootPath . 'install/apiRoute.tpl';
        $methodArr = ['*', 'POST', 'GET'];

        $tplOriginStr = file_get_contents($tplPath);
        $listInfo = (new AdminList())->where('status', 1)->select();
        $tplStr = [];
        foreach ($listInfo as $value) {
            if ($value['hash_type'] === 1) {
                array_push($tplStr, 'Route::rule(\'' . addslashes($value->api_class) . '\',\'api.' . addslashes($value->api_class) . '\', \'' . $methodArr[$value->method] . '\')->middleware([app\middleware\ApiAuth::class, app\middleware\ApiPermission::class, app\middleware\RequestFilter::class, app\middleware\ApiLog::class]);');
            } else {
                array_push($tplStr, 'Route::rule(\'' . addslashes($value->hash) . '\',\'api.' . addslashes($value->api_class) . '\', \'' . $methodArr[$value->method] . '\')->middleware([app\middleware\ApiAuth::class, app\middleware\ApiPermission::class, app\middleware\RequestFilter::class, app\middleware\ApiLog::class]);');
            }
        }
        $tplOriginStr = str_replace(['{$API_RULE}'], [implode(PHP_EOL . '    ', $tplStr)], $tplOriginStr);

        file_put_contents($apiRoutePath, $tplOriginStr);

        return $this->buildSuccess();
    }
}
