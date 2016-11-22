<?php
/**
 * Api管理中心
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use app\admin\model\App;

class AppManager extends Base {

    public function index(){
        $data = [];
//        $dataObj = App::all();

        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '应用名称'
                ],
                [
                    'field' => 'token',
                    'info' => '应用唯一标识'
                ],
                [
                    'field' => 'baseUrl',
                    'info' => '基础URL'
                ],
                [
                    'field' => 'keyGroup',
                    'info' => '关联秘钥组'
                ],
                [
                    'field' => 'type',
                    'info' => '参与角色'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'Menu/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => 'Menu/del',
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '编辑',
                    'href' => 'Menu/edit',
                    'class'=> 'btn-warning',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'Menu/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                    'show' => ''
                ]
            ],
            'typeRule' => [
                'name' => [
                    'module' => 'a',
                    'rule' => [
                        'info' => '',
                        'href' => url('Menu/add'),
                        'param'=> [$this->primaryKey],
                        'class' => 'refresh'
                    ]
                ],
                'hide' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '显示',
                            'class' => 'label label-success'
                        ],
                        [
                            'info' => '隐藏',
                            'class' => 'label label-warning'
                        ]
                    ]
                ]
            ],
            'data' => $data
        ];
        $table = $this->_prepareTemplate($table);
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

}