<?php
declare (strict_types=1);
/**
 * 由ApiAdmin自动构建，请处理{$MODEL_NAME}！
 * @author apiadmin <apiadmin.org>
 */

namespace app\controller\{$MODULE};

use app\model\{$MODEL_NAME};
use app\util\ReturnCode;
use think\Response;

class {$NAME} extends Base {
    /**
     * 获取
     * @return \think\Response
     * @throws \think\db\exception\DbException
     * @author apiadmin <apiadmin.org>
     */
    public function index(): Response {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);

        $obj = new {$MODEL_NAME}();
        $listObj = $obj->paginate(['page' => $start, 'list_rows' => $limit])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 添加
     * @return Response
     * @author apiadmin <apiadmin.org>
     */
    public function add(): Response {
        $postData = $this->request->post();
        $res = {$MODEL_NAME}::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 编辑
     * @return Response
     * @author apiadmin <apiadmin.org>
     */
    public function edit(): Response {
        $postData = $this->request->post();
        $res = {$MODEL_NAME}::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 删除
     * @return Response
     * @author apiadmin <apiadmin.org>
     */
    public function del(): Response {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }

        // 请处理部分删除数据
        {$MODEL_NAME}::destroy(['id' => $id]);

        return $this->buildSuccess();
    }
}
