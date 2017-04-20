<?php
/**
 * @since   2017-04-20
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore;


use Home\ApiStore\ApiSDK\TaoBao\Http;
use Home\ApiStore\ApiSDK\TaoBaoSDK;

class DayuSms {

    private $appInfo = array(
        'secretKey' => '880ad9f6daae467f2ec2e11f50932f8f',
        'appKey' => '23762660',
        'url' => 'http://gw.api.taobao.com/router/rest'
    );

    private $apiRule = array(
        'sms_type' => 'normal',
        'sms_free_sign_name' => '',
        'rec_num' => '',
        'sms_template_code' => ''
    );

    public function send(){
        $sdk = new TaoBaoSDK($this->appInfo, 'alibaba.aliqin.fc.sms.num.send');
        $sdk->buildSysParams();
        $sdk->buildApiParams(array(
            'sms_free_sign_name' => 'Api管理后台',
            'rec_num' => '17366005512',
            'sms_template_code' => 'SMS_62650093'
        ), $this->apiRule);
        $sdk->buildUrl();
        return Http::get($sdk->url);
    }
}