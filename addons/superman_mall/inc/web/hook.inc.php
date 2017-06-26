<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebHook extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W;
        if (isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) {
            //后台只处理当前店铺的拼团退款 合理
            if ($_W['uid']) {
                if (isset($this->shop['id']) && $this->shop['id'] > 0) {
                    //读取店铺拼团退款设置
                    $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, $this->shop['id']);
                    if (isset($setting['autorefund']) && $setting['autorefund']) {  //开启自动退款
                        $filter = array(
                            'uniacid' => $_W['uniacid'],
                            'mgid' => 0,
                            'shopid' => $this->shop['id'],
                            'pay_status' => 1,
                            'status' => array(0, 2),    //未处理或未开启自动成团状态
                            'expiretime' => '#<' . TIMESTAMP, //已过期
                        );
                        $list = M::t('superman_mall_merge_groupon')->fetchall($filter, '', 0, 10);  //取10个团开始处理
                        if ($list) {
                            $this->autorefund_mgroupon($list);
                        }
                    }
                }
            }
        }
    }

    private function autorefund_mgroupon($list) {
        //自动化处理状态,0:未处理,1:拼团成功,2:未开启自动成团,3:退款成功,4:订单状态异常退款失败
        if (!$list || !is_array($list)) {
            return;
        }
        foreach ($list as $mgroup) {
            //团员人数
            $filter = array(
                'mgid' => $mgroup['id'],
                'pay_status' => 1
            );
            $mgroup_member = M::t('superman_mall_merge_groupon')->fetchall($filter, '', 0, -1);
            $remain_total = $mgroup['limit'] - count($mgroup_member) - 1;   //拼团空位
            if ($remain_total <= 0) {   //拼团成功
                $this->update_mgstatus($mgroup['id'], 1);
            } else {                    //过期退款
                //团长退款
                $mgroup['remain_total'] = $remain_total;
                $this->check_order_refund($mgroup);
                if ($mgroup_member) {
                    foreach ($mgroup_member as $joiner) {
                        //团员退款
                        $joiner['remain_total'] = $remain_total;
                        $this->check_order_refund($joiner);
                    }
                }
            }
        }
    }

    private function check_order_refund($mgroupon) {
        global $_W;
        if ($mgroupon['uid'] > 0 && $mgroupon['orderid'] > 0) {
            $order = M::t('superman_mall_order')->fetch($mgroupon['orderid']);
            if (isset($order['status']) && $order['status'] == 1) {
                //退款
                $_data = array(
                    'status' => -1,
                );
                if ($order['pay_type'] == 2) {  //微信支付
                    //退还微信支付
                    if ($order['payment_no'] == '') {
                        WeUtility::logging('warning', '订单（'.$order['ordersn'].'）的微信支付单号为空，退款到微信操作失败');
                        return;
                    }
                    $setting = uni_setting($_W['uniacid'], array('payment'));
                    $pay = $setting['payment'];
                    if (empty($pay)) {
                        WeUtility::logging('warning', '未配置和开启微信支付,订单（'.$order['ordersn'].'）退款到微信操作失败');
                        return;
                    }
                    $paycert = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                    if (empty($paycert) || empty($paycert['apiclient_cert']) || empty($paycert['apiclient_key']) || empty($paycert['rootca'])) {
                        WeUtility::logging('warning', '微信支付证书未设置,订单（'.$order['ordersn'].'）退款到微信操作失败');
                        return;
                    }
                    $mchid = $pay['wechat']['mchid'];
                    if (empty($mchid)) {
                        WeUtility::logging('warning', '微信支付商户号(MchId)参数未设置,订单（'.$order['ordersn'].'）退款到微信操作失败');
                        return;
                    }
                    $params = array(
                        'appid' => $_W['account']['key'],
                        'mch_id' => $mchid,
                        'nonce_str' => random(32),
                        'transaction_id' => $order['payment_no'],
                        'out_refund_no' => random(22, true),   //退款订单号
                        'total_fee' => $order['price']-$order['pay_credit'],
                        'refund_fee' => $order['price']-$order['pay_credit'],
                        'op_user_id' => $mchid,
                    );
                    $extra = array();
                    $extra['sign_key'] = $pay['wechat']['signkey'];
                    $path = SupermanUtil::attachment_path();
                    $extra['apiclient_cert'] = $path.$paycert['apiclient_cert'];
                    $extra['apiclient_key'] = $path.$paycert['apiclient_key'];
                    $extra['rootca'] = $path.$paycert['rootca'];
                    $ret = WxpayAPI::refund($params, $extra);
                    if (is_array($ret) && isset($ret['success'])) {
                        if ($order['pay_credit'] > 0) {
                            //退还余额支付
                            $log = array(
                                $_W['uid'],
                                '拼团订单('.$order['ordersn'].')过期支付余额退款',
                                'superman_mall'
                            );
                            $this->order_refund($order['uid'], $order['pay_credit'], $log);
                        }
                        //退款成功
                        $extend = $order['extend']?iunserializer($order['extend']):array();
                        $extend['refund']['time'] = TIMESTAMP;      //退款时间
                        $extend['refund']['type'] = 2;              //退款类型
                        $extend['refund']['price'] = $order['price'];//退款金额
                        $extend['refund']['result'] = 'success';    //退款结果
                        $extend['refund']['refund_id'] = $ret['refund_id'];         //微信退款交易号
                        $extend['refund']['out_refund_no'] = $ret['out_refund_no']; //退款单号
                        $_data['extend'] = iserializer($extend);
                    }
                } else {    //余额支付
                    $log = array(
                        $_W['uid'],
                        '拼团订单(' . $order['ordersn'] . ')过期退款',
                        'superman_mall'
                    );
                    $this->order_refund($mgroupon['uid'], $order['price'], $log);
                    //发送订单退款模版消息
                    $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                    $credit_type = $setting['creditbehaviors']['currency'];
                    $url = $this->createMobileUrl('creditlog', array('credittype' => $credit_type));
                    $this->send_order_tmplmsg('refund', $order, SupermanUtil::uid2openid($order['uid']), $url);
                }

                $order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $order['id']));
                if ($order_items) {
                    foreach ($order_items as $order_item) {
                        $item = M::t('superman_mall_item')->fetch($order_item['itemid']);
                        if ($item) {
                            $this->update_item_total($item, $order_item, '+');
                            $this->update_item_sales($item, $order_item, '-'); //更新销量
                        }
                    }
                }
                M::t('superman_mall_order')->update($_data, array('id' => $mgroupon['orderid']));
                M::t('superman_mall_merge_groupon')->update(array('status' => 3), array('id' => $mgroupon['id']));
            } else {
                M::t('superman_mall_merge_groupon')->update(array('status' => 4), array('id' => $mgroupon['id']));
            }
        } else {
            M::t('superman_mall_merge_groupon')->update(array('status' => 4), array('id' => $mgroupon['id']));
        }
        //组团失败，发送客服消息
        $order['mgroupon_id'] = $mgroupon['id'];
        $order['remain_total'] = $mgroupon['remain_total'];
        $this->send_mgroupon_svcmsg('fail', $order, SupermanUtil::uid2openid($order['uid']));
        return;
    }

    private function update_mgstatus($mgid, $status) {
        if (in_array($status, array(1,2,3,4)) && $mgid > 0) {
            M::t('superman_mall_merge_groupon')->update(array('status' => $status), array('id' => $mgid));
            M::t('superman_mall_merge_groupon')->update(array('status' => $status), array('mgid' => $mgid));
        }
    }
}

$obj = new Superman_mall_doWebHook();