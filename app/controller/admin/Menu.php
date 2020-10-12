<?php
declare (strict_types=1);
/**
 * 目录管理
 * @since   2018-01-16
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\admin;

use app\model\AdminMenu;
use app\util\ReturnCode;
use app\util\Tools;
use think\Response;

class Menu extends Base {

    /**
     * 获取菜单列表
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index(): Response {
        $keywords = $this->request->get('keywords', '');
        $obj = new AdminMenu();
        if ($keywords) {
            $obj = $obj->whereLike('title', "%{$keywords}%");
        }
        $obj = $obj->order('sort', 'ASC')->select();
        $list = Tools::buildArrFromObj($obj);
        if (!$keywords) {
            $list = Tools::listToTree($list);
        }

        return $this->buildSuccess([
            'list' => $list
        ]);
    }

    /**
     * 新增菜单
     * @return \think\Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add(): Response {
        $postData = $this->request->post();
        if ($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $res = AdminMenu::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        } else {
            return $this->buildSuccess();
        }
    }

    /**
     * 菜单状态编辑
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus(): Response {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminMenu::update([
            'id'   => $id,
            'show' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 编辑菜单
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit(): Response {
        $postData = $this->request->post();
        if ($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $res = AdminMenu::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 删除菜单
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del(): Response {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        (new AdminMenu())->whereIn('id', $id)->delete();

        return $this->buildSuccess();
    }
}
