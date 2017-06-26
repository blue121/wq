<?php
/**
 * 【超人】超级商城模块，畅卓短信接口
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanSms_chanzor extends SupermanSms {
    public function __construct() {
        parent::__construct();
        if (defined('SUPERMAN_DEVELOPMENT')) {
            $this->debug = true;
        }
    }
    public function send($mobile, $message, $template = array(), $check_total = false, $extra = array()) {
        if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
            WeUtility::logging('fatal', '[SupermanSms_chanzor::send] failed, mobile('.$mobile.') invalid');
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
        $str = "action=send&userid=&account=%s&password=%s&mobile=%s&sendTime=&content=%s";
        $post_data = sprintf($str, $this->account['username'], $this->account['password'], $mobile, $message);
        $xml = $this->post($this->account['url'], $post_data);
        if ($xml->returnstatus == 'Success') {
            if ($this->debug) {
                WeUtility::logging('trace', "[SupermanSms_chanzor::send] success, mobile={$mobile}, message={$message}");
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
            WeUtility::logging('fatal', "[SupermanSms_chanzor::send] failed, mobile={$mobile}, message={$message}, result={$xml->message}, post_data={$post_data}");
            return $xml->message;
        }
    }

    public function balance() {
        $str = "action=overage&userid=&account=%s&password=%s";
        $post_data = sprintf($str, $this->account['username'], $this->account['password']);
        $xml = $this->post($this->account['url'], $post_data);
        if ($xml->returnstatus == 'Sucess') {
            if ($this->debug) {
                WeUtility::logging('trace', "[SupermanSms_chanzor::balance] success, xml=".var_export($xml, true));
            }
            return $xml->overage;
        } else {
            WeUtility::logging('fatal', "[SupermanSms_chanzor::balance] failed, result={$xml->message}, post_data={$post_data}");
            return $xml->message;
        }
    }

    private function post($url, $post_data) {
        $urls = parse_url($this->account['url']);
        $httpheader = "POST {$this->account['url']} HTTP/1.0\r\n";
        $httpheader .= "Host:{$urls['host']}\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
        $httpheader .= $post_data;
        $fd = fsockopen($urls['host'], 80);
        fwrite($fd, $httpheader);
        $ret = '';
        while(!feof($fd)) {
            $ret .= fread($fd, 128);
        }
        fclose($fd);
        $start = strpos($ret, '<?xml');
        $data = substr($ret, $start);
        $xml = simplexml_load_string($data);
        return $xml;
    }
}