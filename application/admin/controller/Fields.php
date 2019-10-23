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
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $hash = $this->request->get('hash', '');

        if (empty($hash)) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $listObj = (new AdminFields())->where(['hash' => $hash, 'type' => 0])
            ->paginate($limit, false, ['page' => $start])->toArray();

        $apiInfo = (new AdminList())->where('hash', $hash)->find();

        return $this->buildSuccess([
            'list'     => $listObj['data'],
            'count'    => $listObj['total'],
            'dataType' => $this->dataType,
            'apiInfo'  => $apiInfo
        ]);
    }

    /**
     * 获取返回参数
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function response() {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $hash = $this->request->get('hash', '');

        if (empty($hash)) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $listObj = (new AdminFields())->where(['hash' => $hash, 'type' => 1])
            ->paginate($limit, false, ['page' => $start])->toArray();

        $apiInfo = (new AdminList())->where('hash', $hash)->find();

        return $this->buildSuccess([
            'list'     => $listObj['data'],
            'count'    => $listObj['total'],
            'dataType' => $this->dataType,
            'apiInfo'  => $apiInfo
        ]);
    }

    /**
     * 新增字段
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $postData = $this->request->post();
        $postData['show_name'] = $postData['field_name'];
        $postData['default'] = $postData['defaults'];
        unset($postData['defaults']);
        $res = AdminFields::create($postData);

        cache('RequestFields:NewRule:' . $postData['hash'], null);
        cache('RequestFields:Rule:' . $postData['hash'], null);
        cache('ResponseFieldsRule:' . $postData['hash'], null);

        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 字段编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $postData = $this->request->post();
        $postData['show_name'] = $postData['field_name'];
        $postData['default'] = $postData['defaults'];
        unset($postData['defaults']);
        $res = AdminFields::update($postData);

        cache('RequestFields:NewRule:' . $postData['hash'], null);
        cache('RequestFields:Rule:' . $postData['hash'], null);
        cache('ResponseFieldsRule:' . $postData['hash'], null);

        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 字段删除
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
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

        return $this->buildSuccess();
    }

    /**
     * 批量上传返回字段
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
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
        AdminList::update(['return_str' => json_encode($data)], ['hash' => $hash]);
        $this->handle($data['data'], $dataArr);
        $old = (new AdminFields())->where([
            'hash' => $hash,
            'type' => $type
        ])->select();
        $old = Tools::buildArrFromObj($old);
        $oldArr = array_column($old, 'show_name');
        $newArr = array_column($dataArr, 'show_name');
        $addArr = array_diff($newArr, $oldArr);
        $delArr = array_diff($oldArr, $newArr);
        if ($delArr) {
            AdminFields::destroy(['show_name' => ['in', $delArr]]);
        }
        if ($addArr) {
            $addData = [];
            foreach ($dataArr as $item) {
                if (in_array($item['show_name'], $addArr)) {
                    $addData[] = $item;
                }
            }
            (new AdminFields())->insertAll($addData);
        }

        cache('RequestFields:NewRule:' . $hash, null);
        cache('RequestFields:Rule:' . $hash, null);
        cache('ResponseFieldsRule:' . $hash, null);

        return $this->buildSuccess();
    }

    private function handle($data, &$dataArr, $prefix = 'data', $index = 'data') {
        if (!$this->isAssoc($data)) {
            $addArr = array(
                'field_name' => $index,
                'show_name'  => $prefix,
                'hash'       => $this->request->post('hash'),
                'is_must'    => 1,
                'data_type'  => DataType::TYPE_ARRAY,
                'type'       => $this->request->post('type')
            );
            $dataArr[] = $addArr;
            $prefix .= '[]';
            if (isset($data[0]) && is_array($data[0])) {
                $this->handle($data[0], $dataArr, $prefix);
            }
        } else {
            $addArr = array(
                'field_name' => $index,
                'show_name'  => $prefix,
                'hash'       => $this->request->post('hash'),
                'is_must'    => 1,
                'data_type'  => DataType::TYPE_OBJECT,
                'type'       => $this->request->post('type')
            );
            $dataArr[] = $addArr;
            $prefix .= '{}';
            foreach ($data as $index => $datum) {
                $myPre = $prefix . $index;
                $addArr = array(
                    'field_name' => $index,
                    'show_name'  => $myPre,
                    'hash'       => $this->request->post('hash'),
                    'is_must'    => 1,
                    'data_type'  => DataType::TYPE_STRING,
                    'type'       => $this->request->post('type')
                );
                if (is_numeric($datum)) {
                    if (preg_match('/^\d*$/', $datum)) {
                        $addArr['data_type'] = DataType::TYPE_INTEGER;
                    } else {
                        $addArr['data_type'] = DataType::TYPE_FLOAT;
                    }
                    $dataArr[] = $addArr;
                } elseif (is_array($datum)) {
                    $this->handle($datum, $dataArr, $myPre, $index);
                } else {
                    $addArr['data_type'] = DataType::TYPE_STRING;
                    $dataArr[] = $addArr;
                }
            }
        }
    }

    /**
     * 判断是否是关联数组（true表示是关联数组）
     * @param array $arr
     * @return bool
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function isAssoc(array $arr) {
        if (array() === $arr) return false;

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
