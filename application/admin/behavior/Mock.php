<?php
/**
 * 自动构建随机的Mock数据，参考mockjs的配置
 * @since   2018-08-23
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\behavior;


use app\util\ReturnCode;
use app\util\StrRandom;
use app\util\Strs;
use think\Config;

class Mock {

    /**
     * 拦截并且返回Mock数据
     * @return \think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function run() {
        $header = Config::get('apiAdmin.CROSS_DOMAIN');

        $config = [
            "myField|1-10"       => "1",
            "myNum|1-100"        => 1,
            "myFloat|1-100.1-10" => 1,
            "myFa|123.1-10"      => 1,
            "myFb|123.3"         => 1
        ];
        $data = $this->buildData($config);

        $return = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功', 'data' => $data];

        return json($return, 200, $header);
    }

    /**
     * 构建随机数据
     * @param $config
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildData($config) {
        $data = [];

        foreach ($config as $key => $value) {
            $vType = gettype($value);
            list($name, $rule) = explode('|', $key);
            switch ($vType) {
                case 'integer':
                    $data[$name] = $this->buildInt($rule);
                    break;
                case 'array':
                    break;
                case 'string':
                    $data[$name] = $this->buildString($rule);
                    break;
                case 'double':
                    $data[$name] = $this->buildFloat($rule);
                    break;
                case 'boolean':
                    break;
            }
        }

        return $data;
    }

    /**
     * 构建随机浮点数
     * @param string $rule
     * @return float|int
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildFloat($rule = '') {
        $hasDot = strstr($rule, '.');
        if (!$hasDot) {
            return $this->buildInt($rule);
        }
        list($intPart, $floatPart) = explode('.', $rule);

        $intVertical = strstr($intPart, '-');
        if ($intVertical) {
            list($intMin, $intMax) = explode('-', $intPart);
        } else {
            $intMin = $intMax = $intPart;
        }

        $floatVertical = strstr($floatPart, '-');
        if ($floatVertical) {
            list($floatMin, $floatMax) = explode('-', $floatPart);
        } else {
            $floatMin = $floatMax = $floatPart;
        }

        return StrRandom::randomFloat($intMin, $intMax, $floatMin, $floatMax);
    }

    /**
     * 构建随机的整型数据
     * @param string $rule
     * @return float|integer
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildInt($rule = '') {
        $hasDot = strstr($rule, '.');
        if ($hasDot) {
            return $this->buildFloat($rule);
        }
        $hasVertical = strstr($rule, '-');
        if ($hasVertical) {
            list($min, $max) = explode('-', $rule);
            return mt_rand($min, $max);
        } else {
            return intval($rule);
        }
    }

    /**
     * 构建随机字符串
     * @param string $rule
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildString($rule = '') {
        $hasVertical = strstr($rule, '-');
        if ($hasVertical) {
            list($minLen, $maxLen) = explode('-', $rule);
            $len = mt_rand($minLen, $maxLen);
        } else {
            $len = $rule;
        }

        return Strs::randString($len);
    }

    private function buildArray($rule = '') {

    }

    private function buildObject($rule = '') {

    }

}
