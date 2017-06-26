<?php
/**
 * 【超人】超级商城模块，海客接口
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanSms_heysky extends SupermanSms {
    protected $statusStr = array(
        "000" => "短信发送成功",
        "0101" => "无效的command参数",
        "0100" => "请求参数错误",
        "0104" => "账号信息错误",
        "0106" => "账号密码错误",
        "0110" => "目标号码格式错误或群发号码数量超过100个",
        "0600" => "未知错误",
    );
    public function __construct() {
        parent::__construct();
        if (defined('SUPERMAN_DEVELOPMENT')) {
            $this->debug = true;
        }
    }
    public function send($mobile, $message, $template = array(), $check_total = false, $extra = array()) {
        //检查短信条数
        if ($check_total && !$this->check_total($extra['shopid'])) {
            WeUtility::logging('fatal', '[SupermanSms_heysky::send] check_total failed, shopid='.$extra['shopid']);
            return false;
        }

        $request = array(
            'command' => 'MT_REQUEST',
            'cpid' => $this->account['username'],
            'cppwd' => $this->account['password'],
            'da' => $extra['areacode'].$mobile,
            'dc' => 15,
            'sm' => $this->_santoEncode($message, 15),
        );
        $sendurl = $this->account['url'].'?'.http_build_query($request);

        load()->func('communication');
        $ret = ihttp_get($sendurl);
        parse_str($ret['content'], $arr);
        WeUtility::logging('trace', 'arr='.var_export($arr, true));
        if (!is_error($arr) && $arr['mterrcode'] == '000') {
            if ($this->debug) {
                WeUtility::logging('trace', "[SupermanSms_heysky::send] success, mobile={$mobile}, message={$message}");
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
                        WeUtility::logging('fatal', "[SupermanSms_heysky::send] decrement_total failed, shopid={$extra['shopid']}");
                    }
                }
            }
            return true;
        } else {
            WeUtility::logging('fatal', "[SupermanSms_heysky::send] failed, mobile={$mobile}, statusStr={$this->statusStr[$arr['mterrcode']]}, sendurl={$sendurl}");
            return isset($this->statusStr[$arr['mterrcode']])?$this->statusStr[$arr['mterrcode']]:null;
        }
    }

    private function _santoEncode($message, $encode_code = 15) {
        $code2Encodetype = array(
            0   =>'ISO-8859-1',
            8	=>'UTF-16BE',
            15  =>'GBK',
        );

        if(empty($code2Encodetype[$encode_code])) {
            throw new Exception('Encode_type Error');
        }

        $message = mb_convert_encoding($message, $code2Encodetype[$encode_code],'auto');
        return bin2hex($message);
    }

    public function balance() {
        /*$url = 'http://www.smsbao.com/query?u=%s&p=%s';
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
        return -1;*/
    }
}