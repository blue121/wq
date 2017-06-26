<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileCheckout extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '扫码核销';
        $do = $do?$do:'checkout';
        $act = in_array($_GPC['act'], array('display', 'check'))?$_GPC['act']:'display';
        $this->checkauth();
        $orderid = intval($_GPC['orderid']);
        if ($orderid <= 0) {
            $this->json(ERRNO::INVALID_REQUEST);
        }
        $order = M::t('superman_mall_order')->fetch($orderid);
        if (!$order) {
            //没有
            $this->json(ERRNO::ORDER_NOT_EXIST);
        }
        if ($order['status'] < 1 || $order['status'] > 3) {
            $this->json(ERRNO::ORDER_CANNOT_CHECKOUT);
        }
        //核销员权限查询
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'shopid' => $order['shopid'],
            'uid' => $_W['member']['uid'],
            'status' => 1,
        );
        $user = M::t('superman_mall_checkout_user')->fetch($filter);
        if (!$user) {
            $this->json(ERRNO::INVALID_REQUEST, '没有该订单的核销权限');
        }
        $filter = array('orderid' => $orderid);
        $checkout_log = M::t('superman_mall_checkout_log')->fetch($filter);
        if ($act == 'display') {
            $buyer = mc_fetch($order['uid'], array('nickname', 'avatar'));
            $order_item = M::t('superman_mall_order_item')->fetchall(array('orderid' => $orderid), '', 0, -1);
            $pay_type = $order['pay_type'] == 1 ? '余额支付': '微信支付';
        } else if ($act == 'check') {
            if ($checkout_log) {
                $this->json(ERRNO::ORDER_HAD_CHECKOUT);
            }
            $data = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $order['shopid'],
                'uid' => $order['uid'],
                'orderid' => $orderid,
                'ordersn' => $order['ordersn'],
                'checkout_id' => $user['id'],
                'type' => 1,
                'remark' => $_GPC['remark'],
                'dateline' => TIMESTAMP,
            );
            $new_id = M::t('superman_mall_checkout_log')->insert($data);
            if ($new_id) {
                $ret = M::t('superman_mall_order')->update(array('status' => 3), array('id' => $orderid));
                if ($ret !== false) {
                    $this->json(ERRNO::OK, '核销成功，跳转中...');
                } else {
                    $this->json(ERRNO::SYSTEM_ERROR, '核销成功，但订单状态更新失败，用户需手动确认收货');
                }
            } else {
                $this->json(ERRNO::SYSTEM_ERROR, '核销失败，请稍后再试');
            }
        }
		include $this->template('checkout');
    }
}
$obj = new Superman_mall_doMobileCheckout;