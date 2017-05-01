<?php
/**
 *
 * @since   2017/04/21 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


class ApiStoreController extends BaseController {
    public function index() {
        $keyArr = D('ApiStoreAuth')->select();
        $list = array_column($keyArr, 'name', 'id');
        $list[0] = '暂不绑定';
        $this->assign('list', $list);
        $this->display();
    }

    public function ajaxGetIndex() {
        $postData = I('post.');
        $start = $postData['start'] ? $postData['start'] : 0;
        $limit = $postData['length'] ? $postData['length'] : 20;
        $draw = $postData['draw'];
        $total = D('ApiStore')->count();
        $info = D('ApiStore')->limit($start, $limit)->select();
        $data = array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $info
        );
        $this->ajaxReturn($data, 'json');
    }

    public function edit() {
        if (IS_GET) {
            $id = I('get.id');
            if ($id) {
                $detail = D('ApiStore')->where(array('id' => $id))->find();
                $this->assign('detail', $detail);
                $keyArr = D('ApiStoreAuth')->select();
                $list = array_column($keyArr, 'name', 'id');
                $list[0] = '暂不绑定';
                $this->assign('list', $list);
                $this->display('add');
            } else {
                $this->redirect('add');
            }
        } elseif (IS_POST) {
            $data = I('post.');
            $res = D('ApiStore')->where(array('id' => $data['id']))->save($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('操作成功');
            }
        }
    }

    public function refresh() {
        $apiPath = dirname(THINK_PATH) . '/Application/Home/ApiStore/';
        $dir = opendir($apiPath);
        if ($dir) {
            $preData = array();
            while (($file = readdir($dir)) !== false) {
                $filePath = $apiPath . $file;
                if (!is_dir($filePath)) {
                    $prefix = 'Home\\ApiStore\\';
                    $moduleName = str_replace('.class.php', '', $file);
                    $reflection = new \ReflectionClass($prefix . $moduleName);
                    if ($reflection->hasProperty('apiName')) {
                        $data['name'] = $reflection->getStaticPropertyValue('apiName');
                    } else {
                        $data['name'] = '未定义';
                    }
                    $data['path'] = $prefix . $moduleName;
                    $preDataPath[] = $prefix . $moduleName;
                    $preData[] = $data;
                }
            }
            if (!$preData) {
                D('ApiStore')->execute('Truncate Table api_store');
            } else {
                $old = D('ApiStore')->select();
                $oldPath = array_column($old, 'path');
                $addArr = array_diff($preDataPath, $oldPath);
                $delArr = array_diff($oldPath, $preDataPath);
                if ($delArr) {
                    D('ApiStore')->where(array('path' => array('in', $delArr)))->delete();
                }
                if ($addArr) {
                    $addData = array();
                    foreach ($preData as $item) {
                        if (in_array($item['path'], $addArr)) {
                            $addData[] = $item;
                        }
                    }
                    D('ApiStore')->addAll($addData);
                }
            }
        }
        $this->ajaxSuccess('操作成功');
    }

    public function open() {
        if (IS_POST) {
            $id = I('post.id');
            if ($id) {
                D('ApiStore')->open(array('id' => $id));
                $this->ajaxSuccess('操作成功');
            } else {
                $this->ajaxError('缺少参数');
            }
        }
    }

    public function close() {
        if (IS_POST) {
            $id = I('post.id');
            if ($id) {
                D('ApiStore')->close(array('id' => $id));
                $this->ajaxSuccess('操作成功');
            } else {
                $this->ajaxError('缺少参数');
            }
        }
    }
}