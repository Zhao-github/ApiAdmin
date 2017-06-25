<?php
/**
 *
 * @since   2017/03/09 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


use Home\ORG\DataType;

class FieldsManageController extends BaseController {

    private $dataType = array(
        DataType::TYPE_INTEGER => 'Integer',
        DataType::TYPE_STRING  => 'String',
        DataType::TYPE_BOOLEAN => 'Boolean',
        DataType::TYPE_ENUM    => 'Enum',
        DataType::TYPE_FLOAT   => 'Float',
        DataType::TYPE_FILE    => 'File',
        DataType::TYPE_MOBILE  => 'Mobile',
        DataType::TYPE_OBJECT  => 'Object',
        DataType::TYPE_ARRAY   => 'Array'
    );

    public function index() {
    }

    public function request() {
        $hash = I('get.hash');
        $where['type'] = 0;
        if (!empty($hash)) {
            $where['hash'] = $hash;
        }
        $res = D('ApiFields')->where($where)->select();
        $this->assign('dataType', $this->dataType);
        $this->assign('list', $res);
        $this->assign('type', 0);
        $this->display('index');
    }

    public function response() {
        $hash = I('get.hash');
        $where['type'] = 1;
        if (!empty($hash)) {
            $where['hash'] = $hash;
        }
        $res = D('ApiFields')->where($where)->select();
        $this->assign('dataType', $this->dataType);
        $this->assign('list', $res);
        $this->assign('type', 1);
        $this->display('index');
    }

    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['fieldName'] = $data['showName'];
            $res = D('ApiFields')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                S('ApiRequest_' . $data['hash'], null);
                S('ApiResponse_' . $data['hash'], null);
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $this->assign('dataType', $this->dataType);
            $this->display();
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $data['fieldName'] = $data['showName'];
            $res = D('ApiFields')->where(array('id' => $data['id']))->save($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                if ($data['type'] == 0) {
                    S('ApiRequest_' . $data['hash'], null);
                } else {
                    S('ApiResponse_' . $data['hash'], null);
                }
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $id = I('get.id');
            if ($id) {
                $detail = D('ApiFields')->where(array('id' => $id))->find();
                $this->assign('detail', $detail);
                $this->assign('dataType', $this->dataType);
                $this->display('add');
            }
        }
    }

    public function del() {
        if (IS_POST) {
            $id = I('post.id');
            if ($id) {
                $detail = D('ApiFields')->where(array('id' => $id))->find();
                if ($detail['type'] == 0) {
                    S('ApiRequest_' . $detail['hash'], null);
                } else {
                    S('ApiResponse_' . $detail['hash'], null);
                }
                D('ApiFields')->where(array('id' => $id))->delete();
                $this->ajaxSuccess('操作成功');
            } else {
                $this->ajaxError('缺少参数');
            }
        }
    }

    public function upload() {
        if (IS_POST) {
            $hash = I('post.hash');
            $type = I('post.type');
            $jsonStr = I('post.jsonStr');
            $jsonStr = html_entity_decode($jsonStr);
            $data = json_decode($jsonStr, true);
            D('ApiList')->where(array('hash' => $hash))->save(array('returnStr' => json_encode($data)));
            $this->handle($data['data'], $dataArr);
            $old = D('ApiFields')->where(array(
                'hash' => $hash,
                'type' => $type
            ))->select();
            $oldArr = array_column($old, 'showName');
            $newArr = array_column($dataArr, 'showName');
            $addArr = array_diff($newArr, $oldArr);
            $delArr = array_diff($oldArr, $newArr);
            if ($delArr) {
                D('ApiFields')->where(array('showName' => array('in', $delArr)))->delete();
            }
            if ($addArr) {
                $addData = array();
                foreach ($dataArr as $item) {
                    if (in_array($item['showName'], $addArr)) {
                        $addData[] = $item;
                    }
                }
                D('ApiFields')->addAll($addData);
            }
            if ($type == 0) {
                S('ApiRequest_' . $hash, null);
            } else {
                S('ApiResponse_' . $hash, null);
            }
            S('ApiReturnType_' . $hash, null);
            $this->ajaxSuccess('操作成功');
        } else {
            $this->display();
        }
    }

    private function handle($data, &$dataArr, $prefix = 'data', $index = 'data') {
        if (!$this->isAssoc($data)) {
            $addArr = array(
                'fieldName' => $index,
                'showName'  => $prefix,
                'hash'      => I('post.hash'),
                'isMust'    => 1,
                'dataType'  => DataType::TYPE_ARRAY,
                'type'      => I('post.type')
            );
            $dataArr[] = $addArr;
            $prefix .= '[]';
            if (is_array($data[0])) {
                $this->handle($data[0], $dataArr, $prefix);
            }
        } else {
            $addArr = array(
                'fieldName' => $index,
                'showName'  => $prefix,
                'hash'      => I('post.hash'),
                'isMust'    => 1,
                'dataType'  => DataType::TYPE_OBJECT,
                'type'      => I('post.type')
            );
            $dataArr[] = $addArr;
            $prefix .= '{}';
            foreach ($data as $index => $datum) {
                $myPre = $prefix . $index;
                $addArr = array(
                    'fieldName' => $index,
                    'showName'  => $myPre,
                    'hash'      => I('post.hash'),
                    'isMust'    => 1,
                    'dataType'  => DataType::TYPE_STRING,
                    'type'      => I('post.type')
                );
                if (is_numeric($datum)) {
                    if (preg_match('/^\d*$/', $datum)) {
                        $addArr['dataType'] = DataType::TYPE_INTEGER;
                    } else {
                        $addArr['dataType'] = DataType::TYPE_FLOAT;
                    }
                    $dataArr[] = $addArr;
                } elseif (is_array($datum)) {
                    $this->handle($datum, $dataArr, $myPre, $index);
                } else {
                    $addArr['dataType'] = DataType::TYPE_STRING;
                    $dataArr[] = $addArr;
                }
            }
        }
    }

    /**
     * 判断是否是关联数组（true表示是关联数组）
     * @param array $arr
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return bool
     */
    private function isAssoc(array $arr) {
        if (array() === $arr) return false;

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}