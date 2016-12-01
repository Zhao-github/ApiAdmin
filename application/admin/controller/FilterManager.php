<?php
/**
 * 规则组配置
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use app\admin\model\Filter;

class FilterManager extends Base {
    public function index(){
        $cacheValue = [];
        $data = (new Filter())->where([])->select();
        foreach ( $data as $value ){
            $cacheValue[$value['id']] = $value['name'];
        }
        cache(CacheType::FILTER_LIST_KEY, $cacheValue);
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '规则组名称'
                ],
                [
                    'field' => 'month',
                    'info' => '次/月'
                ],
                [
                    'field' => 'day',
                    'info' => '次/日'
                ],
                [
                    'field' => 'hour',
                    'info' => '次/时'
                ],
                [
                    'field' => 'minute',
                    'info' => '次/分'
                ],
                [
                    'field' => 'second',
                    'info' => '次/秒'
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'FilterManager/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '启用',
                    'href' => 'FilterManager/open',
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => 'FilterManager/close',
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '编辑',
                    'href' => 'FilterManager/edit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'FilterManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
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
            $userModel = new Filter();
            $result = $userModel->allowField(true)->save($this->request->post());
            if(false === $result){
                $this->error($userModel->getError());
            }else{
                $this->success('操作成功！', url('FilterManager/index'));
            }
        }else{
            $form = [
                'formTitle' => $this->menuInfo['name'].'【请注意规则的合理性】',
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('FilterManager/add'),
                    'formId' => 'add-FilterManager-form',
                    'backUrl' => url('FilterManager/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '规则组名称',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每月请求频率：',
                        'attr' => [
                            'name' => 'month',
                            'value' => '-1',
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每日请求频率：',
                        'attr' => [
                            'name' => 'day',
                            'value' => '-1',
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每小时请求频率：',
                        'attr' => [
                            'name' => 'hour',
                            'value' => '-1',
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每分钟请求频率：',
                        'attr' => [
                            'name' => 'minute',
                            'value' => '-1',
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每秒请求频率：',
                        'attr' => [
                            'name' => 'second',
                            'value' => '-1',
                            'placeholder' => '-1'
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
            $delNum = Filter::destroy($key);
            if( $delNum ){
                $this->success('操作成功！', url('FilterManager/index'));
            }
        }
        $this->error('操作失败！');
    }

    public function edit(){
        if( $this->request->isPut() ){
            $data = $this->request->put();
            $keysModel = new Filter();
            $keysModel->allowField(true)->update($data);
            $this->success('操作成功！', url('FilterManager/index'));
        }else{
            $detail = Filter::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('FilterManager/edit'),
                    'formId' => 'edit-FilterManager-form',
                    'backUrl' => url('FilterManager/index'),
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
                        'description' => '',
                        'info' => '规则组名称',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每月请求频率：',
                        'attr' => [
                            'name' => 'month',
                            'value' => $detail['month'],
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每日请求频率：',
                        'attr' => [
                            'name' => 'day',
                            'value' => $detail['day'],
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每小时请求频率：',
                        'attr' => [
                            'name' => 'hour',
                            'value' => $detail['hour'],
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每分钟请求频率：',
                        'attr' => [
                            'name' => 'minute',
                            'value' => $detail['minute'],
                            'placeholder' => '-1'
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '请填写整数，-1表示不限制',
                        'info' => '每秒请求频率：',
                        'attr' => [
                            'name' => 'second',
                            'value' => $detail['second'],
                            'placeholder' => '-1'
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
            $keysObj = Filter::get([$this->primaryKey => $id]);
            if( is_null($keysObj) ){
                $this->error('当前规则组不存在','');
            }else{
                $keysObj->status = 1;
                $keysObj->save();
                $this->success('操作成功', url('FilterManager/index'));
            }
        }
    }

    public function close(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $keysObj = Filter::get([$this->primaryKey => $id]);
            if( is_null($keysObj) ){
                $this->error('当前规则组不存在','');
            }else{
                $keysObj->status = 0;
                $keysObj->save();
                $this->success('操作成功', url('FilterManager/index'));
            }
        }
    }
}