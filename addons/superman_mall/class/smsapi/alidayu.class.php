<?php
/**
 * 【超人】超级商城模块，阿里大鱼短信接口
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanSms_alidayu extends SupermanSms {
    public function __construct() {
        parent::__construct();
        if (defined('SUPERMAN_DEVELOPMENT')) {
            $this->debug = true;
        }
    }
    public function send($mobile, $message, $template = array(), $check_total = false, $extra = array()) {
        if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
            WeUtility::logging('fatal', '[SupermanSms_alidayu::send] failed, mobile('.$mobile.') invalid');
            return false;
        }
        //检查短信条数
        if ($check_total && !$this->check_total($extra['shopid'])) {
            WeUtility::logging('fatal', '[SupermanSms_alidayu::send] check_total failed, shopid='.$extra['shopid']);
            return false;
        }
        require_once MODULE_ROOT.'/class/smsapi/alidayu/TopSdk.php';
        $c = new TopClient;
        $c->appkey = $this->account['app_key'];
        $c->secretKey = $this->account['app_secret'];
        $c->format = 'json';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        //$req->setExtend('123456');
        $req->setSmsType('normal');
        $req->setSmsFreeSignName($this->account['signature']);
        $req->setSmsParam($template['variables']);
        $req->setRecNum($mobile);
        $req->setSmsTemplateCode($template['id']);
        $resp = $c->execute($req);
        if (isset($resp->result) && isset($resp->result->err_code) && $resp->result->err_code == '0') {
            if ($this->debug) {
                WeUtility::logging('trace', "[SupermanSms_alidayu::send] success, mobile={$mobile}, template_id={$template['id']}, signature={$this->account['signature']}, variables=".var_export($template['variables'], true));
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
                                'id' => $template['id'],
                                'variables' => $template['variables'],
                                'signature' => $this->account['signature'],
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
            WeUtility::logging('fatal', "[SupermanSms_alidayu::send] failed, return code={$resp->code}, msg={$resp->msg}, sub_code={$resp->sub_code}, sub_msg={$resp->sub_msg}, template_id={$template['id']}, signature={$this->account['signature']}, variables=".var_export($template['variables'], true));
            return $resp->msg.', '.$resp->sub_msg;
        }
    }

    public function balance() {
        return -1;
    }
}