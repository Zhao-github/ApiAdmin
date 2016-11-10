<?php
namespace app\admin\controller;

use app\admin\model\Menu;

class Index extends Base  {
    public function index() {
        $dataObj = Menu::all(function($query){
            $query->order('sort', 'asc');
        });
        foreach ($dataObj as $value){
            if( !$value->hide ){
                $data[] = $value->toArray();
            }
        }
        $data = listToTree($data);
        $this->assign('title', '首页');
        $this->assign('menuData', $data);
        return $this->fetch();
    }
}
