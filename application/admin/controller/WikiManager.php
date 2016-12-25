<?php
/**
 * 文档生成引擎
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


class WikiManager extends Base {

    const FILE_CACHE = 0;
    const SERVER_CACHE = 1;
    const NO_CACHE = 2;

    private $cacheType;

    public function _myInitialize( $type = '' ) {
        if( empty($type) ){
            $this->cacheType = self::FILE_CACHE;
        }else{
            $this->cacheType = $type;
        }
    }

    public function api(){

    }

    public function app(){

    }

    private function getWikiToken( $mark ){

    }

    private function getFileCache( $str ){

    }

    private function delFileCache( $str ){

    }

    private function getServerCache( $str ){

    }

    private function delServerCache( $str ){

    }
}