<?php

namespace app\http\middleware;

use app\util\ApiLogTool;

class ApiLog {

    /**
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $response = $next($request);
        $requestInfo = $request->param();
        unset($requestInfo['API_CONF_DETAIL']);
        unset($requestInfo['APP_CONF_DETAIL']);

        ApiLogTool::setApiInfo($request->API_CONF_DETAIL);
        ApiLogTool::setAppInfo($request->APP_CONF_DETAIL);
        ApiLogTool::setRequest($requestInfo);
        ApiLogTool::setResponse($response->getData(), isset($response->getData()['code']) ? $response->getData()['code'] : 'null');
        ApiLogTool::setHeader($request->header());
        ApiLogTool::save();

        return $response;
    }
}
