<?php

namespace app\http\middleware;

use app\model\AdminFields;
use app\util\DataType;
use app\util\ReturnCode;
use think\facade\Cache;
use think\facade\Validate;
use think\Request as re;


class RequestFilter {

    /**
     * 接口请求字段过滤
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $apiInfo = $request->API_CONF_DETAIL;
        $data = $request->param();
        $method = $request->method();

        $has = Cache::has('RequestFields:NewRule:' . $apiInfo['hash']);
        if ($has) {
            $newRule = cache('RequestFields:NewRule:' . $apiInfo['hash']);
            $rule = cache('RequestFields:Rule:' . $apiInfo['hash']);
        } else {
            $rule = AdminFields::all(['hash' => $apiInfo['hash'], 'type' => 0]);
            $newRule = $this->buildValidateRule($rule);
            cache('RequestFields:NewRule:' . $apiInfo['hash'], $newRule);
            cache('RequestFields:Rule:' . $apiInfo['hash'], $rule);
        }

        if ($newRule) {
            $validate = Validate::make($newRule);
            if (!$validate->check($data)) {
                return json(['code' => ReturnCode::PARAM_INVALID, 'msg' => $validate->getError(), 'data' => []]);
            }
        }

        $newData = [];
        foreach ($rule as $item) {
            $newData[$item['field_name']] = isset($data[$item['field_name']]) ? $data[$item['field_name']] : '';
            if (!$item['is_must'] && $item['default'] !== '' && !isset($data[$item['field_name']])) {
                $newData[$item['field_name']] = $item['default'];
            }
        }

        /**
         * TODO::等待研究没有测试通过
         */
        switch ($method) {
            case 'GET':
                (new re())->withGet($newData);
                break;
            case 'POST':
                (new re())->withPost($newData);
                break;
            case 'DELETE':
            case 'PUT':
                (new re())->withInput($newData);
                break;
        }

        return $next($request);
    }

    /**
     * 将数据库中的规则转换成TP_Validate使用的规则数组
     * @param array $rule
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function buildValidateRule($rule = array()) {
        $newRule = [];
        if ($rule) {
            foreach ($rule as $value) {
                if ($value['is_must']) {
                    $newRule[$value['field_name'] . '|' . $value['info']][] = 'require';
                }
                switch ($value['data_type']) {
                    case DataType::TYPE_INTEGER:
                        $newRule[$value['field_name'] . '|' . $value['info']][] = 'number';
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['egt'] = $range['min'];
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['elt'] = $range['max'];
                            }
                        }
                        break;
                    case DataType::TYPE_STRING:
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['min'] = $range['min'];
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['max'] = $range['max'];
                            }
                        }
                        break;
                    case DataType::TYPE_ENUM:
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            $newRule[$value['field_name'] . '|' . $value['info']]['in'] = implode(',', $range);
                        }
                        break;
                    case DataType::TYPE_FLOAT:
                        $newRule[$value['field_name'] . '|' . $value['info']][] = 'float';
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['egt'] = $range['min'];
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['elt'] = $range['max'];
                            }
                        }
                        break;
                    case DataType::TYPE_ARRAY:
                        $newRule[$value['field_name']][] = 'array';
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['min'] = $range['min'];
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['field_name'] . '|' . $value['info']]['max'] = $range['max'];
                            }
                        }
                        break;
                    case DataType::TYPE_MOBILE:
                        $newRule[$value['field_name'] . '|' . $value['info']]['regex'] = '/^1[34578]\d{9}$/';
                        break;
                }
            }
        }

        return $newRule;
    }
}
