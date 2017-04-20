<?php
/**
 * JPush推送实现
 * @since   2017/03/24 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK\JPush;


use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Push {
    private $platform = 'all';
    private $tag = null;
    private $tagAnd = null;
    private $alias = null;
    private $registrationId = null;
    private $extras = null;
    private $notificationAlert = null;
    private $androidNotification = null;
    private $iosNotification = null;
    private $message = null;
    private $options = null;

    /**
     * 增加推送到苹果
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function ios() {
        if (is_array($this->platform)) {
            if (!in_array(Config::PLATFORM_IOS, $this->platform)) {
                array_push($this->platform, Config::PLATFORM_IOS);
            }
        } else {
            $this->platform = array(Config::PLATFORM_IOS);
        }

        return $this;
    }

    /**
     * 推送至安卓
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function android() {
        if (is_array($this->platform)) {
            if (!in_array(Config::PLATFORM_ANDROID, $this->platform)) {
                array_push($this->platform, Config::PLATFORM_ANDROID);
            }
        } else {
            $this->platform = array(Config::PLATFORM_ANDROID);
        }

        return $this;
    }

    /**
     * 设置推送tag，仅允许传入字符串和一维索引数组
     * @param string|array $param
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function addTag($param) {
        if (is_null($this->tag)) {
            if (is_array($this->tag)) {
                $this->tag = $param;
            } else {
                $this->tag = array($param);
            }
        } else {
            if (is_array($param)) {
                foreach ($param as $item) {
                    if (!in_array($item, $this->tag)) {
                        array_push($this->tag, $item);
                    }
                }
            } else {
                if (!in_array($param, $this->tag)) {
                    array_push($this->tag, $param);
                }
            }
        }

        return $this;
    }

    /**
     * 设置推送tag_and，仅允许传入字符串和一维索引数组
     * @param string|array $param
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function addTagAnd($param) {
        if (is_null($this->tagAnd)) {
            if (is_array($this->tagAnd)) {
                $this->tagAnd = $param;
            } else {
                $this->tagAnd = array($param);
            }
        } else {
            if (is_array($param)) {
                foreach ($param as $item) {
                    if (!in_array($item, $this->tagAnd)) {
                        array_push($this->tagAnd, $item);
                    }
                }
            } else {
                if (!in_array($param, $this->tagAnd)) {
                    array_push($this->tagAnd, $param);
                }
            }
        }

        return $this;
    }

    /**
     * 设置推送alias，仅允许传入字符串和一维索引数组
     * @param string|array $param
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function addAlias($param) {
        if (is_null($this->alias)) {
            if (is_array($this->alias)) {
                $this->alias = $param;
            } else {
                $this->alias = array($param);
            }
        } else {
            if (is_array($param)) {
                foreach ($param as $item) {
                    if (!in_array($item, $this->alias)) {
                        array_push($this->alias, $item);
                    }
                }
            } else {
                if (!in_array($param, $this->alias)) {
                    array_push($this->alias, $param);
                }
            }
        }

        return $this;
    }

    /**
     * 设置推送registration_id，仅允许传入字符串和一维索引数组
     * @param string|array $param
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function addRegistrationId($param) {
        if (is_null($this->registrationId)) {
            if (is_array($this->registrationId)) {
                $this->registrationId = $param;
            } else {
                $this->registrationId = array($param);
            }
        } else {
            if (is_array($param)) {
                foreach ($param as $item) {
                    if (!in_array($item, $this->registrationId)) {
                        array_push($this->registrationId, $item);
                    }
                }
            } else {
                if (!in_array($param, $this->registrationId)) {
                    array_push($this->registrationId, $param);
                }
            }
        }

        return $this;
    }

    /**
     * 设置公告消息
     * @param string $param
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function setNotificationAlert($param) {
        if (!is_string($param)) {
            Response::error(ReturnCode::EXCEPTION, 'NotificationAlert 必须是字符串');
        }
        $this->notificationAlert = $param;

        return $this;
    }

    /**
     * 设置推送addExtras，新增加额外字段
     * @param array $param
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function addExtras($param) {
        if (is_null($this->extras)) {
            if (is_array($param)) {
                $this->extras = $param;
            }
        } else {
            if (is_array($param)) {
                array_merge($this->extras, $param);
            }
        }

        return $this;
    }

    /**
     * 设置IOS的通知消息体
     * @param       $alert
     * @param array $notification
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function iosNotification($alert = null, $notification = array()) {
        $ios = array();
        if (!is_null($alert)) {
            $ios['alert'] = (is_string($alert) || is_array($alert)) ? $alert : '';
        }
        if (!empty($notification)) {
            if (isset($notification['sound']) && is_string($notification['sound'])) {
                $ios['sound'] = $notification['sound'];
            }
            if (isset($notification['badge'])) {
                $ios['badge'] = (int)$notification['badge'] ? $notification['badge'] : 0;
            }
            if (isset($notification['content-available']) && is_bool($notification['content-available']) && $notification['content-available']) {
                $ios['content-available'] = $notification['content-available'];
            }
            if (isset($notification['mutable-content']) && is_bool($notification['mutable-content']) && $notification['mutable-content']) {
                $ios['mutable-content'] = $notification['mutable-content'];
            }
            if (isset($notification['category']) && is_string($notification['category'])) {
                $ios['category'] = $notification['category'];
            }
            if (isset($notification['extras']) && is_array($notification['extras']) && !empty($notification['extras'])) {
                $ios['extras'] = $notification['extras'];
            }
        }
        if (!isset($ios['sound'])) {
            $ios['sound'] = '';
        }
        if (!isset($ios['badge'])) {
            $ios['badge'] = '+1';
        }
        $this->iosNotification = $ios;

        return $this;
    }

    /**
     * 设置Android的通知消息体
     * @param       $alert
     * @param array $notification
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function androidNotification($alert = null, array $notification = array()) {
        $android = array();
        if (!is_null($alert)) {
            $android['alert'] = is_string($alert) ? $alert : '';
        }
        if (!empty($notification)) {
            if (isset($notification['title']) && is_string($notification['title'])) {
                $android['title'] = $notification['title'];
            }
            if (isset($notification['builder_id']) && is_int($notification['builder_id'])) {
                $android['builder_id'] = $notification['builder_id'];
            }
            if (isset($notification['extras']) && is_array($notification['extras']) && !empty($notification['extras'])) {
                $android['extras'] = $notification['extras'];
            }
            if (isset($notification['priority']) && is_int($notification['priority'])) {
                $android['priority'] = $notification['priority'];
            }
            if (isset($notification['category']) && is_string($notification['category'])) {
                $android['category'] = $notification['category`'];
            }
            if (isset($notification['style']) && is_int($notification['style'])) {
                $android['style'] = $notification['style'];
            }
            if (isset($notification['big_text']) && is_string($notification['big_text'])) {
                $android['big_text'] = $notification['big_text'];
            }
            if (isset($notification['inbox']) && is_array($notification['inbox'])) {
                $android['inbox'] = $notification['inbox'];
            }
            if (isset($notification['big_pic_path']) && is_string($notification['big_pic_path'])) {
                $android['big_pic_path'] = $notification['big_pic_path'];
            }
        }
        $this->androidNotification = $android;

        return $this;
    }

    /**
     * 自定义消息体设置
     * @param       $msgContent
     * @param array $msg
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function message($msgContent, array $msg = array()) {
        if (is_string($msgContent)) {
            $message = array();
            $message['msg_content'] = $msgContent;
            if (!empty($msg)) {
                if (isset($msg['title']) && is_string($msg['title'])) {
                    $message['title'] = $msg['title'];
                }
                if (isset($msg['content_type']) && is_string($msg['content_type'])) {
                    $message['content_type'] = $msg['content_type'];
                }
                if (isset($msg['extras']) && is_array($msg['extras']) && !empty($msg['extras'])) {
                    $message['extras'] = $msg['extras'];
                }
            }
            $this->message = $message;
        }

        return $this;
    }

    /**
     * 额外可选配置参数
     * @param array $opts
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return $this
     */
    public function options(array $opts = array()) {
        $options = array();
        if (isset($opts['sendno']) && is_int($opts['sendno'])) {
            $options['sendno'] = $opts['sendno'];
        }
        if (isset($opts['time_to_live']) && is_int($opts['time_to_live']) && $opts['time_to_live'] <= 864000 && $opts['time_to_live'] >= 0) {
            $options['time_to_live'] = $opts['time_to_live'];
        }
        if (isset($opts['override_msg_id']) && is_long($opts['override_msg_id'])) {
            $options['override_msg_id'] = $opts['override_msg_id'];
        }
        if (isset($opts['apns_production']) && is_bool($opts['apns_production'])) {
            $options['apns_production'] = $opts['apns_production'];
        } else {
            $options['apns_production'] = false;
        }
        if (isset($opts['big_push_duration']) && is_int($opts['big_push_duration']) && $opts['big_push_duration'] <= 1400 && $opts['big_push_duration'] >= 0) {
            $options['big_push_duration'] = $opts['big_push_duration'];
        }
        $this->options = $options;

        return $this;
    }

    /**
     * 根据配置，整合数据
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    private function buildData() {
        $payload = array();
        $payload["platform"] = $this->platform;

        $audience = array();
        if (!is_null($this->tag)) {
            $audience["tag"] = $this->tag;
        }
        if (!is_null($this->tagAnd)) {
            $audience["tag_and"] = $this->tagAnd;
        }
        if (!is_null($this->alias)) {
            $audience["alias"] = $this->alias;
        }
        if (!is_null($this->registrationId)) {
            $audience["registration_id"] = $this->registrationId;
        }
        if (count($audience) <= 0) {
            $payload["audience"] = 'all';
        } else {
            $payload["audience"] = $audience;
        }

        $notification = array();
        if (!is_null($this->notificationAlert)) {
            $notification['alert'] = $this->notificationAlert;
        }
        if (!is_null($this->androidNotification)) {
            $notification['android'] = $this->androidNotification;
            if (is_null($this->androidNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    Response::error(ReturnCode::EXCEPTION, 'Android alert can not be null');
                }
            } else {
                $notification['android']['alert'] = $this->androidNotification['alert'];
            }
        }
        if (!is_null($this->extras)) {
            $notification['android']['extras'] = $this->extras;
        }


        if (!is_null($this->iosNotification)) {
            $notification['ios'] = $this->iosNotification;
            if (is_null($this->iosNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    Response::error(ReturnCode::EXCEPTION, 'iOS alert can not be null');
                }
            } else {
                $notification['ios']['alert'] = $this->iosNotification['alert'];
            }
        }
        if (!is_null($this->extras)) {
            $notification['ios']['extras'] = $this->extras;
        }

        if (count($notification) > 0) {
            $payload['notification'] = $notification;
        }
        if (count($this->message) > 0) {
            $payload['message'] = $this->message;
        }
        if (!array_key_exists('notification', $payload) && !array_key_exists('message', $payload)) {
            Response::error(ReturnCode::EXCEPTION, 'notification and message can not all be null');
        }
        if (!is_null($this->options)) {
            $payload['options'] = $this->options;
        }

        return $payload;
    }

    public function send() {
        return Http::post(Config::API_PUSH, $this->buildData());
    }
}