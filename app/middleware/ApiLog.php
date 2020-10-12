<?php
declare (strict_types=1);

namespace app\middleware;

use app\util\ApiLogTool;

class ApiLog {

    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
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
        ApiLogTool::setResponse($response->getData(), isset($response->getData()['code']) ? strval($response->getData()['code']) : 'null');
        ApiLogTool::setHeader($request->header());
        ApiLogTool::save();

        return $response;
    }
}
