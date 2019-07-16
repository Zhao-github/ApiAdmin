<?php
/**
 *
 * @since   2019-07-16
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\api\controller;


use app\model\LibPhone;
use Curl\Curl;

class Phone extends Base {

    public function area() {
        $phone = $this->request->param('phone');

        $return = [
            'isp'          => '无法识别归属地',
            'isp_city'     => '无法识别归属地',
            'isp_province' => '无法识别归属地',
            'isMobile'     => false
        ];
        if ($phone) {
            $isMobile = true;
            $newPhone = substr(trim($phone), -11);
            $newPhoneArr = str_split($newPhone);
            if ($newPhoneArr[0] != 1) {
                $isMobile = false;
                if ($newPhoneArr[0] != 0) {
                    $newPhone = substr($phone, -12);
                }
            }

            if ($isMobile) {
                $pre = substr($newPhone, 0, 7);
                $ispInfo = LibPhone::get(['phone' => $pre]);
            } else {
                $preThree = substr($newPhone, 0, 3);
                if ($preThree <= '030') {
                    $ispInfo = LibPhone::get(['city_code' => $preThree]);
                } else {
                    $preFour = substr($newPhone, 0, 4);
                    $ispInfo = LibPhone::get(['city_code' => $preFour]);
                }
            }

            if ($ispInfo) {
                $return = [
                    'isp'          => $ispInfo['isp'],
                    'isp_city'     => $ispInfo['city'],
                    'isp_province' => $ispInfo['province'],
                    'post_code'    => $ispInfo['post_code'],
                    'city_code'    => $ispInfo['city_code'],
                    'area_code'    => $ispInfo['area_code'],
                    'isMobile'     => $isMobile
                ];
            } else {
                $curl = new Curl();
                $curl->get('http://mobsec-dianhua.baidu.com/dianhua_api/open/location?tel=' . $newPhone);

                if (!$curl->error) {
                    if ($curl->response->response && $curl->response->response->$newPhone) {
                        $detail = $curl->response->response->$newPhone;

                        $return = [
                            'isp'          => $detail->detail->operator,
                            'isp_city'     => $detail->detail->area[0]->city,
                            'isp_province' => $detail->detail->province,
                            'isMobile'     => $isMobile
                        ];

                        $hasInfo = LibPhone::get([
                            'province' => $return['isp_province'],
                            'city'     => $return['isp_city']
                        ]);
                        if ($hasInfo) {
                            LibPhone::create([
                                'phone'     => substr($newPhone, 0, 7),
                                'pref'      => substr($newPhone, 0, 3),
                                'province'  => $return['isp_province'],
                                'city'      => $return['isp_city'],
                                'isp'       => $return['isp'],
                                'post_code' => $hasInfo->post_code,
                                'city_code' => $hasInfo->city_code,
                                'area_code' => $hasInfo->area_code
                            ]);
                            $return['post_code'] = $hasInfo->post_code;
                            $return['city_code'] = $hasInfo->city_code;
                            $return['area_code'] = $hasInfo->area_code;
                        } else {
                            LibPhone::create([
                                'phone'     => substr($newPhone, 0, 7),
                                'pref'      => substr($newPhone, 0, 3),
                                'province'  => $return['isp_province'],
                                'city'      => $return['isp_city'],
                                'isp'       => $return['isp'],
                                'post_code' => '',
                                'city_code' => '',
                                'area_code' => ''
                            ]);
                        }
                    }
                }
            }
        }

        return $this->buildSuccess($return);
    }

}
