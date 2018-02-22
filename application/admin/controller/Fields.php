<?php
/**
 * 接口输入输出字段维护
 * @since   2018-02-21
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\ApiFields;
use app\util\DataType;
use app\util\ReturnCode;

class Fields extends Base {
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
        return $this->buildSuccess($this->dataType);
    }

    /**
     * 获取请求参数
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function request() {
        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $limit * ($this->request->get('page', 1) - 1);
        $hash = $this->request->get('hash', '');

        if (!empty($hash)) {
            $listInfo = (new ApiFields())->where(['hash' => $hash, 'type' => 0])->limit($start, $limit)->select();
            $count = (new ApiFields())->where(['hash' => $hash, 'type' => 0])->count();
            $listInfo = $this->buildArrFromObj($listInfo);

            return $this->buildSuccess([
                'list'     => $listInfo,
                'count'    => $count,
                'dataType' => $this->dataType
            ]);
        } else {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
    }

    /**
     * 获取返回参数
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function response() {
        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $limit * ($this->request->get('page', 1) - 1);
        $hash = $this->request->get('hash', '');

        if (!empty($hash)) {
            $listInfo = (new ApiFields())->where(['hash' => $hash, 'type' => 1])->limit($start, $limit)->select();
            $count = (new ApiFields())->where(['hash' => $hash, 'type' => 1])->count();
            $listInfo = $this->buildArrFromObj($listInfo);

            return $this->buildSuccess([
                'list'     => $listInfo,
                'count'    => $count,
                'dataType' => $this->dataType
            ]);
        } else {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
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
