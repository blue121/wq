<?php
/**
 * 【超人】超级商城模块
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanSms {
    public static $providers = array(
        'chanzor' => array(
            'title' => '畅卓',
            'url' => 'http://www.chanzor.com/',
        ),
        'smsbao' => array(
            'title' => '短信宝',
            'url' => 'http://www.cocsms.com/',
        ),
        'alidayu' => array(
            'title' => '阿里大鱼',
            'url' => 'http://www.alidayu.com/',
        ),
        'heysky' => array(
            'title' => '海客',
            'url' => 'http://www.heysky.com/'
        )
    );
    public static $templates = array(
        'verifycode' => array(
            'title' => '验证码',
        ),
        'alidayu_verifycode' => array(
            'title' => '验证码（阿里大鱼）',
        ),
    );
    public $debug = false;
    public $account = array(
        'provider' => '',
        'url' => '',
        'username' => '',
        'password' => '',
        'signature' => '',
        'app_key' => '', //alidayu
        'app_secret' => '', //alidayu
    );
    public $provider;
    public function __construct() {}
    public function send($mobile, $message, $template = array(), $check_total = false, $extra = array()) {
        return false;
    }
    public function check_total($shopid) {
        if (!$shopid) {
            WeUtility::logging('fatal', '[SupermanSms::check_total] failed, shopid is null');
            return false;
        }
        $shop = M::t('superman_mall_shop')->fetch($shopid);
        if (!$shop) {
            WeUtility::logging('fatal', '[SupermanSms::check_total] failed, shop not found, shopid='.$shopid);
            return false;
        }
        if ($shop['sms_total'] <= 0) {
            WeUtility::logging('fatal', '[SupermanSms::check_total] check failed, sms_total='.$shop['sms_total']);
            return false;
        }
        if ($this->debug) {
            WeUtility::logging('trace', '[SupermanSms::check_total] check ok, sms_total='.$shop['sms_total']);
        }
        return true;
    }
    public function decrement_total($shopid) {
        if (!$shopid) {
            WeUtility::logging('fatal', '[SupermanSms::decrement_total] failed, shopid is null');
            return false;
        }
        $ret = M::t('superman_mall_shop')->decrement(array(
            'sms_total' => 1,
        ), array('id' => $shopid));
        if ($ret !== false) {
            if ($this->debug) {
                WeUtility::logging('trace', '[SupermanSms::decrement_total] success, shopid='.$shopid);
            }
            return true;
        }
        return false;
    }
    public function balance() {
        return -1;
    }
}

/*
 * Usage: Sms::init(string provider, array account)->send(string mobile, string message, [bool check_total], [array extra]);
 */
if (!class_exists('Sms')) {
    class Sms {
        static private $_instances;
        public static function init($provider, $account) {
            if (!isset(Sms::$_instances[$provider])) {
                if (empty($account) || !array_key_exists($provider, SupermanSms::$providers)) {
                    WeUtility::logging('fatal', '未配置短信接口');
                    Sms::$_instances[$provider] = new SupermanSms($account); //容错处理
                } else {
                    switch ($provider) {
                        case 'chanzor':
                            $account['url'] = $account['url']?$account['url']:'http://sms.chanzor.com:8001/sms.aspx';break;
                        case 'smsbao':
                            $account['url'] = $account['url']?$account['url']:'http://www.cocsms.com/openapi/';break;
                        case 'heysky':
                            $account['url'] = $account['url']?$account['url']:'http://api2.santo.cc/submit';break;
                    }
                    $classname = "SupermanSms_{$provider}";
                    $filename = MODULE_ROOT.'/class/smsapi/'.$provider.'.class.php';
                    if (file_exists($filename)) {
                        require $filename;
                        Sms::$_instances[$provider] = new $classname();
                        Sms::$_instances[$provider]->account = $account;
                        Sms::$_instances[$provider]->provider = $provider;
                    } else {
                        trigger_error('短信接口 "'.'class/smsapi/'.$provider.'.class.php" 不存在', E_USER_ERROR);
                    }
                }
            }
            return Sms::$_instances[$provider];
        }
    }
} else {
    exit('class Sms conflict');
}