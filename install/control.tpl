<?php
declare (strict_types=1);
/**
 * ��ApiAdmin�Զ��������봦��{$MODEL_NAME}��
 * @author apiadmin <apiadmin.org>
 */

namespace app\controller\{$MODULE};

use app\model\{$MODEL_NAME};
use app\util\ReturnCode;
use think\Response;

class {$NAME} extends Base {
    /**
     * ��ȡ
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
     * ���
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
     * �༭
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
     * ɾ��
     * @return Response
     * @author apiadmin <apiadmin.org>
     */
    public function del(): Response {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, 'ȱ�ٱ�Ҫ����');
        }

        // �봦����ɾ������
        {$MODEL_NAME}::destroy(['id' => $id]);

        return $this->buildSuccess();
    }
}
