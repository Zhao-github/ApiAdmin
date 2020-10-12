<?php
declare (strict_types=1);
/**
 *
 * @since   2017-11-01
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;

class Tools {

    /**
     * 获取相对时间
     * @param int $timestamp
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function getDate(int $timestamp): string {
        $now = time();
        $diff = $now - $timestamp;
        if ($diff <= 60) {
            return $diff . '秒前';
        } elseif ($diff <= 3600) {
            return floor($diff / 60) . '分钟前';
        } elseif ($diff <= 86400) {
            return floor($diff / 3600) . '小时前';
        } elseif ($diff <= 2592000) {
            return floor($diff / 86400) . '天前';
        } else {
            return '一个月前';
        }
    }

    /**
     * 二次封装的密码加密
     * @param $str
     * @param string $auth_key
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function userMd5(string $str, string $auth_key = ''): string {
        if (!$auth_key) {
            $auth_key = config('apiadmin.AUTH_KEY');
        }

        return '' === $str ? '' : md5(sha1($str) . $auth_key);
    }

    /**
     * 判断当前用户是否是超级管理员
     * @param int $uid
     * @return bool
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function isAdministrator(int $uid = 0): bool {
        if (!empty($uid)) {
            $adminConf = config('apiadmin.USER_ADMINISTRATOR');
            if (is_array($adminConf)) {
                if (is_array($uid)) {
                    $m = array_intersect($adminConf, $uid);
                    if (count($m)) {
                        return true;
                    }
                } else {
                    if (in_array($uid, $adminConf)) {
                        return true;
                    }
                }
            } else {
                if (is_array($uid)) {
                    if (in_array($adminConf, $uid)) {
                        return true;
                    }
                } else {
                    if ($uid == $adminConf) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * 将查询的二维对象转换成二维数组
     * @param $res
     * @param string $key 允许指定索引值
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function buildArrFromObj($res, string $key = ''): array {
        $arr = [];
        foreach ($res as $value) {
            $value = $value->toArray();
            if ($key) {
                $arr[$value[$key]] = $value;
            } else {
                $arr[] = $value;
            }
        }

        return $arr;
    }

    /**
     * 将二维数组变成指定key
     * @param $array
     * @param string $keyName
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function buildArrByNewKey($array, string $keyName = 'id'): array {
        $list = [];
        foreach ($array as $item) {
            $list[$item[$keyName]] = $item;
        }

        return $list;
    }

    /**
     * 把返回的数据集转换成Tree
     * @param $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param string $root
     * @return array
     */
    public static function listToTree(
        array $list,
        string $pk = 'id',
        string $pid = 'fid',
        string $child = 'children',
        string $root = '0'
    ): array {
        $tree = [];
        if (is_array($list)) {
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }

        return $tree;
    }

    /**
     * 将层级数组遍历成一维数组
     * @param array $list
     * @param int $lv
     * @param string $title
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function formatTree(array $list, int $lv = 0, string $title = 'title'): array {
        $formatTree = [];
        foreach ($list as $key => $val) {
            $title_prefix = '';
            for ($i = 0; $i < $lv; $i++) {
                $title_prefix .= "|---";
            }
            $val['lv'] = $lv;
            $val['namePrefix'] = $lv == 0 ? '' : $title_prefix;
            $val['showName'] = $lv == 0 ? $val[$title] : $title_prefix . $val[$title];
            if (!array_key_exists('children', $val)) {
                array_push($formatTree, $val);
            } else {
                $child = $val['children'];
                unset($val['children']);
                array_push($formatTree, $val);
                $middle = self::formatTree($child, $lv + 1, $title); //进行下一层递归
                $formatTree = array_merge($formatTree, $middle);
            }
        }

        return $formatTree;
    }
}
