<?php
/**
 * Api入口
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\Controller;


use Home\ORG\ApiLog;
use Home\ORG\Filter;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class ApiController extends BaseController {

    private $apiDetail;             //api配置（API路由，是否需要登录，是否需要AccessToken）
    private $apiRequest;            //请求参数规则
    private $apiResponse;           //返回参数规则
    private $param;                 //根据API配置过滤后的传入参数
    private $header;                //http request header信息


    public function index() {
        $getArr = I('get.');
        $postArr = I('post.');

        //获取ApiInfo根据
        $this->apiDetail = S('ApiInfo_' . $getArr['hash']);
        if (!$this->apiDetail) {
            $this->apiDetail = M('ApiList')->where(array('hash' => $getArr['hash'], 'status' => 1))->find();
            S('ApiInfo_' . $getArr['hash'], json_encode($this->apiDetail));
        } else {
            $this->apiDetail = json_decode($this->apiDetail, true);
        }

        if (empty($this->apiDetail)) {
            Response::error(ReturnCode::NOT_EXISTS, '非法的API标识');
        }
        ApiLog::setApiInfo($this->apiDetail);

        $this->apiRequest = S('ApiRequest_' . $getArr['hash']);
        if (!$this->apiRequest) {
            $this->apiRequest = M('ApiFields')->where(array('hash' => $getArr['hash'], 'type' => 0))->select();
            S('ApiRequest_' . $getArr['hash'], json_encode($this->apiRequest));
        } else {
            $this->apiRequest = json_decode($this->apiRequest, true);
        }

        $this->apiResponse = S('ApiResponse_' . $getArr['hash']);
        if (!$this->apiResponse) {
            $this->apiResponse = M('ApiFields')->where(array('hash' => $getArr['hash'], 'type' => 1))->select();
            S('ApiResponse_' . $getArr['hash'], json_encode($this->apiResponse));
        } else {
            $this->apiResponse = json_decode($this->apiResponse, true);
        }

        $returnType = S('ApiReturnType_' . $getArr['hash']);
        if (!$returnType) {
            $returnType = M('ApiFields')->where(array('hash' => $getArr['hash'], 'showName' => 'data', 'type' => 1))->getField('dataType');
            S('ApiReturnType_' . $getArr['hash'], $returnType);
        }
        Response::setDataType($returnType['dataType']);

        $this->header = apache_request_headers();
        $this->header = array_change_key_case($this->header, CASE_UPPER);
        ApiLog::setHeader($this->header);

        switch ($this->apiDetail['method']) {
            case 0:
                $this->param = array_merge($getArr, $postArr);
                break;
            case 1:
                $this->param = $postArr;
                break;
            case 2:
                $this->param = $getArr;
                break;
        }
        if ($this->header['AGENT'] == 'wx') {
            $data = file_get_contents("php://input");
            $data = json_decode($data, true);
            $this->param = $data;
        }
        ApiLog::setRequest($this->param);

        if ($this->apiDetail['accessToken'] && !$this->apiDetail['isTest']) {
            $this->checkAccessToken();
        }
        if (!$this->apiDetail['isTest']) {
            $this->checkVersion();
        }
        $this->checkLogin();
        unset($getArr['hash']);
        $this->iniApi();
    }

    /**
     * 系统初始化函数（登陆状态检测，权限检测，初始化菜单）
     */
    private function iniApi() {
        $filterObj = new Filter();
        if (!$this->apiDetail['isTest']) {
            $this->checkRule();
            $filterObj->request($this->param, $this->apiRequest);
        }

        ApiLog::setRequestAfterFilter($this->param);
        list($className, $actionName) = explode('/', $this->apiDetail['apiName']);

        $moduleName = MODULE_NAME . '\\Api\\' . $className;
        $reflection = new \ReflectionClass($moduleName);
        if (!$reflection->hasMethod($actionName)) {
            Response::error(ReturnCode::EXCEPTION, '服务器端配置异常');
        }
        $method = $reflection->getMethod($actionName);
        $handle = $reflection->newInstance();
        $data = $method->invokeArgs($handle, array($this->param));
        if (!$this->apiDetail['isTest']) {
            $data = $filterObj->response($data, $this->apiResponse);
        }
        Response::success($data);
    }

    /**
     * Api接口合法性检测
     */
    private function checkAccessToken() {
        $access_token = $this->header['ACCESS-TOKEN'];
        if (!isset($access_token) || !$access_token) {
            Response::error(ReturnCode::ACCESS_TOKEN_TIMEOUT, '缺少参数access-token');
        } else {
            $appInfo = S($access_token);
            if (!$appInfo) {
                Response::error(ReturnCode::ACCESS_TOKEN_TIMEOUT, 'access-token已过期');
            }
            ApiLog::setAppInfo($appInfo);
        }
    }

    /**
     * Api版本参数校验
     */
    private function checkVersion() {
        $version = $this->header['VERSION'];
        if (!isset($version) || !$version) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少参数version');
        } else {
            if ($version != C('APP_VERSION')) {
                Response::error(ReturnCode::VERSION_INVALID, 'API版本不匹配');
            }
        }
    }

    /**
     * 检测用户登录情况  检测通过请赋予USER_INFO值
     */
    private function checkLogin() {
        if ($this->apiDetail['needLogin']) {
            if (!isset($this->header['USER-TOKEN']) || !$this->header['USER-TOKEN']) {
                Response::error(ReturnCode::AUTH_ERROR, '缺少user-token');
            }
        }
        if (isset($this->header['USER-TOKEN']) && $this->header['USER-TOKEN']) {
            $userInfo = S($this->header['USER-TOKEN']);
            if (!is_array($userInfo) || !isset($userInfo['passport_uid'])) {
                Response::error(ReturnCode::AUTH_ERROR, 'user-token不匹配');
            }
            ApiLog::setUserInfo($userInfo);
        }
    }

    /**
     * 权限检测&权限验证(暂时预留)
     */
    private function checkRule() {

    }

}