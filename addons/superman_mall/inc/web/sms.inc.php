<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebSms extends Superman {
    public function __construct() {
        parent::__construct();
        parent::init();
        $this->exec();
    }
    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('verifycode')) ? $_GPC['act'] : '';
        $op = $_GPC['op'];
        if ($act == 'verifycode') {
            $this->send_verifycode();
        } else {
            $this->json(ERRNO::INVALID_REQUEST);
        }
    }
    private function send_verifycode() {
        global $_W, $_GPC;
        $mobile = $_GPC['mobile'];
        $country_id = $_GPC['country_id'];
        if ($mobile == '') {
            $this->json(ERRNO::MOBILE_NULL);
        }
        $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
        if (!$sms) {
            $this->json(ERRNO::PARAM_ERROR, '短信服务未配置');
        }
        $setting = $_GPC['setting'];
        $extra = array();
        if ($country_id) {
            $country = M::t('superman_mall_country')->fetch($country_id);
            $extra['areacode'] = $country['areacode'];
        }
        $provider = $sms['setting'][$setting]['provider'];
        $account = $sms['account'][$provider];
        if (!$account) {
            $this->json(ERRNO::PARAM_ERROR, '短信账号未配置('.$provider.')');
        }
        $item = M::t('superman_mall_sms_verify')->fetch(array(
            'openid' => $this->shop_user['openid'],
        ));
        if (!$item) {
            $verifycode = random(6, true);
            $data = array(
                'openid' => $this->shop_user['openid'],
                'mobile' => $mobile,
                'verifycode' => $verifycode,
                'ip' => CLIENT_IP,
                'count' => 1,
                'sendtime' => TIMESTAMP
            );
            $new_id = M::t('superman_mall_sms_verify')->insert($data);
            if ($new_id) {
                if ($provider == 'chanzor' || $provider == 'smsbao' || $provider == 'heysky') {
                    $tmpl = $sms['template']['verifycode'];
                    $vars = array(
                        '{签名}' => $sms['account'][$provider]['signature'],
                        '{验证码}' => $verifycode,
                    );
                    foreach ($vars as $k=>$v) {
                        if (strpos($tmpl, $k) !== false) {
                            $tmpl = str_replace($k, $v, $tmpl);
                        }
                    }
                    $ret = Sms::init($provider, $account)->send($mobile, $tmpl, array(), false, $extra);
                    if ($ret !== true) {
                        WeUtility::logging('fatal', 'send sms failed, account='.var_export($account, true).', mobile='.$mobile.', message='.$tmpl);
                        $this->json(ERRNO::SMS_SEND_FAILED);
                    }
                } else if ($provider == 'alidayu') {
                    $template['id'] = $sms['template']['alidayu']['verifycode']['id'];
                    $template['variables'] = json_encode(array(
                        $sms['template']['alidayu']['verifycode']['variable'] => $verifycode,
                    ));
                    $ret = Sms::init($provider, $account)->send($mobile, '', $template);
                    if ($ret !== true) {
                        WeUtility::logging('fatal', 'send sms failed, account='.var_export($account, true).', mobile='.$mobile.', template='.var_export($template, true));
                        $this->json(ERRNO::SMS_SEND_FAILED);
                    }
                }
                $this->json(ERRNO::OK, '验证码发送成功，请注意查收短信');
            } else {
                $this->json(ERRNO::SYSTEM_ERROR);
            }
        } else {
            if (TIMESTAMP - $item['sendtime'] < 60) {   //未过60s
                $this->json(ERRNO::SMS_SENDTIME_QUICK);
            }
            $verifycode = random(6, true);
            $data = array(
                'mobile' => $mobile,
                'verifycode' => $verifycode,
                'ip' => CLIENT_IP,
                'sendtime' => TIMESTAMP
            );
            $ret = M::t('superman_mall_sms_verify')->update($data, array('id' => $item['id']));
            if ($ret !== false) {
                M::t('superman_mall_sms_verify')->increment(array(
                    'count' => 1
                ), array('id' => $item['id']));
                if ($provider == 'chanzor' || $provider == 'smsbao' || $provider == 'heysky') {
                    $tmpl = $sms['template']['verifycode'];
                    $vars = array(
                        '{签名}' => $sms['account'][$provider]['signature'],
                        '{验证码}' => $verifycode,
                    );
                    foreach ($vars as $k=>$v) {
                        if (strpos($tmpl, $k) !== false) {
                            $tmpl = str_replace($k, $v, $tmpl);
                        }
                    }
                    $ret = Sms::init($provider, $account)->send($mobile, $tmpl, array(), false, $extra);
                    if ($ret !== true) {
                        WeUtility::logging('fatal', 'send sms failed, account='.var_export($account, true).', mobile='.$mobile.', message='.$tmpl);
                        $this->json(ERRNO::SMS_SEND_FAILED);
                    }
                } else if ($provider == 'alidayu') {
                    $template['id'] = $sms['template']['alidayu']['verifycode']['id'];
                    $template['variables'] = json_encode(array(
                        $sms['template']['alidayu']['verifycode']['variable'] => $verifycode,
                    ));
                    $ret = Sms::init($provider, $account)->send($mobile, '', $template);
                    if ($ret !== true) {
                        WeUtility::logging('fatal', 'send sms failed, account='.var_export($account, true).', mobile='.$mobile.', template='.var_export($template, true));
                        $this->json(ERRNO::SMS_SEND_FAILED);
                    }
                }
                $this->json(ERRNO::OK, '验证码发送成功，请注意查收短信');
            } else {
                $this->json(ERRNO::SYSTEM_ERROR);
            }
        }
    }
}
$obj = new Superman_mall_doWebSms;