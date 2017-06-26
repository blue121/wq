<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobilePay extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '订单支付';
        $do = $do?$do:'pay';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        $this->checkauth();
        $uid = $_W['member']['uid'];
        //回退返回待支付订单
        $back_url = $this->createMobileUrl('order', array('status' => 'no_pay'));
        if ($act == 'display') {
            $orderid = intval($_GPC['orderid']);
            if (!$orderid) {
                $this->json(ERRNO::PARAM_ERROR);
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            if ($order['status'] != 0) {
                $this->json(ERRNO::ORDER_NOT_NEED_PAY);
            }
            if ($order['payu_rid'] > 0 && defined('SUPERMAN_CONNECT_BMPAYU')) {
                $query = array(
                    'i' => $_W['uniacid'],
                    'c' => 'entry',
                    'tid' => $order['id'],
                    'title' => '订单号'.$order['ordersn'],
                    'fee' => $order['price'],
                    'ordersn' => $order['ordersn'],
                    'user' => $_W['fans']['openid'],
                    'rid' => $order['payu_rid'],
                    'ms' => 'superman_mall',
                    'do' => 'payex',
                    'm' => 'bm_payu',
                );
                $this->json(ERRNO::OK, '订单创建成功，跳转中...', array(
                    'url' => $_W['siteroot'].'app/index.php?'.http_build_query($query),
                ));
            }
            if ($order['uid'] != $uid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $filter = array(
                'orderid' => $orderid
            );

            $iscash = 1;
            if ($order['shopid'] == 0) {    //父订单
                $child_order = M::t('superman_mall_order')->fetchall(array('pid' => $order['id']), '', 0, -1);
                foreach ($child_order as $child) {
                    //取出该订单下所有商品
                    $items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $child['id']));
                    if (!$items) {
                        $this->json(ERRNO::SYSTEM_ERROR);
                    }
                    foreach ($items as $li) {
                        $item = M::t('superman_mall_item')->fetch($li['itemid']);
                        if (!$item) {
                            $this->json(ERRNO::ITEM_NOT_FOUND);
                        }
                        if (!in_array($item['status'], array(1, 3))) {
                            $this->json(ERRNO::ITEM_NOT_FOUND);
                        }
                        if ($item['minus_total'] == 1) {
                            if ($li['skuid'] > 0) { //有规格的商品
                                $total_check = M::t('superman_mall_item_sku')->fetch($li['skuid']);
                                if (!$total_check || $total_check['total'] <= 0 || ($total_check['total']-$li['total']) < 0) {
                                    $this->json(ERRNO::ITEM_NOT_TOTAL);
                                }
                            } else {    //没规格的商品
                                if ($item['total'] <= 0 || ($item['total'] - $li['total']) < 0) {
                                    $this->json(ERRNO::ITEM_NOT_TOTAL);
                                }
                            }
                        }
                        $iscash = $item['iscash'] == 0?0:$iscash;
                    }
                    unset($li);
                }
            } else {
                //取出该订单下所有商品
                $items = M::t('superman_mall_order_item')->fetchall($filter);
                if (!$items) {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
                foreach ($items as $li) {
                    $item = M::t('superman_mall_item')->fetch($li['itemid']);
                    if (!$item) {
                        $this->json(ERRNO::ITEM_NOT_FOUND);
                    }
                    if (!in_array($item['status'], array(1, 3))) {
                        $this->json(ERRNO::ITEM_NOT_FOUND);
                    }
                    if ($item['minus_total'] == 1) {
                        if ($li['skuid'] > 0) { //有规格的商品
                            $total_check = M::t('superman_mall_item_sku')->fetch($li['skuid']);
                            if (!$total_check || $total_check['total'] <= 0 || ($total_check['total'] - $li['total']) < 0) {
                                $this->json(ERRNO::ITEM_NOT_TOTAL);
                            }
                        } else {    //没规格的商品
                            if ($item['total'] <= 0 || ($item['total'] - $li['total']) < 0) {
                                $this->json(ERRNO::ITEM_NOT_TOTAL);
                            }
                        }
                    }
                    $iscash = $item['iscash'] == 0?0:$iscash;
                }
                unset($li);
            }

            //订单列表页，点击立即支付
            if ($_W['isajax'] && $_GPC['check'] == 'yes' ) {
                $this->json(ERRNO::OK);
            }

            //mall支付设置
            $payments_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYMENTS);

            //余额支付设置
            $setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
            $payment = array();
            if ($setting && isset($setting['payment']) && is_array($setting['payment'])) {
                $payment = $setting['payment'];
            }
            $credit_titles = $this->get_credit_titles();

            //营销设置
            //$discount_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_DISCOUNT);
            if (checksubmit()) {
                $pay_type = $_GPC['pay_type'];
                $credit_pay = $_GPC['credit'];
                $real_price = $order['price'];

                if ($order['pay_credit'] > 0) {
                    if ($order['pay_credit'] >= $order['price']) {
                        //do nothing
                    } else {
                        $real_price -= $order['pay_credit'];
                        if (!in_array($pay_type, array('wechat'))) {
                            $this->json(ERRNO::ORDER_NOT_FOUND_PAYTYPE, '未选择支付方式');
                        }
                        WeUtility::logging('trace', 'real_price='.$real_price);
                    }
                } else {
                    //余额支付
                    if ($_W['member'][$setting['creditbehaviors']['currency']] > 0 && $credit_pay == 1 && (!isset($payments_setting['credit_open']) || $payments_setting['credit_open'] == 1)) {
                        if ($_W['member'][$setting['creditbehaviors']['currency']] >= $order['price']) {
                            $pay_type = 'credit';
                        } else {
                            if (!in_array($pay_type, array('wechat'))) {
                                $this->json(ERRNO::ORDER_NOT_FOUND_PAYTYPE, '未选择支付方式');
                            }
                            $real_price -= $_W['member'][$setting['creditbehaviors']['currency']];
                        }
                    } else {
                        if (!in_array($pay_type, array('wechat', 'cash'))) {
                            $this->json(ERRNO::ORDER_NOT_FOUND_PAYTYPE, '未选择支付方式');
                        }
                    }
                }

                //检查系统支付日志
                $paylog = null;
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'module' => $this->module['name'],
                    'tid' => $orderid,
                );
                $paylog = M::t('core_paylog')->fetch($filter);
                if (!empty($paylog)) {
                    if ($paylog['status'] == '0') { //未支付成功
                        M::t('core_paylog')->delete(array('plid' => $paylog['plid']));
                        $paylog = null;
                    } else {
                        $this->json(ERRNO::ORDER_PAYED);
                    }
                }
                //系统支付日志
                if (empty($paylog)) {
                    $moduleid = empty($this->module['mid']) ? '000000' : sprintf("%06d", $this->module['mid']);
                    $fee = $real_price;
                    $record = array();
                    $record['uniacid'] = $_W['uniacid'];
                    $record['openid'] = $uid;
                    $record['module'] = $this->module['name'];
                    $record['type'] = $pay_type;
                    $record['tid'] = $orderid;
                    $record['uniontid'] = date('YmdHis').$moduleid.random(8,1);
                    $record['fee'] = $fee;
                    $record['status'] = '0';
                    $record['is_usecard'] = 0;
                    $record['card_id'] = 0;
                    $record['card_fee'] = $fee;
                    $record['encrypt_code'] = '';
                    $record['acid'] = $_W['acid'];
                    $plid = M::t('core_paylog')->insert($record);
                    if($plid > 0) {
                        $record['plid'] = $plid;
                        $paylog = $record;
                    } else {
                        $this->json(ERRNO::SYSTEM_ERROR);
                    }
                }

                $credit_log = array(
                    $uid,
                    '订单('.$order['ordersn'].')支付',
                    'superman_mall',
                );
                //bm_payu module error,init wechat pay,update payu_rid 0
                if ($order['payu_rid'] > 0) {
                    M::t('superman_mall_order')->update(array('payu_rid' => 0), array('id' => $order['id']));
                }
                if ($pay_type == 'cash') {
                    if ($order['shopid'] == 0) {    //父订单
                        if (isset($child_order)) {
                            foreach ($child_order as $li) {
                                M::t('superman_mall_order')->update(array(
                                    'pay_type' => 3,
                                    'pay_time' => TIMESTAMP,
                                ), array(
                                    'id' => $li['id']
                                ));
                            }
                        }
                    }
                    M::t('superman_mall_order')->update(array(
                        'pay_type' => 3,
                        'pay_time' => TIMESTAMP,
                    ), array(
                        'id' => $order['id']
                    ));
                    $row = M::t('core_paylog')->fetch(array('plid' => $paylog['plid']));
                    if ($row && $row['status'] == '0') {
                        M::t('core_paylog')->update(array('status' => 1), array('plid' => $paylog['plid']));
                        $method = 'payResult';
                        if (method_exists($this, $method)) {
                            $params = array(
                                'uniacid' => $_W['uniacid'],
                                'uniontid' => $record['uniontid'],
                                'user' => $uid,
                                'from' => 'return',
                                'result' => 'success',
                                'tid' => $orderid,
                                'type' => $pay_type,
                            );
                            exit($this->$method($params));
                        }
                    }
                } else if ($pay_type == 'credit') {
                    $result = mc_credit_update($uid, $setting['creditbehaviors']['currency'], -$real_price, $credit_log);
                    if (is_error($result)) {
                        $this->json(ERRNO::SYSTEM_ERROR, $result['message']);
                    }
                    if ($order['shopid'] == 0) {    //父订单
                        if (isset($child_order)) {
                            foreach ($child_order as $li) {
                                M::t('superman_mall_order')->update(array(
                                    'pay_type' => 1,
                                    'pay_time' => TIMESTAMP,
                                    'pay_credit' => $li['price'],
                                ), array(
                                    'id' => $li['id']
                                ));
                            }
                        }
                    }
                    M::t('superman_mall_order')->update(array(
                        'pay_type' => 1,
                        'pay_time' => TIMESTAMP,
                        'pay_credit' => $real_price,
                    ), array(
                        'id' => $order['id']
                    ));
                    $row = M::t('core_paylog')->fetch(array('plid' => $paylog['plid']));
                    if ($row && $row['status'] == '0') {
                        M::t('core_paylog')->update(array('status' => 1), array('plid' => $paylog['plid']));
                        $method = 'payResult';
                        if (method_exists($this, $method)) {
                            $params = array(
                                'uniacid' => $_W['uniacid'],
                                'uniontid' => $record['uniontid'],
                                'user' => $uid,
                                'from' => 'return',
                                'result' => 'success',
                                'tid' => $orderid,
                                'type' => $pay_type,
                            );
                            exit($this->$method($params));
                        }
                    }
                } else {
                    if ($order['price'] > $real_price + $order['pay_credit']) {    //部分金额需要余额支付
                        if ($order['shopid'] == 0) {
                            $this->json(ERRNO::INVALID_REQUEST);
                        }
                        $diff = $order['price']-$real_price;
                        $result = mc_credit_update($uid, $setting['creditbehaviors']['currency'], -$diff, $credit_log);
                        if (is_error($result)) {
                            $this->json(ERRNO::SYSTEM_ERROR, $result['message']);
                        }
                        M::t('superman_mall_order')->update(array(
                            'pay_credit' => $diff,
                        ), array(
                            'id' => $order['id']
                        ));
                    }
                    $ps = array();
                    $ps['tid'] = $paylog['plid'];
                    $ps['uniontid'] = $paylog['uniontid'];
                    $ps['user'] = $_W['fans']['from_user'];
                    $ps['fee'] = $paylog['card_fee'];
                    $ps['title'] = '订单号:'.$order['ordersn'];
                    if(!empty($plid)) {
                        $tag = array();
                        $tag['acid'] = $_W['acid'];
                        M::t('core_paylog')->update(array('openid' => $_W['fans']['from_user'], 'tag' => iserializer($tag)), array('plid' => $paylog['plid']));
                    }
                    load()->model('payment');
                    load()->func('communication');
                    $sl = base64_encode(json_encode($ps));
                    $auth = sha1($sl . $_W['uniacid'] . $_W['config']['setting']['authkey']);
                    $url = "../payment/wechat/pay.php?i={$_W['uniacid']}&auth={$auth}&ps={$sl}";
                    $this->json(ERRNO::OK, '跳转中...', array('redirect_url' => $url));
                }
            }
        }
		include $this->template('pay');
    }
}
$obj = new Superman_mall_doMobilePay;