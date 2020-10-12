<?php
declare (strict_types=1);
/**
 * 后台操作日志管理
 * @since   2018-02-06
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\admin;

use app\model\AdminUserAction;
use app\util\ReturnCode;
use think\Response;

class Log extends Base {

    /**
     * 获取操作日志列表
     * @return Response
     * @throws \think\db\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index(): Response  {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $type = $this->request->get('type', '');
        $keywords = $this->request->get('keywords', '');

        $obj = new AdminUserAction();
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->whereLike('url', "%{$keywords}%");
                    break;
                case 2:
                    $obj = $obj->whereLike('nickname', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->where('uid', $keywords);
                    break;
            }
        }
        $listObj = $obj->order('add_time', 'DESC')->paginate(['page' => $start, 'list_rows' => $limit])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 删除日志
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del(): Response  {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        AdminUserAction::destroy($id);

        return $this->buildSuccess();
    }
}
