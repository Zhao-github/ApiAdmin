<?php

namespace app\admin\controller;


use app\util\ReturnCode;

class Index extends Base {
    public function index() {
        return json(['welcome']);
    }

    public function upload() {
        $path = '/upload/' . date('Ymd', time()) . '/';
        $name = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];
        $error = $_FILES['file']['error'];
        //过滤错误
        if ($error) {
            switch ($error) {
                case 1 :
                    $error_message = '您上传的文件超过了PHP.INI配置文件中UPLOAD_MAX-FILESIZE的大小';
                    break;
                case 2 :
                    $error_message = '您上传的文件超过了PHP.INI配置文件中的post_max_size的大小';
                    break;
                case 3 :
                    $error_message = '文件只被部分上传';
                    break;
                case 4 :
                    $error_message = '文件不能为空';
                    break;
                default :
                    $error_message = '未知错误';
            }
            die($error_message);
        }
        $arr_name = explode('.', $name);
        $hz = array_pop($arr_name);
        $new_name = md5(time() . uniqid()) . '.' . $hz;
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . $path, 0755, true);
        }
        if (move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $path . $new_name)) {
            return $this->buildSuccess([
                'fileName' => $new_name,
                'fileUrl'  => $this->request->domain() . $path . $new_name
            ]);
        } else {
            return $this->buildFailed(ReturnCode::FILE_SAVE_ERROR, '文件上传失败');
        }
    }
}
