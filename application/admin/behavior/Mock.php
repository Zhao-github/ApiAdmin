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
            "myFb|123.3"         => 1,
            "object|2"           => [
                "name|1-3"  => ['myName', 123123, '1231541asdasd', 'jjjjsssss', '2345123afasgvawe'],
                "name2|2"   => ['myName', 123123, '1231541asdasd', 'jjjjsssss', '2345123afasgvawe'],
                'age|25-68' => 1
            ]
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
                    $data[$name] = $this->buildArray($rule, $value);
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

    /**
     * 构建随机的数组列表数据
     * @param string $rule
     * @param array $value
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildArray($rule = '', $value = []) {
        $isAssoc = $this->isAssoc($value);
        if ($isAssoc) {
            $has = strstr($rule, '-');
            if ($has) {
                list($min, $max) = explode('-', $rule);
                $num = mt_rand($min, $max);
            } else {
                $num = intval($rule);
            }

            $res = [];
            for ($i = 0; $i < $num; $i++) {
                $new = [];
                foreach ($value as $vKey => $item) {
                    $hasVertical = strstr($vKey, '|');
                    if ($hasVertical) {
                        $new = array_merge($new, $this->buildData([$vKey => $item]));
                    } else {
                        $new[$vKey] = $item;
                    }
                }
                $res[] = $new;
            }

            return $res;
        } else {
            $hasVertical = strstr($rule, '-');
            if ($hasVertical) {
                $new = [];
                list($min, $max) = explode('-', $rule);
                $num = mt_rand($min, $max);
                for ($i = 0; $i < $num; $i++) {
                    $new = array_merge($new, $value);
                }

                return $new;
            } else {
                $rule = intval($rule);
                if (count($value) <= $rule) {
                    return $value;
                } else {
                    $new = [];
                    shuffle($value);
                    for ($i = 0; $i < $rule; $i++) {
                        $new[] = $value[$i];
                    }

                    return $new;
                }
            }
        }
    }

    /**
     * 判断是否是关联数组
     * @param $array
     * @return bool true 是关联数组  false 是索引数组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function isAssoc($array) {
        if (is_array($array)) {
            $keys = array_keys($array);

            return $keys !== array_keys($keys);
        }

        return false;
    }

}
