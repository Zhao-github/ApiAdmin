<?php
/**
 * 用户日志管理控制器
 * @since   2016-01-25
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
namespace Home\Controller;

class UserLogController extends HomeController{

    public function index(){
        $tableField = [
            [
                'name' => 'actionName',
                'info' => '行为名称',
                'type' => 'text'
            ],
            [
                'name' => 'nickName',
                'info' => '执行者',
                'type' => 'text'
            ],
            [
                'name' => 'actionTime',
                'info' => '操作时间',
                'type' => 'text'
            ],
            [
                'name' => 'actionIp',
                'info' => '操作IP',
                'type' => 'text'
            ],
            [
                'name' => 'action',
                'info' => '操作',
                'type' => 'rightButton'
            ]
        ];
        $typeRule = [
            'rightButton' => [
                'action' => [
                    [
                        'desc' => '查看',
                        'href' => 'UserLog/show',
                        'class'=> 'secondary',
                        'param'=> '_id',
                        'icon' => 'eye',
                        'ajax' => 0,
                        'show' => ''
                    ],
                    [
                        'desc' => '删除',
                        'href' => 'UserLog/del',
                        'class'=> 'danger',
                        'param'=> '_id',
                        'icon' => 'trash',
                        'ajax' => 2,
                        'show' => ''
                    ]
                ]
            ]
        ];
        $topList = [
            'topButton' => [
                [
                    'href' => 'UserLog/del',
                    'class'=> 'am-btn-danger del-all',
                    'title'=> '删除',
                    'icon' => 'trash',
                    'ajax' => 1,
                ],
            ]
        ];
        $listNum = D('UserAction')->count();
        $listLimit = $this->_getPage($listNum, 20);
        $listInfo = D('UserAction')->order('actionTime desc')->limit($listLimit[0],$listLimit[1])->select();
        foreach( $listInfo as $key => $value ){
            $listInfo[$key]['actionTime'] = date('Y-m-d H:i:s', $value['actionTime']);
            $listInfo[$key]['actionIp'] = long2ip($value['actionIp']);
        }
        $this->_prepareListInfo( $listInfo, $tableField, $typeRule );
        $this->_prepareTopList( $topList );
        $this->assign('tableField', $tableField);
        $this->display();
    }

    public function show(){
        if( IS_GET ){
            $map['_id'] = I('get._id');
            $res = D('UserAction')->where($map)->find();
            $formData = [
                [
                    'type' => 'text',
                    'info' => '行为名称：',
                    'attr' => 'disabled',
                    'value'=> $res['actionName'],
                ],
                [
                    'type' => 'text',
                    'info' => '执行者：',
                    'attr' => 'disabled',
                    'value'=> $res['nickName'],
                ],
                [
                    'type' => 'text',
                    'info' => '操作时间：',
                    'attr' => 'disabled',
                    'value'=> date('Y-m-d H:i:s',$res['actionTime']),
                ],
                [
                    'type' => 'text',
                    'info' => '操作IP：',
                    'attr' => 'disabled',
                    'value'=> long2ip($res['actionIp']),
                ],
                [
                    'type' => 'textarea',
                    'info' => '受影响的URL：',
                    'attr' => 'readonly',
                    'value'=> $res['actionUrl'],
                ],
                [
                    'type' => 'textarea',
                    'info' => 'POST数据：',
                    'attr' => 'readonly rows="5"',
                    'value'=> $res['data'],
                ],
            ];
            $this->assign('formData', $formData);
            $this->display();
        }else{
            $this->error( L('_ERROR_ACTION_') );
        }
    }

    public function del(){
        $this->_del('UserAction', $_REQUEST['_id'], 'UserLog/index');
    }
}