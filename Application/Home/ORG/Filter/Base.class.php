<?php
/**
 *
 * @since   2017/03/01 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG\Filter;


use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Base {

    protected $fieldName;

    protected function filterByRange($value, $rule) {
        $this->checkSetting($rule);
        $this->checkMin($value, $rule);
        $this->checkMax($value, $rule);
    }

    protected function checkSetting($rule) {
        if (isset($rule['min']) && isset($rule['max']) && $rule['min'] > $rule['max']) {
            Response::error(ReturnCode::NUMBER_MATCH_ERROR, "字段[{$this->fieldName}]的系统配置矛盾，请检测");
        }
    }

    protected function checkMin($value, $rule) {
        if (isset($rule['min']) && $value < $rule['min']) {
            Response::error(ReturnCode::NUMBER_MATCH_ERROR, "字段[{$this->fieldName}]不合法，系统要求最小值为：". $rule['min']);
        }
    }

    protected function checkMax($value, $rule) {
        if (isset($rule['max']) && $value > $rule['max']) {
            Response::error(ReturnCode::NUMBER_MATCH_ERROR, "字段[{$this->fieldName}]不合法，系统要求最大值为：". $rule['max']);
        }
    }

}