<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebFinance extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'apply', 'stat', 'post', 'delete', 'balance', 'money_log', 'statement'))?$_GPC['act']:'display';
        $nav['title'] = '财务管理';
        if ($act == 'apply') {
            $nav['subtitle'] = '提现管理';
            //提现申请列表
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_shop_getcash_log')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_getcash_log')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['createtime'] = $li['createtime']?date('Y-m-d', $li['createtime']):'';
                        $li['paytime'] = $li['paytime']?date('Y-m-d H:i:s', $li['paytime']):'';
                        $shop = M::t('superman_mall_shop')->fetch($li['shopid']);
                        if ($shop) {
                            $li['shop_title'] = $shop['title'];
                        }
                    }
                    unset($li, $shop);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'stat'){
            $nav['subtitle'] = '商户结算';
            if (!isset($_GPC['time_limit'])) {
                $start = date('Y-m-1', strtotime('-1 months'));
                $time_limit = array(
                    'start' => $start,
                    'end' => date('Y-m-d', strtotime($start.'+1 months - 1 day')),
                );
                unset($start);
            } else {
                //生成结算数据
                $time_limit = $_GPC['time_limit'];
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'status' => 1,
                );
                $shop_list = M::t('superman_mall_shop')->fetchall($filter, '', 0, -1);
                if ($shop_list) {
                    @set_time_limit(0);
                    foreach ($shop_list as $shop) {
                        $this->build_shop_stat($shop['id'], strtotime($time_limit['start']), strtotime($time_limit['end']));
                    }
                    unset($shop);
                }
                unset($shop_list);
            }
            //所选日期内所有结算数据
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['pagesize'])?$_GPC['pagesize']:20;
            $start = ($pindex - 1) * $pagesize;
            $where = " WHERE uniacid=:uniacid";
            $params = array(
                ':uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $params[':shopid'] = $this->shop['id'];
                $where .= " AND shopid=:shopid";
            }
            if (isset($_GPC['status'])) {
                $params[':status'] = $_GPC['status'];
                $where .= " AND status=:status";
            }
            $sql = "SELECT COUNT(*) FROM ".tablename('superman_mall_shop_money_stat')." {$where}";
            $total = pdo_fetchcolumn($sql, $params);
            if ($total > 0) {
                $sql = "SELECT * FROM ".tablename('superman_mall_shop_money_stat');
                $sql .= " {$where} ORDER BY stat_date DESC, shopid DESC LIMIT {$start},{$pagesize}";
                $list = pdo_fetchall($sql, $params);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['dateline'] = $li['dateline']?date('Y-m-d H:i:s', $li['dateline']):'';
                        $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                    }
                    $pager = pagination($total, $pindex, $pagesize);
                    unset($li);
                }
            }
            //批量结算
            if (checksubmit()) {
                $ids = $_GPC['id'];
                if (empty($ids)) {
                    message('未选择结算的内容', referer(), 'error');
                }
                $shop_data = array();
                foreach ($ids as $shopid=>$val) {
                    foreach ($val as $id) {
                        $stat = M::t('superman_mall_shop_money_stat')->fetch($id);
                        if (!$stat || $stat['status'] == 1) {
                            continue;
                        }
                        //更新结算数据状态
                        $ret = M::t('superman_mall_shop_money_stat')->update(array(
                            'status' => 1,
                            'dateline' => TIMESTAMP,
                            'operator' => $_W['user']['username']
                        ), array('id' => $id));
                        if ($ret !== false) {
                            $shop_data[$shopid]['income'] += floatval($stat['order_price']);
                            $shop_data[$shopid]['remark'][] = "{$stat['stat_date']}=".floatval($stat['order_price']);
                        }
                    }
                }
                foreach ($shop_data as $shopid=>$val) {
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $shopid,
                        'type' => 1,
                        'money' => $val['income'],
                        'operator' => $_W['user']['username'],
                        'remark' => '平台结算'.implode(',', $val['remark']),
                        'dateline' => TIMESTAMP,
                    );
                    $new_id = M::t('superman_mall_shop_money_log')->insert($data);
                    if ($new_id > 0) {
                        $filter = array(
                            'uniacid' => $_W['uniacid'],
                            'shopid' => $shopid,
                        );
                        $row = M::t('superman_mall_shop_money')->fetch($filter);
                        if ($row) {
                            $data = array(
                                'balance' => $val['income'] + $row['balance'],
                                'updatetime' => TIMESTAMP,
                            );
                            M::t('superman_mall_shop_money')->update($data, array('id' => $row['id']));
                        } else {
                            $data = array(
                                'uniacid' => $_W['uniacid'],
                                'shopid' => $shopid,
                                'balance' => $val['income'],
                                'updatetime' => TIMESTAMP,
                            );
                            M::t('superman_mall_shop_money')->insert($data);
                        }
                    } else {
                        WeUtility::logging('trace', 'superman_mall_shop_money_stat update success but superman_mall_shop_money_log insert fail, data=' . var_export($data, true));
                    }
                }
                message('结算完毕，跳转中...', $this->createWebUrl('finance', array('act' => 'balance')), 'info');
                /*$data = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'type' => 1,
                    'money' => $income,
                    'operator' => $_W['user']['username'],
                    'remark' => '平台结算余额，结算日期:'.$remark_date,
                    'dateline' => TIMESTAMP,
                );
                $new_id = M::t('superman_mall_shop_money_log')->insert($data);
                if ($new_id > 0) {
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $this->shop['id']
                    );
                    $row = M::t('superman_mall_shop_money')->fetch($filter);
                    if ($row) {
                        $data = array(
                            'balance' => $income + $row['balance'],
                            'updatetime' => TIMESTAMP,
                        );
                        M::t('superman_mall_shop_money')->update($data, array('id' => $row['id']));
                    } else {
                        $data = array(
                            'uniacid' => $_W['uniacid'],
                            'shopid' => $this->shop['id'],
                            'balance' => $income,
                            'updatetime' => TIMESTAMP,
                        );
                        M::t('superman_mall_shop_money')->insert($data);
                    }
                    message('结算成功，跳转中...', referer(), 'success');
                } else {
                    WeUtility::logging('trace', 'superman_mall_shop_money_stat update success but superman_mall_shop_money_log insert fail, data=' . var_export($data, true));
                    message('数据库出错，详情请查看日志', referer(), 'error');
                }*/
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            //修改提现申请状态
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法访问', referer(), 'error');
            }
            $row = M::t('superman_mall_shop_getcash_log')->fetch($id);
            if (!$row) {
                message('此申请不存在或已删除', referer(), 'error');
            }
            $row['createtime'] = $row['createtime']?date('Y-m-d H:i:s', $row['createtime']):'';
            $row['paytime'] = $row['paytime']?date('Y-m-d H:i:s', $row['paytime']):'';
            $row['updatetime'] = $row['updatetime']?date('Y-m-d H:i:s', $row['updatetime']):'';
            $row['account'] = iunserializer($row['account']);
            if ($row['account_type'] == 'wechat' && $row['account']['openid']) {
                $row['shop_admin'] = mc_fetch($row['account']['openid'], array('nickname', 'avatar'));
            }
            $shop = M::t('superman_mall_shop')->fetch($row['shopid']);
            if ($shop) {
                $row['shop_title'] = $shop['title'];
            }
            if (checksubmit()) {
                $remark = $_GPC['remark'];
                $status = $_GPC['status'];
                $data = array(
                    'remark' => $remark,
                    'status' => $status?1:0,
                    'paytime' => $_GPC['paytime']?strtotime($_GPC['paytime']):TIMESTAMP,
                    'updatetime' => TIMESTAMP,
                    'operator' => $_W['user']['username']
                );
                $ret = M::t('superman_mall_shop_getcash_log')->update($data, array('id' => $id));
                if ($ret !== false) {
                    message('操作成功，跳转中...', $this->createWebUrl('finance', array('act' => 'apply')), 'success');
                } else {
                    message('数据库出错，请稍候再试', referer(), 'error');
                }
            }
            //微信付款
            if (checksubmit('wxpay_submit')) {
                if ($row['status'] != 0) {
                    message('支付状态错误', referer(), 'error');
                }
                if ($row['account_type'] != 'wechat') {
                    message('非法请求', referer(), 'error');
                }
                $row['account'] = iunserializer($row['account']);
                $appid = $_W['account']['key'];
                $setting = uni_setting($_W['uniacid'], array('payment'));
                $pay = $setting['payment'];
                if (empty($pay)) {
                    message('请配置和开启微信支付', url('profile/payment'), 'error');
                }
                $mchid = $pay['wechat']['mchid'];
                if (empty($mchid)) {
                    message('微信支付商户号(MchId)参数未设置', url('profile/module/setting', array('m' => 'superman_mall')), 'error');
                }
                $paycert = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                if (empty($paycert) || empty($paycert['apiclient_cert']) || empty($paycert['apiclient_key']) || empty($paycert['rootca'])) {
                    message('微信支付证书未设置', $this->createWebUrl('platform', array('act' => 'paycert')), 'error');
                }
                $orderno = date('Ymd') . random(8, 1);
                $openid = $row['account']['openid'];
                $money = $row['money'] - $row['service_fee'];
                $params = array(
                    'mch_appid' => $appid,
                    'mchid' => $mchid,
                    'nonce_str' => random(32),
                    'partner_trade_no' => $orderno,
                    'openid' => $openid,
                    'check_name' => 'NO_CHECK',
                    're_user_name' => '',
                    'amount' => $money,
                    'desc' => '提现'.date('Ymd'),
                    'spbill_create_ip' => CLIENT_IP,
                );
                $extra = array();
                $extra['sign_key'] = $pay['wechat']['signkey'];
                $path = SupermanUtil::attachment_path();
                $extra['apiclient_cert'] = $path.$paycert['apiclient_cert'];
                $extra['apiclient_key'] = $path.$paycert['apiclient_key'];
                $extra['rootca'] = $path.$paycert['rootca'];
                $ret = WxpayAPI::pay($params, $extra);
                if (is_array($ret) && isset($ret['success'])) {
                    //TODO: 发送通知消息
                    $data = array(
                        'wxpay_result' => is_array($ret)?implode("\n", $ret):$ret,
                        'wxpay_orderno' => $orderno,
                        'wxpay_paymentno' => $ret['payment_no'],
                        'operator' => $_W['user']['username'],
                        'status' => 1,
                        'paytime' => strtotime($ret['payment_time']),
                        'remark' => $_GPC['remark'],
                        'updatetime' => TIMESTAMP,
                    );
                    $ret = M::t('superman_mall_shop_getcash_log')->update($data, array('id' => $id));
                    message('付款成功', $this->createWebUrl('finance', array('act' => 'post', 'id' => $id)), 'success');
                } else {
                    $data = array(
                        'wxpay_result' => is_array($ret)?implode("\n", $ret):$ret,
                        'operator' => $_W['user']['username'],
                        'remark' => $_GPC['remark'],
                        'updatetime' => TIMESTAMP,
                    );
                    M::t('superman_mall_shop_getcash_log')->update($data, array('id' => $id));
                    message('付款失败，请查看微信付款结果信息', referer(), 'error');
                }
            }
        } else if ($act == 'delete') {
            //删除提现申请
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法访问', referer(), 'error');
            }
            $log = M::t('superman_mall_shop_getcash_log')->fetch($id);
            if (!$log) {
                message('该提现申请不存在或已删除', referer(), 'error');
            }
            pdo_begin();
            if ($log['status'] == 0) {  //未提现成功
                //还钱
                $ret1 = M::t('superman_mall_shop_money')->increment(array(
                    'balance' => $log['money']
                ), array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $log['shopid'],
                ));
                $ret2 = M::t('superman_mall_shop_money_log')->insert(array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $log['shopid'],
                    'type' => 1,
                    'money' => $log['money'],
                    'operator' => $_W['user']['username'],
                    'remark' => '删除未提现的记录，退回未提现的余额:getcash_logid='.$id,
                    'dateline' => TIMESTAMP
                ));
            } else {
                $ret1 = $ret2 = 1;
            }
            $ret3 = M::t('superman_mall_shop_getcash_log')->delete(array('id' => $id));
            if ($ret1 !== false && $ret2 > 0 && $ret3 !== false) {
                pdo_commit();
                message('操作成功，跳转中...', referer(), 'success');
            } else {
                pdo_rollback();
                message('数据库出错，请稍候再试', referer(), 'error');
            }
        } else if ($act == 'balance') {
            $nav['subtitle'] = '商户钱包';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            $balance_total = M::t('superman_mall_shop_money')->sum($filter, 'balance');
            $total = M::t('superman_mall_shop_money')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_money')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['updatetime'] = $li['updatetime']?date('Y-m-d H:i:s', $li['updatetime']):'';
                        $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                    }
                    unset($li, $shop);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
            //print_r($list);
        } else if ($act == 'money_log') {
            $nav['subtitle'] = '流水明细';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $_GPC['id'],
            );
            $total = M::t('superman_mall_shop_money_log')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_money_log')->fetchall($filter, '', $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'statement') {
            $nav['subtitle'] = '对账单下载';
            $date = array(
                'start' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'end' => date('Y-m-d H:i:s', strtotime('-1 day')),
            );
            if (checksubmit()) {
                $bill_type = in_array($_GPC['bill_type'], array('ALL', 'SUCCESS', 'REFUND', 'REVOKED'))?$_GPC['bill_type']:'ALL';
                $bill_date = $_GPC['bill_date'];
                $starttime = strtotime($bill_date['start']);
                $endtime = strtotime($bill_date['end']);
                if ($starttime < strtotime('-3 months') || $endtime > TIMESTAMP) {
                    message('对账单接口只能下载三个月以内的账单', referer(), 'error');
                } else if ($starttime > $endtime) {
                    message('账单时间非法', referer(), 'error');
                }

                $setting = uni_setting($_W['uniacid'], array('payment'));
                $pay = $setting['payment'];
                if (empty($pay)) {
                    message('请配置和开启微信支付', url('profile/payment'), 'error');
                }
                $mchid = $pay['wechat']['mchid'];
                if (empty($mchid)) {
                    message('微信支付商户号(MchId)参数未设置', url('profile/module/setting', array('m' => 'superman_mall')), 'error');
                }
                $appid = $_W['account']['key'];
                $params = array(
                    'appid' => $appid,
                    'mch_id' => $mchid,
                    'nonce_str' => random(32),
                    'bill_type' => $bill_type,
                );
                $extra = array(
                    'sign_key' => $pay['wechat']['signkey']
                );
                $content = $msg = '';
                for ($i = $starttime; $i <= $endtime; $i = $i + 86400) {
                    $params['bill_date'] = date('Ymd', $i);
                    $ret = WxpayAPI::downloadbill($params, $extra);
                    if ($ret['errno'] == 0) {
                        $content .= $ret['message'];
                    } else {
                        $msg .= "{$params['bill_date']}:{$ret['message']}\n";
                    }
                }
                if ($content == '') {
                    message($msg, referer(), 'error');
                }
                $outputFileName = date('YmdHi').'.csv';
                ob_end_clean();
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                header('Content-Disposition:inline;filename="' . $outputFileName . '"');
                header("Content-Transfer-Encoding: binary");
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Pragma: no-cache");
                $content = mb_convert_encoding($content, 'GBK', 'auto');
                echo $content;
                exit(0);
            }
        }
		include $this->template('finance');
    }
    private function build_shop_stat($shopid, $starttime, $endtime) {
        global $_W;
        for ($i = $starttime; $i <= $endtime; $i += 86400) {
            if ($i > TIMESTAMP) {
                break;
            }
            $params = array(
                ':shopid' => $shopid,
            );
            //统计当天订单数和总额
            $sql = "SELECT shopid,COUNT(total) AS total,SUM(price) AS price FROM ".tablename('superman_mall_order');
            $sql .= " WHERE shopid=:shopid AND status>0 AND status<5 AND createtime>=$i AND createtime<($i+86400) AND price>0 AND payu_rid=0 AND pay_type!=3";
            $sql .= " GROUP BY shopid";
            $row = pdo_fetch($sql, $params);
            if ($row) {
                //分销佣金扣除
                $sql = "SELECT SUM(partner1_commission) AS commission1,SUM(partner2_commission) AS commission2,SUM(partner3_commission) AS commission3 FROM ".tablename('superman_mall_order');
                $sql .= " WHERE shopid=:shopid AND status>0 AND status<5 AND createtime>=$i AND createtime<($i+86400) AND price>0 AND payu_rid=0 AND pay_type!=3";
                $com = 0;
                $commission = pdo_fetch($sql, $params);
                if ($commission) {
                    $com = floatval($commission['commission1']) + floatval($commission['commission2']) + floatval($commission['commission3']);
                }

                $data = array(
                    'order_total' => $row['total'],
                    'order_price' => SupermanUtil::float_format($row['price'] - $com),
                );
                //查询是否生成当天的结算数据
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $shopid,
                    'stat_date' => date('Y-m-d', $i)
                );
                $stat = M::t('superman_mall_shop_money_stat')->fetch($filter);
                if ($stat > 0) {
                    //更新
                    M::t('superman_mall_shop_money_stat')->update($data, array('id' => $stat['id']));
                } else {
                    //新增
                    $data['uniacid'] = $_W['uniacid'];
                    $data['shopid'] = $shopid;
                    $data['stat_date'] = date('Y-m-d', $i);
                    M::t('superman_mall_shop_money_stat')->insert($data);
                }
            }
        }
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('apply', 'log', 'log_post', 'delete', 'user', 'money_log'))?$_GPC['act']:'apply';
        $nav['title'] = '财务管理';
        if ($act == 'apply') {
            $nav['subtitle'] = '申请提现';
            $this->check_user_permission('superman_mall_menu_finance_apply');
            if ($this->shop['id'] <= 0) {
                message('非法访问', referer(), 'error');
            }
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id']
            );
            //可提金额
            $shop_money = M::t('superman_mall_shop_money')->fetch($filter);
            //提现账户
            $getcash_user = M::t('superman_mall_shop_getcash_user')->fetch($filter);
            //未设置提现账户时初始化
            if (!$getcash_user) {
                $getcash_user = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'openid' => $this->shop_user['openid'], //商户管理员
                    'createtime' => TIMESTAMP
                );
                $getcash_user['id'] = M::t('superman_mall_shop_getcash_user')->insert($getcash_user);
                if ($getcash_user['id'] <= 0) {
                    message('提现账户初始化失败，请手动添加提现账户', $this->createWebUrl('finance', array('act' => 'user')), 'error');
                }
            }
            if (isset($getcash_user['openid']) && $getcash_user['openid']) {
                $getcash_user['shop_admin'] = mc_fetch($getcash_user['openid'], array('nickname', 'avatar'));
            }
            //短信设置
            $sms_available = false;
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
            if ($sms) {
                $provider = $sms['setting']['shop_getcash']['provider'];
                if ($sms['setting']['shop_getcash']['switch'] && !empty($provider)) {
                    $sms_available = true;
                }
            }
            //商户设置
            $shop_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);

            if (checksubmit()) {
                if (!$shop_money && $shop_money['balance'] <= 0) {
                    message('没有可提现的余额，请联系平台结算');
                }
                $mobile = $_GPC['mobile'];
                //验证登录密码
                $password = $_GPC['password'];
                $password = user_hash($password, $this->shop_user['salt']);
                if ($password != $this->shop_user['password']) {
                    message('登录密码错误，请重新输入', referer(), 'error');
                }
                //短信验证码
                if ($sms_available) {
                    $checkcode = $_GPC['checkcode'];
                    if ($checkcode == '') {
                        message('验证码为空，请重新输入', referer(), 'error');
                    }
                    $filter = array(
                        'openid' => $this->shop_user['openid'],
                        'mobile' => $mobile,
                        'verifycode' => $checkcode,
                    );
                    $item = M::t('superman_mall_sms_verify')->fetch($filter);
                    if (!$item) {
                        message('验证码错误，请重新输入', referer(), 'error');
                    }
                    if (TIMESTAMP - $item['sendtime'] > 600) {
                        message('验证码已过期，请重新输入', referer(), 'error');
                    }
                }

                $account_type = $_GPC['account_type'];
                if (!in_array($account_type, array('wechat', 'alipay', 'bank'))) {
                    message('非法请求', referer(), 'error');
                }

                $money = floatval($_GPC['money']);

                if ($money > $shop_money['balance']) {
                    message('提现金额高于可提现的余额，无法提现', referer(), 'error');
                }
                if ($money < $shop_setting['limit']) {
                    message('提现金额低于最低提现金额，无法提现', referer(), 'error');
                }
                if ($money <= $this->shop['fee_min']) {
                    message('提现金额低于最低服务费，无法提现', referer(), 'error');
                }
                //服务费计算
                $service_fee = $money * ($this->shop['fee_rate']/100);
                if ($service_fee < $this->shop['fee_min']) {
                    $service_fee = $this->shop['fee_min'];
                } else if ($service_fee > $this->shop['fee_max']) {
                    $service_fee = $this->shop['fee_max'];
                }
                if ($money <= $service_fee) {
                    message('提现金额低于服务费，无法提现', referer(), 'error');
                }

                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'account_type' => $account_type,
                    'apply_remark' => $_GPC['apply_remark'],
                    'createtime' => TIMESTAMP,
                    'money' => $money,
                    'fee_rate' => $this->shop['fee_rate'],
                    'fee_min' => $this->shop['fee_min'],
                    'fee_max' => $this->shop['fee_max'],
                    'service_fee' => $service_fee
                );

                if ($account_type == 'wechat') {
                    if ($getcash_user['openid'] == '') {
                        message('本商户不能使用微信提现', referer(), 'error');
                    }
                    $account = array(
                        'openid' => $getcash_user['openid']
                    );
                } else if ($account_type == 'alipay') {
                    if (!isset($getcash_user['alipay_account']) || $getcash_user['alipay_account'] == ''
                        || !isset($getcash_user['alipay_username']) || $getcash_user['alipay_username'] == '') {
                        message('提现账户中支付宝信息未设置完全，不能使用支付宝申请提现', referer(), 'error');
                    }
                    $account = array(
                        'alipay_account' => $getcash_user['alipay_account'],
                        'alipay_username' => $getcash_user['alipay_username']
                    );
                } else {
                    if (!isset($getcash_user['bank_name']) || $getcash_user['bank_name'] == ''
                        || !isset($getcash_user['bank_account']) || $getcash_user['bank_account'] == ''
                        || !isset($getcash_user['bank_cardno']) || $getcash_user['bank_cardno'] == ''
                        || !isset($getcash_user['bank_username']) || $getcash_user['bank_username'] == '') {
                        message('提现账户中银行信息未设置完全，不能使用银行卡申请提现', referer(), 'error');
                    }
                    $account = array(
                        'bank_name' => $getcash_user['bank_name'],
                        'bank_account' => $getcash_user['bank_account'],
                        'bank_cardno' => $getcash_user['bank_cardno'],
                        'bank_username' => $getcash_user['bank_username']
                    );
                }
                $data['account'] = iserializer($account);
                //事务开始
                pdo_begin();
                $ret1 = M::t('superman_mall_shop_getcash_log')->insert($data);
                $ret2 = M::t('superman_mall_shop_money')->decrement(array('balance' => $money), array('id' => $shop_money['id']));
                $ret3 = M::t('superman_mall_shop_money_log')->insert(array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'type' => 2,
                    'money' => $money,
                    'operator' => $_W['user']['username'],
                    'remark' => '申请提现操作:getcash_logid='.$ret1,
                    'dateline' => TIMESTAMP
                ));
                if ($ret1 > 0 && $ret2 !== false && $ret3 > 0) {
                    pdo_commit();
                    message('申请成功，跳转中...', $this->createWebUrl('finance', array('act' => 'log')), 'success');
                } else {
                    pdo_rollback();
                    message('系统出错，请稍后再试', referer(), 'error');
                }
            }
        } else if ($act == 'log') {
            $nav['subtitle'] = '提现记录';
            //提现记录
            $this->check_user_permission('superman_mall_menu_finance_log');
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id']
            );
            $total = M::t('superman_mall_shop_getcash_log')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_getcash_log')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['createtime'] = $li['createtime']?date('Y-m-d', $li['createtime']): '';
                        $li['paytime'] = $li['paytime']?date('Y-m-d H:i:s', $li['paytime']): '';
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'log_post') {    //申请提现记录的编辑
            $nav['subtitle'] = '编辑';
            $this->check_user_permission('superman_mall_menu_finance_log');
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法访问', referer(), 'error');
            }
            $row = M::t('superman_mall_shop_getcash_log')->fetch($id);
            if (!$row) {
                message('此提现申请不存在或已删除', referer(), 'error');
            }
            if ($this->shop['id'] != $row['shopid']) {
                message('非法访问', referer(), 'error');
            }
            $row['account'] = iunserializer($row['account']);
            if ($row['account_type'] == 'wechat') {
                $row['account'] = mc_fetch($row['account']['openid'], array('nickname', 'avatar'));
            }
            $row['createtime'] = $row['createtime']? date('Y-m-d H:i:s', $row['createtime']): '';
            $row['paytime'] = $row['paytime']? date('Y-m-d H:i:s', $row['paytime']): '';
        } else if ($act == 'delete') {
            $this->check_user_permission('superman_mall_menu_finance_log');
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法访问', referer(), 'error');
            }
            $log = M::t('superman_mall_shop_getcash_log')->fetch($id);
            if (!$log) {
                message('该提现申请不存在或已删除', referer(), 'error');
            }
            if ($this->shop['id'] != $log['shopid']) {
                message('非法访问', referer(), 'error');
            }
            pdo_begin();
            if ($log['status'] == 0) {  //未提现成功
                //还钱
                $ret1 = M::t('superman_mall_shop_money')->increment(array('balance' => $log['money']), array('uniacid' => $_W['uniacid'], 'shopid' => $this->shop['id']));
                $ret2 = M::t('superman_mall_shop_money_log')->insert(array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'type' => 1,
                    'money' => $log['money'],
                    'operator' => $_W['user']['username'],
                    'remark' => '删除未提现的记录，退回未提现的余额:getcash_logid='.$id,
                    'dateline' => TIMESTAMP
                ));
            } else {
                $ret1 = $ret2 = 1;
            }
            $ret3 = M::t('superman_mall_shop_getcash_log')->delete(array('id' => $id));
            if ($ret1 !== false && $ret2 > 0 && $ret3 !== false) {
                pdo_commit();
                message('操作成功，跳转中...', referer(), 'success');
            } else {
                pdo_rollback();
                message('数据库出错，请稍候再试', referer(), 'error');
            }
        } else if ($act == 'user') {
            $nav['subtitle'] = '提现账户';
            //提现账户设置
            $this->check_user_permission('superman_mall_menu_finance_user');
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id']
            );
            $row = M::t('superman_mall_shop_getcash_user')->fetch($filter);

            if (!$row) {
                $filter['groupid'] = 0;
                $shop_admin = M::t('superman_mall_shop_user')->fetch($filter);
                if ($shop_admin) {
                    $row = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $this->shop['id'],
                        'openid' => $shop_admin['openid'],
                        'createtime' => TIMESTAMP
                    );
                    $row['id'] = M::t('superman_mall_shop_getcash_user')->insert($row);
                    if ($row['id'] <= 0) {
                        message('提现账户初始化失败，请手动添加提现账户', $this->createWebUrl('finance', array('act' => 'user')), 'error');
                    }
                }
            }
            if (isset($row['openid']) && $row['openid']) {
                $row['shop_admin'] = mc_fetch($row['openid'], array('nickname', 'avatar'));
            }
            if (checksubmit()) {
                $data = array(
                    'alipay_account' => $_GPC['alipay_account'],
                    'alipay_username' => $_GPC['alipay_username'],
                    'bank_name' => $_GPC['bank_name'],
                    'bank_account' => $_GPC['bank_account'],
                    'bank_cardno' => $_GPC['bank_cardno'],
                    'bank_username' => $_GPC['bank_username'],
                );
                if ($row) {
                    $data['updatetime'] = TIMESTAMP;
                    $ret = M::t('superman_mall_shop_getcash_user')->update($data, array('id' => $row['id']));
                } else {
                    if (isset($shop_admin['openid'])) {
                        $data['openid'] = $shop_admin['openid'];
                    }
                    $data['uniacid'] = $_W['uniacid'];
                    $data['shopid'] = $this->shop['id'];
                    $data['createtime'] = TIMESTAMP;
                    $ret = M::t('superman_mall_shop_getcash_user')->insert($data);
                    if ($ret <= 0) {
                        $ret = false;
                    }
                }
                if ($ret === false) {
                    message('系统出错，请稍后再试', referer(), 'error');
                } else {
                    message('更新成功，跳转中...', referer(), 'success');
                }
            }
        } else if ($act == 'money_log') {
            $nav['subtitle'] = '资金流水明细';
            $this->check_user_permission('superman_mall_menu_finance_apply');
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id']
            );
            $total = M::t('superman_mall_shop_money_log')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_money_log')->fetchall($filter, '', $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
            }
        }
        include $this->template('finance/index');
    }
}
$obj = new Superman_mall_doWebFinance();