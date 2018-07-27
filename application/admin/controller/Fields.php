<?php
/**
 * 接口输入输出字段维护
 * @since   2018-02-21
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\AdminFields;
use app\model\AdminList;
use app\util\DataType;
use app\util\ReturnCode;
use app\util\Tools;

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
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function request() {
        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $hash = $this->request->get('hash', '');

        if (!empty($hash)) {
            $listObj = (new AdminFields())->where(['hash' => $hash, 'type' => 0])
                ->paginate($limit, false, ['page' => $start])->toArray();

            return $this->buildSuccess([
                'list'  => $listObj['data'],
                'count' => $listObj['total'],
                'dataType' => $this->dataType
            ]);
        } else {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
    }

    /**
     * 获取返回参数
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function response() {
        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $hash = $this->request->get('hash', '');

        if (!empty($hash)) {
            $listObj = (new AdminFields())->where(['hash' => $hash, 'type' => 1])
                ->paginate($limit, false, ['page' => $start])->toArray();

            return $this->buildSuccess([
                'list'  => $listObj['data'],
                'count' => $listObj['total'],
                'dataType' => $this->dataType
            ]);
        } else {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
    }

    /**
     * 新增字段
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function add() {
        $postData = $this->request->post();
        $postData['showName'] = $postData['fieldName'];
        $postData['default'] = $postData['defaults'];
        unset($postData['defaults']);
        $res = AdminFields::create($postData);

        cache('RequestFields:NewRule:' . $postData['hash'], null);
        cache('RequestFields:Rule:' . $postData['hash'], null);
        cache('ResponseFieldsRule:' . $postData['hash'], null);

        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess('操作成功');
        }
    }

    /**
     * 字段编辑
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function edit() {
        $postData = $this->request->post();
        $postData['showName'] = $postData['fieldName'];
        $postData['default'] = $postData['defaults'];
        unset($postData['defaults']);
        $res = AdminFields::update($postData);

        cache('RequestFields:NewRule:' . $postData['hash'], null);
        cache('RequestFields:Rule:' . $postData['hash'], null);
        cache('ResponseFieldsRule:' . $postData['hash'], null);

        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 字段删除
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\exception\DbException
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }

        $fieldsInfo = AdminFields::get($id);
        cache('RequestFields:NewRule:' . $fieldsInfo->hash, null);
        cache('RequestFields:Rule:' . $fieldsInfo->hash, null);
        cache('ResponseFieldsRule:' . $fieldsInfo->hash, null);

        AdminFields::destroy($id);

        return $this->buildSuccess([]);
    }

    /**
     * 批量上传返回字段
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function upload() {
        $hash = $this->request->post('hash');
        $type = $this->request->post('type');
        $jsonStr = $this->request->post('jsonStr');
        $jsonStr = html_entity_decode($jsonStr);
        $data = json_decode($jsonStr, true);
        if ($data === null) {
            return $this->buildFailed(ReturnCode::EXCEPTION, 'JSON数据格式有误');
        }
        AdminList::update(['returnStr' => json_encode($data)], ['hash' => $hash]);
        $this->handle($data['data'], $dataArr);
        $old = (new AdminFields())->where([
            'hash' => $hash,
            'type' => $type
        ])->select();
        $old = Tools::buildArrFromObj($old);
        $oldArr = array_column($old, 'showName');
        $newArr = array_column($dataArr, 'showName');
        $addArr = array_diff($newArr, $oldArr);
        $delArr = array_diff($oldArr, $newArr);
        if ($delArr) {
            AdminFields::destroy(['showName' => ['in', $delArr]]);
        }
        if ($addArr) {
            $addData = [];
            foreach ($dataArr as $item) {
                if (in_array($item['showName'], $addArr)) {
                    $addData[] = $item;
                }
            }
            (new AdminFields())->insertAll($addData);
        }

        cache('RequestFields:NewRule:' . $hash, null);
        cache('RequestFields:Rule:' . $hash, null);
        cache('ResponseFieldsRule:' . $hash, null);

        return $this->buildSuccess([]);
    }

    private function handle($data, &$dataArr, $prefix = 'data', $index = 'data') {
        if (!$this->isAssoc($data)) {
            $addArr = array(
                'fieldName' => $index,
                'showName'  => $prefix,
                'hash'      => $this->request->post('hash'),
                'isMust'    => 1,
                'dataType'  => DataType::TYPE_ARRAY,
                'type'      => $this->request->post('type')
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
                'hash'      => $this->request->post('hash'),
                'isMust'    => 1,
                'dataType'  => DataType::TYPE_OBJECT,
                'type'      => $this->request->post('type')
            );
            $dataArr[] = $addArr;
            $prefix .= '{}';
            foreach ($data as $index => $datum) {
                $myPre = $prefix . $index;
                $addArr = array(
                    'fieldName' => $index,
                    'showName'  => $myPre,
                    'hash'      => $this->request->post('hash'),
                    'isMust'    => 1,
                    'dataType'  => DataType::TYPE_STRING,
                    'type'      => $this->request->post('type')
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
