<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileShop extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $do = $do?$do:'shop';
        $act = in_array($_GPC['act'], array('display', 'reg', 'join',
            'smsverify', 'list'))?$_GPC['act']:'display';
        $shop_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
        if ($act == 'display') { //商户主页
            $title = '商户';
            $shopid = intval($_GPC['shopid']);
            if (!$shopid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $shop = M::t('superman_mall_shop')->fetch($shopid);
            if (!$shop) {
                $this->json(ERRNO::SHOP_NOT_FOUND);
            }
            if ($shop['status'] == 0) {
                $this->json(ERRNO::SHOP_AUDITING);
            }
            if ($shop['status'] == 2) {
                $this->json(ERRNO::SHOP_DISABLED);
            }
            //幻灯图片加载
            $shop_slide = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_SHOP_SLIDE, $shopid);
            //秒杀商品加载
            $seckill_params = SupermanUtil::get_seckill_params();
            $seckill_items = M::t('superman_mall_item')->fetchall(array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $shopid,
                'status' => 1,
                'special' => 1,
                'starttime' => '#<='.TIMESTAMP,
                'endtime' => '#>='.TIMESTAMP,
                'seckill_time' => $seckill_params['key'],
            ), 'ORDER BY displayorder DESC, id DESC', 0, -1);
            if ($seckill_items) {
                foreach ($seckill_items as &$li) {
                    if ($li['total'] == 0) {
                        $li['sale_percent'] = 100;
                    } else {
                        $li['sale_percent'] = floor($li['sales']/($li['sales']+$li['total'])*100);
                    }
                    if ($li['sale_percent'] <= 0) {
                        $li['sale_percent'] = 1;
                    }
                }
                unset($li);
            }
            //拼团
            $mgroupon_items = M::t('superman_mall_item')->fetchall(array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $shopid,
                'special' => 2,
                'status' => 1,
            ), 'ORDER BY displayorder DESC, id DESC', 0, 12);
            //营销活动
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $shopid,
                'starttime' => '# < '.TIMESTAMP,
                'endtime' => '# >'.TIMESTAMP,
            );
            $activity = M::t('superman_mall_shop_activity')->fetchall($filter, ' ORDER BY `type` ASC', 0, -1);
            $title = $shop['title'];
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $orderby = 'ORDER BY displayorder DESC, id DESC';
            $filter = array(
                'shopid' => $shopid,
                'status' => 1,
                'special' => 0,
            );
            if (isset($_GPC['activityid']) && $_GPC['activityid'] > 0) {
                $activity = M::t('superman_mall_shop_activity')->fetch($_GPC['activityid']);
                $title = $activity['title'];
                if ($activity['isglobal'] == 0) {
                    $result = M::t('superman_mall_shop_activity_item')->fetchall(array('activityid' => $_GPC['activityid']), '', 0, -1, 'itemid');
                    if ($result) {
                        $itemids = array_keys($result);
                        $filter['id'] = $itemids;
                    }
                }
            }
            $list = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
            if ($list) {
                foreach ($list as &$value) {
                    $filter = array(
                        'itemid' => $value['id']
                    );
                    $sku = M::t('superman_mall_item_sku')->fetch($filter);
                    if ($sku) {
                        $value['skuid'] = $sku['id'];
                    }
                    unset($value, $sku);
                }
            }
            //限时打折商品折扣计算
            if (isset($activity) && $activity['type'] == 2 && isset($result) && $result) {
                foreach ($list as &$li) {
                    $extend = isset($result[$li['id']]['extend'])?iunserializer($result[$li['id']]['extend']):array();
                    if ($extend['value']) {
                        $li['act_price'] = SupermanUtil::float_format($li['price'] /10 * $extend['value']);   //活动价
                    }
                }
                unset($li);
            }
            $style_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_STYLE);
            //购物车按钮
            $list_style_cart_btn = $style_setting['list_style_cart_btn']?$style_setting['list_style_cart_btn']:0;
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($list));
            }
            //print_r($list);
            //设置分享参数
            $_share = array(
                'title'   => $shop['title'],
                'link'    => $_W['siteurl'],
                'imgUrl'  => tomedia($shop['logo']),
                'content' => $shop['description']?$shop['description']:$shop['title'],
            );
        } else if ($act == 'reg') {
            if ($shop_setting['join_switch'] == 0) {
                $this->json(ERRNO::INVALID_REQUEST, '未开启商户入驻');
            }
            $this->checkauth();
            $title = '账号注册';
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'openid' => $_W['openid'],
            );
            $shop_user = M::t('superman_mall_shop_user')->fetch($filter);
            if ($shop_user) {
                if ($shop_user['shopid'] > 0) {    //有店铺
                    $this->show_shop_status($shop_user['shopid']);
                } else {
                    @header('Location:'.$this->createMobileUrl('shop', array('act' => 'join')));
                    exit;
                }
            }
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);

            if ($shop_setting) {
                $countrys = M::t('superman_mall_country')->fetchall(array('isshow' => 1), '', 0, -1);
                $countryid_china = pdo_fetchcolumn('SELECT id FROM '.tablename('superman_mall_country').' WHERE title=:title', array(
                    'title' => '中国大陆',
                ));
            }
            if (checksubmit()) {
                //用户名验证
                $username = $_GPC['username'];
                if (!preg_match(SUPERMAN_REGULAR_USERNAME, $username)) {
                    $this->json(ERRNO::USERNAME_INVALID);
                }
                $filter = array(
                    'username' => $username,
                    'uniacid' => $_W['uniacid']
                );
                $uname_repeat = M::t('superman_mall_shop_user')->count($filter);
                if ($uname_repeat > 0) {
                    $this->json(ERRNO::USERNAME_REPEAT);
                }

                //密码验证
                $password = $_GPC['password'];
                if (!preg_match(SUPERMAN_REGULAR_PASSWORD, $password)) {
                    $this->json(ERRNO::PASSWORD_INVALID);
                }

                if (isset($sms['setting']['shop_reg']['switch']) && $sms['setting']['shop_reg']['switch']) {
                    $mobile = $_GPC['mobile'];
                    if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
                        $this->json(ERRNO::MOBILE_INVALID);
                    }

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
                $salt = random(10);
                $hash_password = user_hash($password, $salt);
                if ($shop_setting && $shop_setting['international']) {
                    $countryid = intval($_GPC['countryid']);
                }
                if (!$countryid) {
                    $countryid = pdo_fetchcolumn('SELECT id FROM '.tablename('superman_mall_country').' WHERE title=:title', array(
                        'title' => '中国大陆',
                    ));
                }
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'countryid' => $countryid,
                    'shopid' => 0,  //尚未未注册店铺
                    'groupid' => 0, //管理员，拥有注册店铺所有权限
                    'openid' => $_W['openid'],
                    'username' => $username,
                    'password' => $hash_password,
                    'salt' => $salt,
                    'status' => 0,
                    'mobile' => isset($mobile) && $mobile ? $mobile : '',
                    'starttime' => TIMESTAMP,
                    'expiretime' => 0,  //默认结束时间为0，永久，不过期
                    'dateline' => TIMESTAMP,
                );
                $new_id = M::t('superman_mall_shop_user')->insert($data);
                if ($new_id > 0) {
                    $this->json(ERRNO::OK, '注册成功，跳转中...', array(
                        'url' => $this->createMobileUrl('shop', array('act' => 'join'))
                    ));
                } else {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
            }
        } else if ($act == 'join') {
            if ($shop_setting['join_switch'] == 0 && $act != 'display') {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $this->checkauth();
            $title = '商户入驻';
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'openid' => $_W['openid'],
            );
            $shop_user = M::t('superman_mall_shop_user')->fetch($filter);
            if (!$shop_user) {
                $this->json(ERRNO::INVALID_REQUEST, '未注册帐号', array(
                    'url' => $this->createMobileUrl('shop', array('act' => 'reg'))
                ));
            }
            if ($shop_user['shopid'] > 0) {    //有店铺
                $this->show_shop_status($shop_user['shopid']);
            }
            if ($shop_setting && $shop_setting['international'] == 1) {
                $countrys = M::t('superman_mall_country')->fetchall(array('isshow' => 1), '', 0, -1);
            }
            $countryid_china = pdo_fetchcolumn('SELECT id FROM '.tablename('superman_mall_country').' WHERE title=:title', array(
                'title' => '中国大陆',
            ));
            if (checksubmit()) {
                $title = trim($_GPC['title']);
                if ($title == '') {
                    $this->json(ERRNO::SHOP_TITLE_NULL);
                }
                $address = trim($_GPC['address']);
                if ($address == '') {
                    $this->json(ERRNO::SHOP_ADDRESS_NULL);
                }
                $contact = trim($_GPC['contact']);
                if ($contact == '') {
                    $this->json(ERRNO::SHOP_CONTACT_NULL);
                }
                $phone = trim($_GPC['phone']);
                if ($phone == '') {
                    $this->json(ERRNO::SHOP_PHONE_NULL);
                }
                $business_scope = trim($_GPC['business_scope']);
                if ($business_scope == '') {
                    $this->json(ERRNO::SHOP_BUSINESS_SCOPE_NULL);
                }
                if ($shop_setting && $shop_setting['international']) {
                    $countryid = intval($_GPC['countryid']);
                }
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $title,
                );
                $row = M::t('superman_mall_shop')->fetch($filter);
                if ($row) {
                    $this->json(ERRNO::SHOP_TITLE_REPEAT);
                }
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'countryid' => isset($countryid)?$countryid:$countryid_china,
                    'title' => $title,
                    'description' => $_GPC['description'],
                    'business_scope' => $business_scope,
                    'address' => $address,
                    'contact' => $contact,
                    'phone' => $phone,
                    'status' => 0,
                    'createtime' => TIMESTAMP,
                    'fee_rate' => $shop_setting['fee_rate'],
                    'fee_max' => $shop_setting['fee_max'],
                    'fee_min' => $shop_setting['fee_min'],
                );
                $new_id = M::t('superman_mall_shop')->insert($data);
                if ($new_id > 0) {
                    $ret = M::t('superman_mall_shop_user')->update(array('shopid' => $new_id), array(
                        'uniacid' => $_W['uniacid'],
                        'openid' => $_W['openid']
                    ));
                    if ($ret !== false) {
                        //平台触发器
                        $extra_info = "\n\n==详情==\n";
                        $extra_info .= "商户：{$data['title']}\n";
                        $extra_info .= "联系人：{$contact} {$phone}\n";
                        $param = array(
                            'action' => 'shop_register',
                            'uniacid' => $_W['uniacid'],
                            'extra_info' => $extra_info,
                        );
                        Trigger::init('platform')->send($param);

                        $this->json(ERRNO::OK, '申请中，请稍后...', array(
                            'url' => $this->createMobileUrl('shop', array('act' => 'join')))
                        );
                    } else {
                        $this->json(ERRNO::SYSTEM_ERROR);
                    }
                } else {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
            }
        } else if ($act == 'smsverify') {
            $this->checkauth();
            $mobile = $_GPC['mobile'];
            $country_id = $_GPC['country_id'];
            if ($mobile == '') {
                $this->json(ERRNO::MOBILE_NULL);
            }
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
            if (!isset($sms['setting']['shop_reg']['switch']) || $sms['setting']['shop_reg']['switch'] == 0) {
                $this->json(ERRNO::SMS_SWITCH_CLOSE);
            }
            $extra = array();
            if ($country_id) {
                $country = M::t('superman_mall_country')->fetch($country_id);
                $extra['areacode'] = $country['areacode'];
            }
            $provider = $sms['setting']['shop_reg']['provider'];
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
                        $account = $sms['account'][$provider];
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
                        $account = $sms['account'][$provider];
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
        } else if ($act == 'list') {
            $location = in_array($_GPC['location'], array('get', 'refresh'))?$_GPC['location']:'';
//            $op = in_array($_GPC['op'], array('display', 'getlocation', 'localrefresh'))?$_GPC['op']:'display';
            if ($location) {
                if ($location == 'refresh') {
                    isetcookie('latitude', 0, -1);
                    isetcookie('longitude', 0, -1);
                    $lbs = true;
                } else if ($location == 'get') {
                    if (!empty($_GPC['latitude']) && !empty($_GPC['longitude'])) {
                        @header('Location: '.$this->createMobileUrl('shop', array('act' => 'list', 'getlocal' => 1)));
                        exit;
                    } else {
                        $lbs = true;
                    }
                }
            } else {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 10;
                $start = ($pindex - 1) * $pagesize;
                $selected = 1;
                if ($_GPC['getlocal'] == 1 && !empty($_GPC['latitude']) && !empty($_GPC['longitude']) && empty($_GPC['kw'])) {
                    $selected = 3;
                    $latitude = $_GPC['latitude'];
                    $longitude = $_GPC['longitude'];
                    $sql = "SELECT *,(ROUND(6378.137 * 2 * ASIN(SQRT(POW(SIN(((latitude * PI()) / 180 - (:latitude * PI()) / 180) / 2), 2) + COS((:latitude * PI()) / 180) * COS((latitude * PI()) / 180) * POW(SIN(((longitude * PI()) / 180 - (:longitude * PI()) / 180) / 2), 2))), 2)) AS distance FROM ".tablename('superman_mall_shop');
                    $sql .= " WHERE uniacid=:uniacid AND status=:status ORDER BY distance ASC LIMIT {$start},{$pagesize}";
                    $params = array(
                        ':uniacid' => $_W['uniacid'],
                        ':status' => 1,
                        ':latitude' => $latitude,
                        ':longitude' => $longitude,
                    );
                    $list = pdo_fetchall($sql, $params);
                } else {
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'status' => 1,
                    );
                    $kw = trim($_GPC['kw']);
                    if ($kw != '') {
                        $filter['title'] = "# LIKE '%{$kw}%'";
                    }
                    if ($_GPC['order_payed'] == 1) {
                        $selected = 2;
                        $orderby = ' ORDER BY `order_payed` DESC';
                    } else {
                        $orderby = ' ORDER BY `displayorder` DESC';
                    }
                    $list = M::t('superman_mall_shop')->fetchall($filter, $orderby, $start, $pagesize);
                }
                if ($list) {
                    foreach ($list as &$li) {
                        //营销活动
                        $filter = array(
                            'uniacid' => $_W['uniacid'],
                            'shopid' => $li['id'],
                            'starttime' => '# < '.TIMESTAMP,
                            'endtime' => '# >'.TIMESTAMP,
                        );
                        $li['activity'] = M::t('superman_mall_shop_activity')->fetchall($filter, ' ORDER BY `type` ASC', 0, -1);
                        if ($li['activity']) {
                            foreach ($li['activity'] as &$activity) {
                                $activity['start'] = date('Y-m-d H:i', $activity['starttime']);
                                $activity['end'] = date('Y-m-d H:i', $activity['endtime']);
                            }
                            unset($activity);
                        }
                    }
                    unset($li);
                }
                //加载更多
                if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                    die(json_encode($list));
                }
            }
        }
        include $this->template('shop/index');
    }

    private function show_shop_status($shopid) {
        global $_W;
        $shop = M::t('superman_mall_shop')->fetch($shopid);
        if ($shop) {
            if ($shop['status'] == 0) {
                $this->message('商户入驻申请正在审核中！', '', 'info');
            } else if ($shop['status'] == 1) {
                //$url = $_W['siteroot'].'addons/superman_mall/admin/index.php?shopid='.$shopid;
                $url = $this->create_shop_web_url($shopid);
                if (!defined('LOCAL_DEVELOPMENT') && strstr($url, 'addons/superman_mall/admin') !== false) {
                    $url = $this->short_url($url);
                }
                $msg = array(
                    'msg' => '商户入驻成功！',
                    'submsg' => "<span>请拷贝以下链接从电脑登录商户后台管理系统</span><br/>$url",
                );
                $this->message($msg, '', 'success');
            } else if ($shop['status'] == -1) {
                $this->message('商户入驻审核失败！', '', 'warn');
            }
        }
        $this->message('商户不存在！', '', 'info');
    }
}
$obj = new Superman_mall_doMobileShop;