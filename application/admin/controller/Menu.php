<?php
namespace app\admin\controller;

/**
 * 菜单管理控制器
 * @since   2016-11-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class Menu extends Base {

    public function index(){
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '菜单名称'
                ],
                [
                    'field' => 'url',
                    'info' => '菜单URL'
                ],
                [
                    'field' => 'type',
                    'info' => '菜单类型'
                ],
                [
                    'field' => 'level',
                    'info' => '等级'
                ],
                [
                    'field' => 'hide',
                    'info' => '隐藏'
                ],
                [
                    'field' => 'sort',
                    'info' => '排序'
                ]
            ],
            'topButton' => [
                [
                    'href' => url('Menu/add'),
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => url('Menu/del'),
                    'class'=> 'btn-danger',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '编辑',
                    'href' => url('Menu/edit'),
                    'class'=> 'btn-warning',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => url('Menu/del'),
                    'class'=> 'btn-danger',
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
                    ]
                ],
                'hide' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '隐藏',
                            'class' => 'label label-warning'
                        ],
                        [
                            'info' => '显示',
                            'class' => 'label label-success'
                        ],
                    ]
                ],
                'type' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '方法类功能',
                            'class' => 'label label-info'
                        ],
                        [
                            'info' => '模块类功能',
                            'class' => 'label label-primary'
                        ]
                    ]
                ]
            ],
            'data' => [] //这个数据应该是从数据库中查出来
        ];
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    public function add(){

    }

    public function edit(){

    }

    public function del(){

    }

}