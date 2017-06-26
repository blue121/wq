<?php
/**
 * 【超人】超级商城模块，短信宝接口
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanSms_smsbao extends SupermanSms {
    protected $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );
    public function __construct() {
        parent::__construct();
        if (defined('SUPERMAN_DEVELOPMENT')) {
            $this->debug = true;
        }
    }
    public function send($mobile, $message, $template = array(), $check_total = false, $extra = array()) {
        if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
            WeUtility::logging('fatal', '[SupermanSms_smsbao::send] failed, mobile('.$mobile.') invalid');
            return false;
        }
        //检查短信条数
        if ($check_total && !$this->check_total($extra['shopid'])) {
            WeUtility::logging('fatal', '[SupermanSms_alidayu::send] check_total failed, shopid='.$extra['shopid']);
            return false;
        }
        if ($this->account['signature'] != '' && !strexists($message, $this->account['signature'])) {
            $message .= $this->account['signature'];
        }
        if (substr($this->account['url'], -1, 1) != '/') {
            $this->account['url'] .= '/';
        }
        $sendurl = $this->account['url']."sms?u=".$this->account['username']."&p=".md5($this->account['password'])."&m=".$mobile."&c=".urlencode($message);
        load()->func('communication');
        $ret = ihttp_get($sendurl);
        if (!is_error($ret) && $ret['content'] == '0') {
            if ($this->debug) {
                WeUtility::logging('trace', "[SupermanSms_smsbao::send] success, mobile={$mobile}, message={$message}");
            }
            if ($check_total) {
                //扣短信条数
                if ($extra['shopid']) {
                    $result = $this->decrement_total($extra['shopid']);
                    if ($result !== false) {
                        M::t('superman_mall_shop_sms_sendlog')->insert(array(
                            'shopid' => $extra['shopid'],
                            'provider' => $this->provider,
                            'mobile' => $mobile,
                            'content' => iserializer(array(
                                'message' => $message,
                            )),
                            'dateline' => TIMESTAMP,
                        ));
                    } else {
                        WeUtility::logging('fatal', "[SupermanSms_alidayu::send] decrement_total failed, shopid={$extra['shopid']}");
                    }
                }
            }
            return true;
        } else {
            WeUtility::logging('fatal', "[SupermanSms_smsbao::send] failed, mobile={$mobile}, message={$message}, statusStr={$this->statusStr[$ret['content']]}, sendurl={$sendurl}");
            return isset($this->statusStr[$ret['content']])?$this->statusStr[$ret['content']]:null;
        }
    }

    public function balance() {
        $url = 'http://www.smsbao.com/query?u=%s&p=%s';
        if ($this->account['username'] && $this->account['password']) {
            $url = sprintf($url, $this->account['username'], md5($this->account['password']));
            load()->func('communication');
            $ret = ihttp_get($url);
            $arr = explode("\n", $ret['content']);
            if (!is_error($ret) && count($arr) == 2 && $arr[0] == 0) {
                $result = explode(',', $arr[1]);
                return $result[1];
            }
        }
        return -1;
    }
}