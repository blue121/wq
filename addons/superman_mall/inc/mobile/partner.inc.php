<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobilePartner extends Superman {
    protected $partner = array();
    protected $setting = array();
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $act = in_array($_GPC['act'], array('home', 'reg', 'team', 'invite',
            'ranking', 'order', 'getcash', 'poster'))?$_GPC['act']:'home';
        $method = "do_{$act}";
        if (!method_exists($this, $method)) {
            $this->message('非法请求！', '', 'warn');
        }
        //检查分销总开关
        if (!isset($this->plugin_setting['partner']) || $this->plugin_setting['partner'] == 0) {
            $this->message('非法请求！', '', 'warn');
        }
        $this->load_setting();
        if ($act == 'home' && !$_W['member']['uid']) {
            $this->do_home();
        } else {
            $this->checkauth();
            $this->init_partner();
            $this->$method();
        }
    }
    private function load_setting() {
        $this->setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
        if (!$this->setting) {
            $this->setting['text'] = array();
        }
        SupermanMallData::initPartnerCustomText($this->setting['text']);
    }
    private function init_partner() {
        global $_W, $_GPC;
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'uid' => $_W['member']['uid'],
        );
        $this->partner = M::t('superman_mall_partner')->fetch($filter);
    }
    private function check_partner_auth() {
        global $_W, $_GPC;
        if (!$this->partner) {  //未注册分销商
            @header('Location:'.$this->createMobileUrl('partner', array('act' => 'reg')));
            exit;
        }
        $this->check_subscribe();
        if ($this->partner['status'] == -2) {
            $this->message('帐号已被禁用，请联系管理员', '', 'warn');
        } else if ($this->partner['status'] == -1 && $_GPC['act'] != 'reg') {
            @header('Location:'.$this->createMobileUrl('partner', array('act' => 'reg')));
            exit;
        } else if ($this->partner['status'] == 0) {
            $this->message('帐号审核中，请稍候', '', 'info');
        }
    }
    private function check_subscribe() {
        global $_W;
        if ((empty($_W['fans']) || !$_W['fans']['follow'])) {
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);
            $subscribeurl = $setting['subscribeurl'] ? $setting['subscribeurl'] : $_W['account']['subscribeurl'];
            $tips = '请先关注公众号！';
            if ($_W['isajax']) {
                $this->json(ERRNO::NOT_LOGIN, $tips, array(
                    'url' => $subscribeurl,
                ));
            } else {
                $this->message($tips, $subscribeurl, 'info');
            }
        }
    }
    private function do_home() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $title = $this->setting['text']['partner_center'];
        if ($_W['member']['uid']) {
            $this->check_partner_auth();
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'partnerid' => $this->partner['id'],
                'daytime' => date('Ymd')
            );
            $today_commission = M::t('superman_mall_partner_stat')->fetch($filter);
        }
        include $this->template('partner/index');
    }
    private function do_reg() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $title = $this->setting['text']['partner_center'];
        $op = in_array($_GPC['op'], array('display', 'smsverify'))?$_GPC['op']:'display';
        if ($op == 'display') {
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
            //注册表单
            if (!$this->partner) {  //未注册分销商
                $_data = array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $_W['member']['uid'],
                    'openid' => $_W['openid'],
                    'groupid' => isset($this->setting['partner']['groupid'])?intval($this->setting['partner']['groupid']):0,
                    'position' => 1,
                    'status' => isset($this->setting['partner']['check']) && $this->setting['partner']['check'] == 1?0:1,
                    'createtime' => TIMESTAMP
                );
                $fromid = intval($_GPC['fromid'])?intval($_GPC['fromid']):intval($_GPC['__fromid']); //邀请人id
                $recommend = array();
                if (!empty($fromid) && $fromid != $this->partner['id']) {
                    isetcookie('__fromid', $fromid, 365*86400);
                    $recommend = M::t('superman_mall_partner')->fetch($fromid);
                    if (!empty($recommend)) {
                        $recommend['member'] = mc_fetch($recommend['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                        $_data['recommendid'] = $fromid;
                        $_data['position'] = $recommend['position']+1;
                    } else {
                        isetcookie('__fromid', 0, -1);
                        WeUtility::logging('warning', '[partner] partner not found, fromid='.$fromid);
                    }
                }
                if (!empty($fromid) && $fromid != $this->partner['id']) {  //被邀请
                    if (isset($this->setting['partner']['invite_text']) && $this->setting['partner']['invite_text']) {
                        $vars = array(
                            '{平台}' => $_W['account']['name'],
                        );
                        foreach ($vars as $k=>$v) {
                            if (strpos($this->setting['partner']['invite_text'], $k) !== false) {
                                $this->setting['partner']['invite_text'] = str_replace($k, $v, $this->setting['partner']['invite_text']);
                            }
                        }
                    } else {
                        $this->setting['partner']['invite_text'] = '我在'.$_W['account']['name'].'，邀请您一起加入平台赚佣金！';
                    }
                    if (checksubmit()) {
                        if ($this->setting['partner']['member_info']) { //需要完善资料
                            $realname = $_GPC['realname'];
                            $mobile = $_GPC['mobile'];
                            if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
                                $this->json(ERRNO::MOBILE_INVALID);
                            }
                            if (isset($sms['setting']['partner_reg']['switch']) && $sms['setting']['partner_reg']['switch']) {
                                $checkcode = $_GPC['checkcode'];
                                if ($checkcode == '') {
                                    $this->json(ERRNO::CHECKCODE_NULL);
                                }
                                $filter = array(
                                    'openid' => $_W['openid'],
                                    'mobile' => $mobile,
                                    'verifycode' => $checkcode
                                );
                                $item = M::t('superman_mall_sms_verify')->fetch($filter);
                                if (!$item) {
                                    $this->json(ERRNO::CHECKCODE_ERROR);
                                }
                                if (TIMESTAMP - $item['sendtime'] > 600) {
                                    $this->json(ERRNO::CHECKCODE_ERROR, '验证码已过期');
                                }
                            }
                        }
                        //下线条件判断
                        if (isset($this->setting['partner']['downline']) && $this->setting['partner']['downline'] > 1) {
                            $filter = array(
                                'uniacid' => $_W['uniacid'],
                                'shopid' => '# >0',
                                'uid' => $_W['member']['uid'],
                            );
                            if ($this->setting['partner']['downline'] == 2) {
                                $filter['status'] = array(3, 4);
                                $count = M::t('superman_mall_order')->count($filter);
                            } else {        //downline=3
                                $filter['status'] = 4;
                                $count = M::t('superman_mall_order')->count($filter);
                            }
                            if ($count <= 0) {
                                $_data['status'] = -1;
                            }
                        }
                        $new_id = M::t('superman_mall_partner')->insert($_data);
                        if ($new_id) {  //添加成功
                            isetcookie('__fromid', 0, -1);
                            //平台触发器
                            $extra_info = "\n\n==详情==\n";
                            $extra_info .= "昵称：{$_W['member']['nickname']}\n";
                            if (isset($realname) && $realname) {
                                $extra_info .= "姓名：{$realname}\n";
                            }
                            if (isset($mobile) && $mobile) {
                                $extra_info .= "手机：{$mobile}\n";
                            }
                            $extra_info .= "推荐人：{$recommend['realname']} {$recommend['mobile']}\n";
                            $params = array(
                                'action' => 'partner_register',
                                'uniacid' => $_W['uniacid'],
                                'extra_info' => $extra_info,
                            );
                            Trigger::init('platform')->send($params);
                            if ($this->setting['partner']['member_info']) { //需要完善资料
                                mc_update($_W['member']['uid'], array(
                                    'realname' => $realname,
                                    'mobile' => $mobile
                                ));
                            }
                            if (!empty($recommend)) {
                                $upgradeid = $recommend['id'];
                                for ($i = 1; $i <= 3; $i++) {
                                    $arr = array(
                                        'downline'.$i => 1,
                                    );
                                    M::t('superman_mall_partner')->increment($arr, array('id' => $upgradeid));
                                    $rec = M::t('superman_mall_partner')->fetch($upgradeid);
                                    $upgradeid = $rec['recommendid'];
                                    if ($upgradeid <= 0) {
                                        break;
                                    }
                                }
                            }
                            //发送通知上线模版消息
                            $this->send_partner_invite_tmplmsg($recommend, $_data, array('url' => $this->createMobileUrl('partner', array('act' => 'home'))));
                            $this->json(ERRNO::OK, '申请成功，跳转中', array('url' => $this->createMobileUrl('partner', array('act' => 'home'))));
                        } else {
                            $this->json(ERRNO::SYSTEM_ERROR, '系统错误，请稍后再试');
                        }
                    }
                } else if (!isset($this->setting['partner']['join_condition']['type'])
                    || $this->setting['partner']['join_condition']['type'] == 1) { //关注即可
                    if ($_W['fans']['follow']) {
                        $new_id = M::t('superman_mall_partner')->insert($_data);
                        if ($new_id) {
                            //发送通知上线模版消息
                            $this->send_partner_invite_tmplmsg($recommend, $_data, array('url' => $this->createMobileUrl('partner', array('act' => 'home'))));
                            isetcookie('__fromid', 0, -1);
                            if ($_data['status'] == 1) {
                                @header('Location:'.$this->createMobileUrl('partner', array('act' => 'home')));
                                exit;
                            } else {
                                $this->message('帐号审核中，请稍候', '', 'info');
                            }
                        } else {
                            $this->message('系统错误，请返回重试', '', 'warn');
                        }
                    } else { //未关注
                        $subscribe_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);
                        if ($subscribe_setting && $subscribe_setting['subscribeurl']) {
                            $this->message('未关注公众号', $subscribe_setting['subscribeurl'], 'warn');
                        }
                    }
                } else if ($this->setting['partner']['join_condition']['type'] == 2) {  //申请加入
                    if (checksubmit()) {
                        if ($this->setting['partner']['member_info']) { //需要完善资料
                            $realname = $_GPC['realname'];
                            $mobile = $_GPC['mobile'];
                            if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
                                $this->json(ERRNO::MOBILE_INVALID);
                            }
                            if (isset($sms['setting']['partner_reg']['switch']) && $sms['setting']['partner_reg']['switch']) {
                                $checkcode = $_GPC['checkcode'];
                                if ($checkcode == '') {
                                    $this->json(ERRNO::CHECKCODE_NULL);
                                }
                                $filter = array(
                                    'openid' => $_W['openid'],
                                    'mobile' => $mobile,
                                    'verifycode' => $checkcode
                                );
                                $item = M::t('superman_mall_sms_verify')->fetch($filter);
                                if (!$item) {
                                    $this->json(ERRNO::CHECKCODE_ERROR);
                                }
                                if (TIMESTAMP - $item['sendtime'] > 600) {
                                    $this->json(ERRNO::CHECKCODE_ERROR, '验证码已过期');
                                }
                            }
                        }
                        $new_id = M::t('superman_mall_partner')->insert($_data);
                        if ($new_id) {
                            //发送通知上线模版消息
                            $this->send_partner_invite_tmplmsg($recommend, $_data, array('url' => $this->createMobileUrl('partner', array('act' => 'home'))));
                            isetcookie('__fromid', 0, -1);
                            //平台触发器
                            $extra_info = "\n\n==详情==\n";
                            $extra_info .= "昵称：{$_W['member']['nickname']}\n";
                            if (isset($realname) && $realname) {
                                $extra_info .= "姓名：{$realname}\n";
                            }
                            if (isset($mobile) && $mobile) {
                                $extra_info .= "手机：{$mobile}\n";
                            }
                            $extra_info .= "推荐人：无\n";
                            $params = array(
                                'action' => 'partner_register',
                                'uniacid' => $_W['uniacid'],
                                'extra_info' => $extra_info,
                            );
                            Trigger::init('platform')->send($params);
                            if ($this->setting['partner']['member_info']) { //需要完善资料
                                mc_update($_W['member']['uid'], array(
                                    'realname' => $realname,
                                    'mobile' => $mobile
                                ));
                            }
                            $this->json(ERRNO::OK, '申请成功，跳转中...', array('url' => $this->createMobileUrl('partner', array('act' => 'home'))));
                        } else {
                            $this->json(ERRNO::SYSTEM_ERROR, '系统错误，请稍后再试');
                        }
                    }
                } else {    //type = 3 & 4
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'uid' => $_W['member']['uid'],
                        'shopid' => '# >0',
                        'status' => 4,
                    );
                    if ($this->setting['partner']['join_condition']['type'] == 3) {
                        //订单总数
                        $count = M::t('superman_mall_order')->count($filter);
                    } else {    //join_type = 4
                        //订单总金额
                        $count = M::t('superman_mall_order')->sum($filter, 'price');
                    }
                    if (isset($this->setting['partner']['join_condition']['limit'])     //订单不满足条件
                        && $count < floatval($this->setting['partner']['join_condition']['limit'])) {
                        $_data['status'] = -1;
                    }
                    $new_id = M::t('superman_mall_partner')->insert($_data);
                    if ($new_id) {
                        //发送通知上线模版消息
                        $this->send_partner_invite_tmplmsg($recommend, $_data, array('url' => $this->createMobileUrl('partner', array('act' => 'home'))));
                        isetcookie('__fromid', 0, -1);
                        if ($_data['status'] == 1) {
                            @header('Location:'.$this->createMobileUrl('partner', array('act' => 'home')));
                            exit;
                        } else if ($_data['status'] == -1) {    //等待中
                            //do nothing
                            $this->partner = $_data;
                            $this->partner['id'] = $new_id;
                        } else {
                            $this->message('帐号审核中，请稍候', '', 'info');
                        }
                    } else {
                        $this->message('系统错误，请返回重试', '', 'warn');
                    }
                }
            } else {
                $this->check_partner_auth();
                if ($this->partner['status'] == 1) {
                    $this->message('已加入分销商！', $this->createMobileUrl('partner'), 'info');
                } else {
                    //status == -1
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'uid' => $_W['member']['uid'],
                        'shopid' => '# >0',
                        'status' => 4,
                    );
                    if ($this->partner['recommendid'] == 0) {   //新加入1级
                        if ($this->setting['partner']['join_condition']['type'] == 3) {
                            //订单总数
                            $count = M::t('superman_mall_order')->count($filter);
                        } else {    //join_type = 4
                            //订单总金额
                            $count = M::t('superman_mall_order')->sum($filter, 'price');
                        }
                        if (isset($this->setting['partner']['join_condition']['limit'])     //满足条件
                            && $count > floatval($this->setting['partner']['join_condition']['limit'])) {
                            M::t('superman_mall_partner')->update(array(
                                'status' => isset($this->setting['partner']['check']) && $this->setting['partner']['check'] == 1?0:1,
                            ), array('id' => $this->partner['id']));
                            @header('Location:'.$this->createMobileUrl('partner', array('act' => 'home')));
                            exit;
                        }
                    } else {        //被邀下线
                        if (!isset($this->setting['partner']['downline']) || $this->setting['partner']['downline'] == 1) {
                            //更新状态
                            M::t('superman_mall_partner')->update(array(
                                'status' => 1,
                            ), array('id' => $this->partner['id']));
                            @header('Location:'.$this->createMobileUrl('partner', array('act' => 'home')));
                            exit;
                        } else {
                            $filter = array(
                                'uniacid' => $_W['uniacid'],
                                'shopid' => '# >0',
                                'uid' => $this->partner['uid'],
                            );
                            if ($this->setting['partner']['downline'] == 2) {
                                $filter['status'] = array(3, 4);
                                $count = M::t('superman_mall_order')->count($filter);
                            } else {        //downline=3
                                $filter['status'] = 4;
                                $count = M::t('superman_mall_order')->count($filter);
                            }
                            if ($count > 1) {
                                //更新状态
                                M::t('superman_mall_partner')->update(array(
                                    'status' => 1,
                                ), array('id' => $this->partner['id']));
                                @header('Location:'.$this->createMobileUrl('partner', array('act' => 'home')));
                                exit;
                            }
                        }
                    }
                    $this->message('帐号审核中，请稍候', '', 'info');
                }
            }
        } else if ($op == 'smsverify') {
            $mobile = $_GPC['mobile'];
            if ($mobile == '') {
                $this->json(ERRNO::MOBILE_NULL);
            }
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
            if (!isset($sms['setting']['partner_reg']['switch']) || $sms['setting']['partner_reg']['switch'] == 0) {
                $this->json(ERRNO::SMS_SWITCH_CLOSE);
            }
            $provider = $sms['setting']['partner_reg']['provider'];
            if (!array_key_exists($provider, SupermanSms::$providers)) {
                $this->json(ERRNO::SMS_ACCOUNT_NULL);
            }
            $filter = array(
                'openid' => $_W['openid'],
            );
            $item = M::t('superman_mall_sms_verify')->fetch($filter);
            if (!$item) {
                $verifycode = random(6, true);
                $data = array(
                    'openid' => $_W['openid'],
                    'mobile' => $mobile,
                    'verifycode' => $verifycode,
                    'ip' => CLIENT_IP,
                    'count' => 1,
                    'sendtime' => TIMESTAMP
                );
                $new_id = M::t('superman_mall_sms_verify')->insert($data);
                if ($new_id) {
                    if ($provider == 'chanzor' || $provider == 'smsbao') {
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
                        $account = $sms['account'][$provider];
                        $ret = Sms::init($provider, $account)->send($mobile, $tmpl);
                        if ($ret !== true) {
                            WeUtility::logging('fatal', 'send sms failed, account='.var_export($account, true).', mobile='.$mobile.', message='.$tmpl);
                            $this->json(ERRNO::SMS_SEND_FAILED);
                        }
                    } else if ($provider == 'alidayu') {
                        $template['id'] = $sms['template']['alidayu']['verifycode']['id'];
                        $template['variables'] = json_encode(array(
                            $sms['template']['alidayu']['verifycode']['variable'] => $verifycode,
                        ));
                        $account = $sms['account'][$provider];
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
                    if ($provider == 'chanzor' || $provider == 'smsbao') {
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
                        $account = $sms['account'][$provider];
                        $ret = Sms::init($provider, $account)->send($mobile, $tmpl);
                        if ($ret !== true) {
                            WeUtility::logging('fatal', 'send sms failed, account='.var_export($account, true).', mobile='.$mobile.', message='.$tmpl);
                            $this->json(ERRNO::SMS_SEND_FAILED);
                        }
                    } else if ($provider == 'alidayu') {
                        $template['id'] = $sms['template']['alidayu']['verifycode']['id'];
                        $template['variables'] = json_encode(array(
                            $sms['template']['alidayu']['verifycode']['variable'] => $verifycode,
                        ));
                        $account = $sms['account'][$provider];
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

        include $this->template('partner/index');
    }
    private function do_team() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $title = $this->setting['text']['myteam'];
        $this->check_partner_auth();
        if (isset($_GPC['partnerid']) && $_GPC['partnerid'] > 0 && $_GPC['partnerid'] != $this->partner['id']) {
            //距离
            $distance = M::t('superman_mall_partner')->compute_distance($this->partner['id'], $_GPC['partnerid']);
            if ($distance <= 0) {
                $this->message('非法请求', '', 'warn');
            }
            $partner = M::t('superman_mall_partner')->fetch($_GPC['partnerid']);
        } else {
            $partner = $this->partner;
        }
        $member = mc_fetch($partner['uid'], array('nickname', 'avatar'));
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'recommendid' => $partner['id']
        );
        $downline_list = M::t('superman_mall_partner')->fetchall($filter, '', 0, -1);
        if ($downline_list) {
            foreach ($downline_list as &$li) {
                $li['member'] = mc_fetch($li['uid'], array('nickname', 'avatar'));
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'partner1_id' => $li['id'],
                    'status' => 4
                );
                $li['order_price'] = M::t('superman_mall_order')->sum($filter, 'price'); //该用户邀请人的订单金额
                $li['order_price'] = $li['order_price']?$li['order_price']:0.00;
            }
            unset($li);
        }
        //无限滚动 TODO
        include $this->template('partner/index');
    }
    private function do_invite() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $title = $this->setting['text']['invite_friend'];
        $this->check_partner_auth();
        $member = mc_fetch($this->partner['uid'], array('avatar'));
        $invite_url = $_W['siteroot'].'app/'.$this->createMobileUrl('partner', array(
            'act' => 'reg',
            'fromid' => $this->partner['id'],
        ));
        $qrcode_path = SupermanUtil::get_partner_qrcode($invite_url);
        //设置分享参数
        $_share = array(
            'title'   => isset($this->setting['partner']['share_title'])?$this->setting['partner']['share_title']:'',
            'link'    => $_W['siteroot'].'app/'.$this->createMobileUrl('partner', array('act' => 'reg', 'fromid' => $this->partner['id'])),
            'imgUrl'  => isset($this->setting['partner']['share_img'])?tomedia($this->setting['partner']['share_img']):'',
            'content' => isset($this->setting['partner']['share_desc'])?$this->setting['partner']['share_desc']:'',
        );
        include $this->template('partner/index');
    }
    private function do_ranking() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $title = $this->setting['text']['commission_rank'];
        $this->check_partner_auth();
        $setting = $this->setting;
        if ($setting['base']['rank'] != 1) {
            $this->message('佣金排行未开启', '', 'warn');
        } else {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = (!empty($setting['base']['rank_pagesize']))?intval($setting['base']['rank_pagesize']):10;
            $start = ($pindex - 1) * $pagesize;
            $type = in_array($_GPC['type'], array('week', 'month')) ? $_GPC['type'] : 'week';
            if ($type == 'week') {
                $starttime = date('Ymd', strtotime('-1 week'));
                $endtime = date('Ymd');
            }
            if ($type == 'month') {
                $starttime = date('Ymd', strtotime('-1 months'));
                $endtime = date('Ymd');
            }
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':starttime' => $starttime,
                ':endtime' => $endtime,
            );
            $sql = "SELECT COUNT(DISTINCT partnerid,type) FROM ".tablename('superman_mall_partner_stat');
            $sql .= " WHERE uniacid=:uniacid AND daytime>=:starttime AND daytime<=:endtime";
            $total = pdo_fetchcolumn($sql, $params);
            if ($total) {
                $where = " WHERE uniacid=:uniacid AND daytime>=:starttime AND daytime<=:endtime GROUP BY partnerid,type";
                $sql = "SELECT partnerid,type,SUM(commission_total) AS commission FROM ".tablename('superman_mall_partner_stat')." {$where}";
                $sql .= " ORDER BY commission DESC LIMIT $start, $pagesize";
                $list = pdo_fetchall($sql, $params);
                if ($list) {
                    foreach ($list as $key => &$value) {
                        $value['ranking'] = $start + $key + 1;
                        if ($value['type'] == 1) {
                            $partner_info = M::t('superman_mall_partner')->fetch($value['partnerid']);
                            $value['uid'] = $partner_info['uid'];
                            $member = mc_fetch($value['uid'], array('nickname', 'avatar'));
                            $value['nickname'] = $member['nickname'];
                            $value['avatar'] = $member['avatar'];
                        } else {
                            $value['partner_info'] = M::t('superman_mall_partner_virtual')->fetch($value['partnerid']);
                            $value['nickname'] = $value['partner_info']['nickname'];
                            $value['avatar'] = $value['partner_info']['avatar'];
                        }
                    }
                    unset($value);
                }
                //加载更多
                if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                    die(json_encode($list));
                }
            }
            include $this->template('partner/index');
        }
    }
    private function do_order() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $title = $this->setting['text']['order'];
        $this->check_partner_auth();
        if (isset($_GPC['partnerid']) && $_GPC['partnerid'] > 0 && $_GPC['partnerid'] != $this->partner['id']) {       //非本人   确认上下关系
            $distance = M::t('superman_mall_partner')->compute_distance($this->partner['id'], $_GPC['partnerid'], $this->setting['base']['level']);
            if ($distance <= 0) {
                $this->message('非法请求', '', 'warn');
            }
            $partnerid = $_GPC['partnerid'];
        } else {
            $partnerid = $this->partner['id'];
        }
        $pindex = max(1, intval($_GPC['page']));
        $pagesize = 5;
        $start = ($pindex - 1) * $pagesize;
        $orderby = ' ORDER BY createtime DESC';
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'shopid' => '# >0',
            'partner1_id' => $partnerid,
        );
        $list = M::t('superman_mall_order')->fetchall($filter, $orderby, $start, $pagesize);
        if ($list) {
            foreach ($list as &$row) {
                $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
                $row['status_title'] = SupermanUtil::get_order_status_title($row['status']);
                $row['ordersn'] = SupermanUtil::hide_ordersn($row['ordersn']);
                if ($row['partner1_id'] == $this->partner['id']) {
                    $row['partner_commission'] = $row['partner1_commission'];
                } else if ($row['partner2_id'] == $this->partner['id']) {
                    $row['partner_commission'] = $row['partner2_commission'];
                } else if ($row['partner3_id'] == $this->partner['id']) {
                    $row['partner_commission'] = $row['partner3_commission'];
                }
                if ($this->setting['base']['show_express'] == 1) {
                    $row['express_no'] = SupermanUtil::hide_expressno($row['express_no']);
                }
            }
            unset($row);
        }
        //加载更多
        if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
            die(json_encode($list));
        }
        include $this->template('partner/index');
    }
    private function do_getcash() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $op = in_array($_GPC['op'], array('display', 'apply'))?$_GPC['op']:'display';
        $this->check_partner_auth();
        $member = mc_fetch($this->partner['uid'], array('realname', 'mobile'));
        if (isset($this->setting['partner']['member_info']) && $this->setting['partner']['member_info']) {
            if ($member['realname'] == '' || $member['mobile'] == '') {
                 $member_info = false;
            }
        }
        if ($op == 'display') {
            $title = '提现记录';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $orderby = ' ORDER BY createtime DESC';
            $filter = array(
                'partnerid' => $this->partner['id'],
            );
            $list = M::t('superman_mall_partner_getcash_log')->fetchall($filter, $orderby, $start, $pagesize);
            if ($list) {
                foreach ($list as &$row) {
                    $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
                }
                unset($row);
            }
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($list));
            }
        } else if ($op == 'apply') {
            $title = '我要提现';
            if (isset($this->setting['partner']['getcash_limit']) && $this->setting['partner']['getcash_limit'] > $this->partner['commission_balance']) {
                $this->message('您的可提金额低于平台最低提现金额限制&yen;'.$this->setting['partner']['getcash_limit'], '', 'warn');
            }
            $member = mc_fetch($this->partner['uid'], array('nickname', 'avatar'));
            if (checksubmit()) {
                $money = $_GPC['money'];
                if ($money <= 0) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                if ($money > $this->partner['commission_balance']) {
                    $this->json(ERRNO::INVALID_REQUEST, '提现金额超过可提金额');
                }
                if (isset($this->setting['partner']['getcash_limit']) && $money < $this->setting['partner']['getcash_limit']) {
                    $this->json(ERRNO::INVALID_REQUEST, '提现金额小于最低提现额度');
                }
                pdo_begin();
                $ret1 = M::t('superman_mall_partner')->decrement(array('commission_balance' => $money), array('id' => $this->partner['id']));
                $ret2 = M::t('superman_mall_partner')->increment(array('commission_received' => $money), array('id' => $this->partner['id']));
                $account = array(
                    'openid' => $this->partner['openid']
                );
                $_data = array(
                    'uniacid' => $_W['uniacid'],
                    'partnerid' => $this->partner['id'],
                    'account_type' => 'wechat',
                    'account' => iserializer($account),
                    'money' => $money,
                    'apply_remark' => '',   //申请备注
                    'orderno' => date('YmdHis', TIMESTAMP).random(6, true), //提现单号
                    'status' => 0,
                    'createtime' => TIMESTAMP,
                );
                $new_id = M::t('superman_mall_partner_getcash_log')->insert($_data);
                if ($ret1 !== false && $ret2 !== false && $new_id > 0) {
                    //平台触发器
                    $params = array(
                        'action' => 'partner_getcash_submit',
                        'uniacid' => $_W['uniacid'],
                    );
                    Trigger::init('platform')->send($params);
                    pdo_commit();
                    $this->json(ERRNO::OK, '申请成功，跳转中...', array('url' => $this->createMobileUrl('partner', array('act' => 'getcash', 'op' => 'display'))));
                } else {
                    pdo_rollback();
                    $this->json(ERRNO::SYSTEM_ERROR, '系统出错，请稍后再试');
                }
            }
        }
        include $this->template('partner/index');
    }
    private function do_poster() {
        global $_W, $_GPC, $do;
        $act = $_GPC['act']?$_GPC['act']:'home';
        $title = $this->setting['text']['my_poster'];
        $filter = array(
            'uniacid' => $_W['uniacid'],
        );
        $style_list = M::t('superman_mall_partner_poster')->fetchall($filter, 'ORDER BY isdefault DESC, id DESC');
        /*if (!file_exists(SUPERMAN_FONT_MSYH)) {
            $this->message('字体文件未初始化', '', 'warn');
        }*/
        $poster_url = '';
        $item = M::t('superman_mall_partner_poster')->fetch(array(
            'uniacid' => $_W['uniacid'],
        ), 'ORDER BY isdefault DESC, id DESC');
        if ($item) {
            $poster_path = SupermanUtil::get_parter_poster_path($_W['member']['uid'], $item['id']);
            if (file_exists(MODULE_ROOT.'/'.$poster_path)) {
                $poster_url = MODULE_URL.$poster_path;
            }
        }
        include $this->template('partner/index');
    }
}
$obj = new Superman_mall_doMobilePartner;