<?php
/**
 * @since   2016-12-13
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\admin\model\ApiBack;
use app\admin\model\ApiFields;

class ApiFieldsManager extends Base {

    private $dataType = [
        'string' => '字符串',
        'int' => '整型',
        'float' => '浮点型',
        'boolean' => '布尔型',
        'date' => '日期',
        'array' => '数组',
        'fixed' => '固定值',
        'enum' => '枚举类型',
        'object' => '对象',
    ];

    public function index(){
        if( $this->request->get($this->primaryKey) ){
            session('apiId', $this->request->get($this->primaryKey));
            session('apiName', $this->request->get('name'));
        }else{
            if( !session('apiId') ){
                $this->error('缺少必要参数', '');
            }
        }
        $data = ApiFields::all(['apiId' => session('apiId')]);
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '字段名称'
                ],
                [
                    'field' => 'type',
                    'info' => '字段类型'
                ],
                [
                    'field' => 'must',
                    'info' => '是否必须'
                ],
                [
                    'field' => 'default',
                    'info' => '默认值'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'ApiFieldsManager/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => 'ApiFieldsManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '编辑',
                    'href' => 'ApiFieldsManager/edit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'ApiFieldsManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                    'show' => ''
                ]
            ],
            'typeRule' => [
                'must' => [
                    'module' => 'icon',
                    'rule' => [
                        [
                            'info' => '',
                            'class' => 'fa fa-close'
                        ],
                        [
                            'info' => '',
                            'class' => 'fa fa-check'
                        ]
                    ]
                ],
                'type' => [
                    'module' => 'listValue',
                    'rule' => $this->dataType
                ],
            ],
            'data' => $data
        ];
        $table = $this->_prepareTemplate($table);
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    public function back(){
        if( $this->request->get($this->primaryKey) ){
            session('apiId', $this->request->get($this->primaryKey));
            session('apiName', $this->request->get('name'));
        }else{
            if( !session('apiId') ){
                $this->error('缺少必要参数', '');
            }
        }
        $data = ApiBack::all(['apiId' => session('apiId')]);
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '字段名称'
                ],
                [
                    'field' => 'type',
                    'info' => '字段类型'
                ],
                [
                    'field' => 'info',
                    'info' => '字段说明'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'ApiFieldsManager/backAdd',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => 'ApiFieldsManager/backDel',
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '编辑',
                    'href' => 'ApiFieldsManager/backEdit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'ApiFieldsManager/backDel',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                    'show' => ''
                ]
            ],
            'typeRule' => [
                'type' => [
                    'module' => 'listValue',
                    'rule' => $this->dataType
                ],
            ],
            'data' => $data
        ];
        $table = $this->_prepareTemplate($table);
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    public function add(){
        if( $this->request->isPost() ){
            $apiModel = new ApiFields();
            $result = $apiModel->allowField(true)->save($this->request->post());
            if(false === $result){
                $this->error($apiModel->getError());
            }else{
                $this->success('操作成功！', url('ApiFieldsManager/index'));
            }
        }else{
            $form = [
                'formTitle' => $this->menuInfo['name']."（".session('apiName')."）",
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('ApiFieldsManager/add'),
                    'formId' => 'add-ApiFieldsManager-form',
                    'backUrl' => url('ApiFieldsManager/index'),
                ],
                'formList' => [
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => 'apiId',
                            'value' => session('apiId'),
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '字段名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '字段类型：',
                        'attr' => [
                            'name' => 'type',
                            'value' => '',
                            'options' => $this->dataType
                        ]
                    ],
                    [
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否必须：',
                        'attr' => [
                            'name' => 'must',
                            'value' => '',
                            'options' => [
                                '不必须',
                                '必须',
                            ]
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '默认值：',
                        'attr' => [
                            'name' => 'default',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '字段描述：',
                        'attr' => [
                            'name' => 'info',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    public function backAdd(){
        if( $this->request->isPost() ){
            $apiModel = new ApiBack();
            $result = $apiModel->allowField(true)->save($this->request->post());
            if(false === $result){
                $this->error($apiModel->getError());
            }else{
                $this->success('操作成功！', url('ApiFieldsManager/back'));
            }
        }else{
            $form = [
                'formTitle' => $this->menuInfo['name']."（".session('apiName')."）",
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('ApiFieldsManager/backAdd'),
                    'formId' => 'add-ApiFieldsManager-form',
                    'backUrl' => url('ApiFieldsManager/back'),
                ],
                'formList' => [
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => 'apiId',
                            'value' => session('apiId'),
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '字段名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '字段类型：',
                        'attr' => [
                            'name' => 'type',
                            'value' => '',
                            'options' => $this->dataType
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '字段描述：',
                        'attr' => [
                            'name' => 'info',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    public function edit(){
        if( $this->request->isPut() ){
            $data = $this->request->put();
            $apiModel = new ApiFields();
            $apiModel->update($data);
            $this->success('操作成功！', url('ApiFieldsManager/index'));
        }else{
            $detail = ApiFields::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name']."（".session('apiName')."）",
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('ApiFieldsManager/edit'),
                    'formId' => 'edit-ApiFieldsManager-form',
                    'backUrl' => url('ApiFieldsManager/index'),
                ],
                'formList' => [
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => $this->primaryKey,
                            'value' => $detail[$this->primaryKey],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => 'apiId',
                            'value' => session('apiId'),
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '字段名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '字段类型：',
                        'attr' => [
                            'name' => 'type',
                            'value' => $detail['type'],
                            'options' => $this->dataType
                        ]
                    ],
                    [
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否必须：',
                        'attr' => [
                            'name' => 'must',
                            'value' => $detail['must'],
                            'options' => [
                                '不必须',
                                '必须',
                            ]
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '默认值：',
                        'attr' => [
                            'name' => 'default',
                            'value' => $detail['default'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '字段描述：',
                        'attr' => [
                            'name' => 'info',
                            'value' => $detail['info'],
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    public function backEdit(){
        if( $this->request->isPut() ){
            $data = $this->request->put();
            $apiModel = new ApiBack();
            $apiModel->update($data);
            $this->success('操作成功！', url('ApiFieldsManager/back'));
        }else{
            $detail = ApiBack::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name']."（".session('apiName')."）",
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('ApiFieldsManager/backEdit'),
                    'formId' => 'edit-ApiFieldsManager-form',
                    'backUrl' => url('ApiFieldsManager/back'),
                ],
                'formList' => [
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => $this->primaryKey,
                            'value' => $detail[$this->primaryKey],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => 'apiId',
                            'value' => session('apiId'),
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '字段名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '字段类型：',
                        'attr' => [
                            'name' => 'type',
                            'value' => $detail['type'],
                            'options' => $this->dataType
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '字段描述：',
                        'attr' => [
                            'name' => 'info',
                            'value' => $detail['info'],
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    public function del(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            $delNum = ApiFields::destroy($key);
            if( $delNum ){
                $this->success('操作成功！', url('ApiFieldsManager/index'));
            }
        }
        $this->error('操作失败！');
    }

    public function backDel(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            $delNum = ApiBack::destroy($key);
            if( $delNum ){
                $this->success('操作成功！', url('ApiFieldsManager/back'));
            }
        }
        $this->error('操作失败！');
    }
}