<?php
/**
 * @since   2016-12-12
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\admin\model\Api;

class ApiManager extends Base {

    private $apps;

    public function _myInitialize() {
        $this->apps = cache(CacheType::APP_LIST_KEY);
        if( !$this->apps ){
            $this->error('请先配置应用！', url('AppManager/index'));
        }else{
            $this->apps = [0 => '不关联'] + $this->apps;
        }
    }

    public function index(){
        if( $this->request->get($this->primaryKey) ){
            $data = Api::all(['appId' => $this->request->get($this->primaryKey)]);
        }else{
            $data = Api::all();
        }
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '接口名称'
                ],
                [
                    'field' => 'mark',
                    'info' => '接口标记'
                ],
                [
                    'field' => 'map',
                    'info' => '接口映射'
                ],
                [
                    'field' => 'version',
                    'info' => '接口版本'
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'ApiManager/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => 'ApiManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '启用',
                    'href' => 'ApiManager/open',
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => 'ApiManager/close',
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '编辑',
                    'href' => 'ApiManager/edit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => 'API文档【开发中】',
                    'href' => 'WikiManager/api',
                    'class'=> 'btn-success',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-support',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '请求参数',
                    'href' => 'ApiFieldsManager/index',
                    'class'=> 'btn-warning',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-sign-in',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '返回参数',
                    'href' => 'ApiFieldsManager/back',
                    'class'=> 'btn-info',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-sign-out',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'ApiManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                    'show' => ''
                ]
            ],
            'typeRule' => [
                'status' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '禁用',
                            'class' => 'label label-danger'
                        ],
                        [
                            'info' => '启用',
                            'class' => 'label label-success'
                        ]
                    ]
                ]
            ],
            'data' => $data
        ];
        $table = $this->_prepareTemplate($table);
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    public function add(){
        if( $this->request->isPost() ){
            $apiModel = new Api();
            $result = $apiModel->allowField(true)->save($this->request->post());
            if(false === $result){
                $this->error($apiModel->getError());
            }else{
                $this->success('操作成功！', url('ApiManager/index'));
            }
        }else{
            $map = uniqid();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('ApiManager/add'),
                    'formId' => 'add-ApiManager-form',
                    'backUrl' => url('ApiManager/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '映射为系统自动生成，并且不可修改',
                        'info' => 'API映射：',
                        'attr' => [
                            'name' => 'map',
                            'value' => $map,
                            'placeholder' => '',
                            'readOnly' => true
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '适配APP：',
                        'attr' => [
                            'name' => 'appId',
                            'value' => '',
                            'options' => $this->apps
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API标记：',
                        'attr' => [
                            'name' => 'mark',
                            'value' => '',
                            'placeholder' => '请求第三方API服务时候需要拼接的URL，例如：index/index'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API版本：',
                        'attr' => [
                            'name' => 'version',
                            'value' => '',
                            'placeholder' => '建议使用:v1.1.1这类的版本号'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API提示：',
                        'attr' => [
                            'name' => 'warning',
                            'value' => '',
                            'placeholder' => '用于API文档生成'
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => 'API描述：',
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

    public function open(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $apiObj = Api::get([$this->primaryKey => $id]);
            if( is_null($apiObj) ){
                $this->error('当前API不存在','');
            }else{
                $apiObj->status = 1;
                $apiObj->save();
                $this->success('操作成功', url('ApiManager/index'));
            }
        }
    }

    public function close(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $apiObj = Api::get([$this->primaryKey => $id]);
            if( is_null($apiObj) ){
                $this->error('当前API不存在','');
            }else{
                $apiObj->status = 0;
                $apiObj->save();
                $this->success('操作成功', url('ApiManager/index'));
            }
        }
    }

    public function del(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            $delNum = Api::destroy($key);
            if( $delNum ){
                $this->success('操作成功！', url('ApiManager/index'));
            }
        }
        $this->error('操作失败！');
    }

    public function edit(){
        if( $this->request->isPut() ){
            if( empty($this->request->put('name')) ){
                $this->error('应用名称不能为空', '');
            }
            $data = $this->request->put();
            $appMemberModel = new Api();
            $appMemberModel->update($data);
            $this->success('操作成功！', url('ApiManager/index'));
        }else{
            $detail = Api::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('ApiManager/edit'),
                    'formId' => 'edit-ApiManager-form',
                    'backUrl' => url('ApiManager/index'),
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
                        'module' => 'text',
                        'description' => '映射为系统自动生成，并且不可修改',
                        'info' => 'API映射：',
                        'attr' => [
                            'name' => 'map',
                            'value' => $detail['map'],
                            'placeholder' => '',
                            'readOnly' => true
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '适配APP：',
                        'attr' => [
                            'name' => 'appId',
                            'value' => $detail['appId'],
                            'options' => $this->apps
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API标记：',
                        'attr' => [
                            'name' => 'mark',
                            'value' => $detail['mark'],
                            'placeholder' => '请求第三方API服务时候需要拼接的URL，例如：index/index'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API版本：',
                        'attr' => [
                            'name' => 'version',
                            'value' => $detail['version'],
                            'placeholder' => '建议使用:v1.1.1这类的版本号'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => 'API提示：',
                        'attr' => [
                            'name' => 'warning',
                            'value' => $detail['warning'],
                            'placeholder' => '用于API文档生成'
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => 'API描述：',
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

}