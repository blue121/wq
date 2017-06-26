<?php
/**
 * 【超人】超级商城模块
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanTrigger {
    public static $platform_actions = array(
        'order_submit' => array(
            'title' => '订单：提交',
        ),
        'order_pay' => array(
            'title' => '订单：支付',
        ),
        'order_ship' => array(
            'title' => '订单：发货',
        ),
        'order_receive' => array(
            'title' => '订单：收货',
        ),
        'order_refund_submit' => array(
            'title' => '订单：申请售后',
        ),
        'order_refund' => array(
            'title' => '订单：售后完成',
        ),
        'comment_post' => array(
            'title' => '评价：发表',
        ),
        'shop_register' => array(
            'title' => '商户：注册',
        ),
        'partner_register' => array(
            'title' => '分销商：注册',
        ),
        'partner_getcash_submit' => array(
            'title' => '分销商：申请提现',
        ),
        'partner_getcash_pay' => array(
            'title' => '分销商：提现打款',
        ),
    );
    public static $shop_actions = array(
        'order_submit' => array(
            'title' => '订单：提交',
        ),
        'order_pay' => array(
            'title' => '订单：支付',
        ),
        'order_ship' => array(
            'title' => '订单：发货',
        ),
        'order_receive' => array(
            'title' => '订单：收货',
        ),
        'order_refund_submit' => array(
            'title' => '订单：申请售后',
        ),
        'order_refund' => array(
            'title' => '订单：售后完成',
        ),
        'comment_post' => array(
            'title' => '评价：发表',
        ),
    );
    protected $type;
    protected $debug = false;
    protected $uniacid, $shopid;
    public function __construct($type) {
        $this->type = $type;
        if (defined('SUPERMAN_DEVELOPMENT')) {
            $this->debug = true;
        }
    }
    /*
     * $params = array(
     *      action => '',
     *      [shopid] => '',
     *      [uniacid] => '',
     * )
     */
    public function send($params) {
        if (!array_key_exists($params['action'], self::$platform_actions)
            && !array_key_exists($params['action'], self::$shop_actions)) {
            WeUtility::logging('fatal', "[SupermanTrigger::send] failed, action({$params['action']}) invalid");
            return false;
        }
        $this->uniacid = $params['uniacid'];
        $this->shopid = $params['shopid'];
        if ($this->type == 'platform') {
            return $this->do_send_platform($params);
        } else if ($this->type == 'shop') {
            return $this->do_send_shop($params);
        }
    }

    private function do_send_platform($params) {
        global $_W;
        if ($params['uniacid'] < 0) {
            WeUtility::logging('fatal', "[SupermanTrigger::do_send_platform] failed, uniacid({$params['uniacid']}) invalid");
            return false;
        }

        $sql = "SELECT b.* FROM " . tablename('superman_mall_trigger') . " AS a," . tablename('superman_mall_trigger_notice') . " AS b";
        $sql .= " WHERE a.id=b.triggerid AND a.uniacid=:uniacid AND a.status=1 AND a.action=:action";
        $param = array(
            ':uniacid' => $params['uniacid'],
            ':action' => $params['action']
        );
        $notices = pdo_fetchall($sql, $param);
        if (!$notices || !is_array($notices)) { //未设置触发器
            WeUtility::logging('fatal', "[SupermanTrigger::do_send_platform] failed, not found trigger, uniacid={$params['uniacid']}");
            return false;
        }
        foreach ($notices as $notice) {
            if (!$notice['receiver']) {
                WeUtility::logging('fatal', "[SupermanTrigger::do_send_platform] failed, noticeid({$notice['id']}) receiver is null");
                continue;
            }
            if ($notice['type'] == 1) {         //微信
                $user = mc_fetch($notice['receiver'], array('nickname'));
                $vals = array(
                    'remark' => $notice['message'].(isset($params['extra_info'])?$params['extra_info']:''),
                    'action_title' => SupermanTrigger::$platform_actions[$params['action']]['title'],
                    'username' => $user['nickname'],
                    'url' => $params['url']
                );
                $this->_send_tmplmsg($notice['receiver'], $vals);
            } else if ($notice['type'] == 2) {  //短信
                $this->_send_sms($notice['receiver'], $notice['message'], array(
                    'action' => $params['action'],
                ));
            }
        }
        return true;
    }

    private function do_send_shop($params) {
        global $_W;
        if ($params['shopid'] < 0) {
            WeUtility::logging('fatal', "[SupermanTrigger::do_send_shop] failed, shopid({$params['shopid']}) invalid");
            return false;
        }

        $sql = "SELECT b.* FROM " . tablename('superman_mall_shop_trigger') . " AS a," . tablename('superman_mall_shop_trigger_notice') . " AS b";
        $sql .= " WHERE a.id=b.triggerid AND a.shopid=:shopid AND a.status=1 AND a.action=:action";
        $param = array(
            ':shopid' => $params['shopid'],
            ':action' => $params['action']
        );
        $notices = pdo_fetchall($sql, $param);
        if (!$notices || !is_array($notices)) { //未设置触发器
            WeUtility::logging('fatal', "[SupermanTrigger::do_send_shop] failed, shopid({$params['shopid']}) notice is null");
            return false;
        }
        foreach ($notices as $notice) {
            if (!$notice['receiver']) {
                WeUtility::logging('fatal', "[SupermanTrigger::do_send_shop] failed, noticeid({$notice['id']}) receiver is null");
                continue;
            }
            if ($notice['type'] == 1) {         //微信
                $user = mc_fetch($notice['receiver'], array('nickname'));
                $vals = array(
                    'remark' => $notice['message'].(isset($params['extra_info'])?$params['extra_info']:''),
                    'action_title' => SupermanTrigger::$shop_actions[$params['action']]['title'],
                    'username' => $user['nickname'],
                    'url' => $params['url']
                );
                $this->_send_tmplmsg($notice['receiver'], $vals);
            } else if ($notice['type'] == 2) {  //短信
                $this->_send_sms($notice['receiver'], $notice['message'], array(
                    'action' => $params['action'],
                ));
            }
        }
        return true;
    }

    // init app & web & other
    private function _init_account() {
        global $_W, $_GPC;
        static $account = null;
        if (empty($_W['account'])) {
            if (isset($_W['uniacid']) && $_W['uniacid']) {
                $_W['account'] = uni_fetch($_W['uniacid']);
            } else if (isset($_W['acid']) && $_W['acid']) {
                $_W['account'] = account_fetch($_W['acid']);
            } else {
                return error(-1, '初始化失败，缺少acid||uniacid参数');
            }
        }
        if ($_W['account']['level'] < 3) {
            return error(-1, '公众号没有经过认证');
        }
        if (!is_null($account)) {
            return $account;
        }
        $account = WeAccount::create();
        if (is_null($account)) {
            return error(-1, '创建公众号操作对象失败');
        }
        return $account;
    }
    
    //发送触发器模板消息
    private function _send_tmplmsg ($openid, $vals = array()) {
        global $_W;
        //$_W['account']参数存在为空的情况，必须先初始化
        $account = $this->_init_account();
        if ($_W['account']['level'] != 4) { //服务号（已认证）
            WeUtility::logging('fatal', '[SupermanTrigger::_send_tmplmsg] 非认证服务号没有模板消息权限, name='.$_W['account']['name'].', level='.$_W['account']['level']);
            return false;
        }
        if (!$openid) {
            WeUtility::logging('fatal', '[SupermanTrigger::_send_tmplmsg] 非法参数，openid is null');
            return false;
        }
        $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_TRIGGER);
        if (!$setting || $setting['tmpl_id'] == '') {
            WeUtility::logging('fatal', '[SupermanTrigger::_send_tmplmsg] 没有配置模板消息');
            return false;
        }
        $message = array(   //初始化消息体
            'template_id' => $setting['tmpl_id'],
            'postdata' => array(),
            'url' => '',
            'topcolor' => '#008000',
        );
        $vars = array(
            '{管理员}' => $vals['username'],
            '{动作}' => $vals['action_title'],
            '{时间}' => date('Y-m-d H:i:s', TIMESTAMP),
            '{备注}' => $vals['remark'],
        );
        //替换消息中的变量
        $tmpl_variable = explode("\n", $setting['tmpl_variable']);
        foreach ($tmpl_variable as $line) {
            $arr = explode("=", trim($line));
            $arr = array_map('trim', $arr);
            $value = $arr[1];
            foreach ($vars as $k=>$v) {
                if (strpos($value, $k) !== false) {
                    $value = str_replace($k, $v, $value);
                }
            }
            $message['postdata'][$arr[0]] = array(
                'value' => $value,
                'color' => '#173177',
            );
        }
        //发送模板消息
        $ret = $account->sendTplNotice($openid, $message['template_id'], $message['postdata'], $vals['url'], $message['topcolor']);
        if ($ret !== true) {
            WeUtility::logging("fatal", "[SupermanTrigger::_send_tmplmsg] 模板消息发送失败：openid={$openid}, ret=".var_export($ret, true).", message=" . var_export($message, true));
            return false;
        }
        if (defined('ONLINE_DEVELOPMENT')) {
            WeUtility::logging("trace", "[SupermanTrigger::_send_tmplmsg] 模板消息发送成功：template_id={$message['template_id']}, openid={$openid}, message=".var_export($message, true));
        }
        return true;
    }

    private function _send_sms($mobile, $message, $vars = array()) {
        global $_W;
        if (empty($mobile) || empty($message)) {
            WeUtility::logging('fatal', '[SupermanTrigger::_send_sms] params error, mobile='.$mobile.', message='.$message);
            return false;
        }
        $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
        if (!isset($sms['setting']['shop_trigger']['switch']) || !$sms['setting']['shop_trigger']['switch']) {
            WeUtility::logging('fatal', '[SupermanTrigger::_send_sms] close');
            return false;
        }
        $provider = $sms['setting']['shop_trigger']['provider'];
        if ($provider == 'chanzor' || $provider == 'smsbao' || $provider == 'heysky') {
            //触发器如果是海客，让商户自己填入国家区号
            if ($vars) {
                $username = '';
                if ($_W['uid']) {
                    $username = $_W['user']['username'];
                }
                $arr = array(
                    '{管理员}' => $username,
                    '{动作}' => SupermanTrigger::$shop_actions[$vars['action']]['title'],
                    '{签名}' => $sms['account'][$provider]['signature'],
                );
                foreach ($arr as $k=>$v) {
                    if (strpos($message, $k) !== false) {
                        $message = str_replace($k, $v, $message);
                    }
                }
            }
            $account = $sms['account'][$provider];
            $check_total = isset($this->shopid) && $this->shopid > 0?true:false;
            $ret = Sms::init($provider, $account)->send($mobile, $message, array(), $check_total, array('shopid' => $this->shopid));
            if ($ret !== true) {
                WeUtility::logging('fatal', '[SupermanTrigger::_send_sms] failed, provider='.$provider.', account='.var_export($account, true).', mobile='.$mobile.', message='.$message);
                return false;
            }
        } else if ($provider == 'alidayu') {
            $template['id'] = $sms['template']['alidayu']['trigger']['id'];
            $username = '';
            if ($_W['uid']) {
                $username = $_W['user']['username'];
            }
            $template['variables'] = json_encode(array(
                'name' => $username,
                'action' => SupermanTrigger::$shop_actions[$vars['action']]['title'],
            ));
            $account = $sms['account'][$provider];
            $check_total = isset($this->shopid) && $this->shopid > 0?true:false;
            $ret = Sms::init($provider, $account)->send($mobile, '', $template, $check_total, array('shopid' => $this->shopid));
            if ($ret !== true) {
                WeUtility::logging('fatal', '[SupermanTrigger::_send_sms] failed, provider='.$provider.', account='.var_export($account, true).', mobile='.$mobile.', template='.var_export($template, true));
                return false;
            }
        }
        if ($this->debug) {
            WeUtility::logging('fatal', '[SupermanTrigger::_send_sms] success, provider='.$provider.', mobile='.$mobile);
        }
        return true;
    }
}

/*
 * Usage: Trigger::init(string type)->send(array params);
 */
if (!class_exists('Trigger')) {
    class Trigger {
        static private $_instances;
        public static function init($type) {
            if (!isset(Trigger::$_instances[$type])) {
                if (!in_array($type, array('platform', 'shop'))) {
                    trigger_error('触发器类型参数错误', E_USER_ERROR);
                }
                Trigger::$_instances[$type] = new SupermanTrigger($type);
            }
            return Trigger::$_instances[$type];
        }
    }
} else {
    exit('class Trigger conflict');
}