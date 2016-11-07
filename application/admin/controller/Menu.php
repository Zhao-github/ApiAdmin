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
                ],
                [
                    'field' => 'action',
                    'info' => '操作'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'Menu/add',
                    'class'=> 'am-btn-success',
                    'info'=> '新增',
                    'icon' => 'plus',
                    'ajax' => 0,
                ],
                [
                    'href' => 'Menu/del',
                    'class'=> 'am-btn-danger del-all',
                    'info'=> '删除',
                    'icon' => 'trash',
                    'ajax' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'desc' => '编辑',
                    'href' => 'Menu/edit',
                    'class'=> 'success',
                    'param'=> $this->primaryKey,
                    'icon' => 'check',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'desc' => '删除',
                    'href' => 'Menu/del',
                    'class'=> 'danger',
                    'param'=> $this->primaryKey,
                    'icon' => 'trash',
                    'confirm' => 1,
                    'show' => ''
                ]
            ],
            'typeRule' => [
                'name' => [
                    [
                        'module' => 'a',
                        'rule' => [
                            'info' => '',
                            'href' => 'Menu/add',
                            'param'=> $this->primaryKey,
                        ]
                    ]
                ],
                'hide' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '显示',
                            'class' => 'success',
                            'show' => ['hide', 1]
                        ],
                        [
                            'info' => '隐藏',
                            'class' => 'warning',
                            'show' => ['hide', 0]
                        ]
                    ]
                ],
                'type' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '方法类功能',
                            'class' => 'secondary',
                            'show' => ['type', 0]
                        ],
                        [
                            'info' => '模块类功能',
                            'class' => 'primary',
                            'show' => ['type', 1]
                        ]
                    ]
                ]
            ],
            'data' => []
        ];
        $this->result($table,200);
    }

    public function add(){

    }

    public function edit(){

    }

    public function del(){

    }

}