<?php
/**
 * API数据过滤
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG;


use Home\ORG\Filter\ArrayFilter;
use Home\ORG\Filter\EnumFilter;
use Home\ORG\Filter\FloatFilter;
use Home\ORG\Filter\IntegerFilter;
use Home\ORG\Filter\OtherFilter;
use Home\ORG\Filter\StringFilter;

class Filter {

    /**
     * 返回参数过滤（主要是将返回参数的数据类型给规范）
     * @param array $data
     * @param array $rule
     * @return array
     */
    public function response($data, $rule = array()) {
        $newRule = array();
        foreach ($rule as $item) {
            $newRule[$item['showName']] = $item['dataType'];
        }
        if (is_array($data)) {
            $this->handle($data, $newRule);
        } elseif (empty($data)) {
            if ($newRule['data'] == DataType::TYPE_OBJECT) {
                $data = (object)array();
            } elseif ($newRule['data'] == DataType::TYPE_ARRAY) {
                $data = array();
            }
        }

        return $data;
    }

    private function handle(&$data, $rule, $prefix = 'data') {
        if (empty($data)) {
            if ($rule[$prefix] == DataType::TYPE_OBJECT) {
                $data = (object)array();
            }
        } else {
            if ($rule[$prefix] == DataType::TYPE_OBJECT) {
                $prefix .= '{}';
                foreach ($data as $index => &$datum) {
                    $myPre = $prefix . $index;
                    switch ($rule[$myPre]) {
                        case DataType::TYPE_INTEGER:
                            $datum = intval($datum);
                            break;
                        case DataType::TYPE_FLOAT:
                            $datum = floatval($datum);
                            break;
                        case DataType::TYPE_STRING:
                            $datum = strval($datum);
                            break;
                        default:
                            $this->handle($datum, $rule, $myPre);
                            break;
                    }
                }
            } else {
                $prefix .= '[]';
                if (is_array($data[0])) {
                    foreach ($data as &$datum) {
                        $this->handle($datum, $rule, $prefix);
                    }
                }
            }
        }
    }

    /**
     * 请求参数过滤（主要是判断字段的合法性）
     * @param       $data
     * @param array $rule
     */
    public function request(&$data, $rule = array()) {
        $newData = array();
        foreach ($rule as $value) {
            if (!isset($data[$value['fieldName']])) {
                if ($value['isMust']) {
                    Response::error(ReturnCode::EMPTY_PARAMS, '缺少必要参数：' . $value['fieldName']);
                } else {
                    if ($value['default'] === '') {
                        continue;
                    } else {
                        $data[$value['fieldName']] = $value['default'];
                    }
                }
            }
            if (isset($data[$value['fieldName']])) {
                $newData[$value['fieldName']] = $data[$value['fieldName']];
            }
            if ($value['range']) {
                $value['range'] = htmlspecialchars_decode($value['range']);
            }
            switch ($value['dataType']) {
                case DataType::TYPE_INTEGER:
                    $checkObj = new IntegerFilter();
                    if ($value['isMust'] && $newData[$value['fieldName']] == '') {
                        Response::error(ReturnCode::EMPTY_PARAMS, '字段[' . $value['fieldName'] . ']不能为空');
                    }
                    if ($newData[$value['fieldName']] != '') {
                        $checkObj->parse($newData[$value['fieldName']], json_decode($value['range'], true), $value['fieldName']);
                    }
                    break;
                case DataType::TYPE_STRING:
                    $checkObj = new StringFilter();
                    if ($value['isMust'] && $newData[$value['fieldName']] == '') {
                        Response::error(ReturnCode::EMPTY_PARAMS, '字段[' . $value['fieldName'] . ']不能为空');
                    }
                    $newData[$value['fieldName']] = trim($newData[$value['fieldName']]);
                    if ($newData[$value['fieldName']] != '') {
                        $checkObj->parse($newData[$value['fieldName']], json_decode($value['range'], true), $value['fieldName']);
                    }
                    break;
                case DataType::TYPE_ENUM:
                    $checkObj = new EnumFilter();
                    if ($value['isMust'] && $newData[$value['fieldName']] == '') {
                        Response::error(ReturnCode::EMPTY_PARAMS, '字段[' . $value['fieldName'] . ']不能为空');
                    }
                    if ($newData[$value['fieldName']] != '') {
                        $checkObj->parse($newData[$value['fieldName']], json_decode($value['range'], true), $value['fieldName']);
                    }
                    break;
                case DataType::TYPE_FLOAT:
                    $checkObj = new FloatFilter();
                    if ($value['isMust'] && empty($newData[$value['fieldName']]) && $newData[$value['fieldName']] != 0) {
                        Response::error(ReturnCode::EMPTY_PARAMS, '字段[' . $value['fieldName'] . ']不能为空');
                    }
                    $newData[$value['fieldName']] = trim($newData[$value['fieldName']]);
                    $checkObj->parse($newData[$value['fieldName']], json_decode($value['range'], true), $value['fieldName']);
                    break;
                case DataType::TYPE_ARRAY:
                    $checkObj = new ArrayFilter();
                    $checkObj->parse($newData[$value['fieldName']], json_decode($value['range'], true), $value['fieldName']);
                    break;
                case DataType::TYPE_MOBILE:
                    $checkObj = new OtherFilter();
                    $newData[$value['fieldName']] = trim($newData[$value['fieldName']]);
                    $checkObj->isMobile($newData[$value['fieldName']], json_decode($value['range'], true), $value['fieldName']);
                    break;
            }
        }
        $data = $newData;
    }

}