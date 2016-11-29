<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use app\admin\model\Keys;

class KeyManager extends Base {
    public function index(){
        $data = Keys::all();
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'description',
                    'info' => '秘钥描述'
                ],
                [
                    'field' => 'appId',
                    'info' => '适配APP'
                ],
                [
                    'field' => 'filterId',
                    'info' => '适配过滤组'
                ],
                [
                    'field' => 'addTime',
                    'info' => '创建时间'
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'KeyManager/add',
                    'class'=> 'btn-success',
                    'info'=> '创建',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '启用',
                    'href' => 'KeyManager/open',
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => 'KeyManager/close',
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '编辑',
                    'href' => 'KeyManager/edit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'KeyManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'typeRule' => [
                'addTime' => [
                    'module' => 'date',
                ],
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
            $userModel = new Keys();
            $result = $userModel->allowField(true)->save($this->request->post());
            if(false === $result){
                $this->error($userModel->getError());
            }else{
                $this->success('操作成功！', url('KeyManager/index'));
            }
        }else{
            $apps = cache(CacheType::APP_LIST_KEY);
            //TODO::等待应用配置开发完成开放APP验证
//            if( !$apps ){
//                $this->error('请先配置应用！', url('AppManager/index'));
//            }
            $filters = cache(CacheType::FILTER_LIST_KEY);
            $filters[-1] = '不限制';
            $sk = \StrOrg::randString(64);
            $ak = \StrOrg::keyGen();
            $form = [
                'formTitle' => $this->menuInfo['name'].'【为了API安全，请定期更换Key】',
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('KeyManager/add'),
                    'formId' => 'add-KeyManager-form',
                    'backUrl' => url('KeyManager/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '秘钥为系统自动生成，并且不可修改',
                        'info' => 'AccessKey：',
                        'attr' => [
                            'name' => 'accessKey',
                            'value' => $ak,
                            'placeholder' => '',
                            'readOnly' => true
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '秘钥为系统自动生成，并且不可修改',
                        'info' => 'SecretKey：',
                        'attr' => [
                            'name' => 'secretKey',
                            'value' => $sk,
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
                            'options' => $apps
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '适配过滤组：',
                        'attr' => [
                            'name' => 'filterId',
                            'value' => '',
                            'options' => $filters
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '秘钥描述：',
                        'attr' => [
                            'name' => 'description',
                            'value' => '',
                            'placeholder' => '秘钥的说明，请尽可能的简短清晰！'
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
            if(!isAdministrator($key)){
                $delNum = \app\admin\model\User::destroy($key);
                if( $delNum ){
                    UserData::destroy(['uid' => $key]);
                    AuthGroupAccess::destroy(['uid' => $key]);
                    $this->success('操作成功！', url('User/index'));
                }
            }else{
                $this->error('管理员不能被删除！');
            }
        }
        $this->error('操作失败！');
    }

    public function edit(){
        if( $this->request->isPut() ){
            $data = $this->request->put();
            $keysModel = new Keys();
            $keysModel->allowField(true)->update($data);
            $this->success('操作成功！', url('KeyManager/index'));
        }else{
            $apps = cache(CacheType::APP_LIST_KEY);
            //TODO::等待应用配置开发完成开放APP验证
//            if( !$apps ){
//                $this->error('请先配置应用！', url('AppManager/index'));
//            }
            $filters = cache(CacheType::FILTER_LIST_KEY);
            $filters[-1] = '不限制';
            $detail = Keys::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('KeyManager/edit'),
                    'formId' => 'edit-keyManager-form',
                    'backUrl' => url('KeyManager/index'),
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
                        'description' => '秘钥为系统自动生成，并且不可修改',
                        'info' => 'AccessKey：',
                        'attr' => [
                            'name' => 'accessKey',
                            'value' => $detail['accessKey'],
                            'placeholder' => '',
                            'readOnly' => true
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '秘钥为系统自动生成，并且不可修改',
                        'info' => 'SecretKey：',
                        'attr' => [
                            'name' => 'secretKey',
                            'value' => $detail['secretKey'],
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
                            'options' => $apps
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '适配过滤组：',
                        'attr' => [
                            'name' => 'filterId',
                            'value' => $detail['filterId'],
                            'options' => $filters
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '秘钥描述：',
                        'attr' => [
                            'name' => 'description',
                            'value' => $detail['description'],
                            'placeholder' => '秘钥的说明，请尽可能的简短清晰！'
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
            $keysObj = Keys::get([$this->primaryKey => $id]);
            if( is_null($keysObj) ){
                $this->error('当前秘钥不存在','');
            }else{
                $keysObj->status = 1;
                $keysObj->save();
                $this->success('操作成功', url('KeyManager/index'));
            }
        }
    }

    public function close(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $keysObj = Keys::get([$this->primaryKey => $id]);
            if( is_null($keysObj) ){
                $this->error('当前秘钥不存在','');
            }else{
                $keysObj->status = 0;
                $keysObj->save();
                $this->success('操作成功', url('KeyManager/index'));
            }
        }
    }
}