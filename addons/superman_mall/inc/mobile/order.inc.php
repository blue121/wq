<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileOrder extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $do = $do?$do:'order';
        $act = in_array($_GPC['act'], array('display', 'detail', 'receive', 'cancel', 'delete', 'checkout', 'refund', 'logistics'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $title = '我的订单';
            if (strexists(referer(), 'do=pay')) {
                $back_url = $this->createMobileUrl('order', array('act' => 'display', 'status' => 'all'));
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $list = array();
            if ($_W['member']['uid']) {
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'shopid' => '# != 0'
                );
                $status = in_array($_GPC['status'], array('no_pay', 'no_receive', 'no_comment', 'complete'))?$_GPC['status']:'all';
                switch ($status) {
                    case 'no_pay':
                        $filter['status'] = 0;    //status == 0
                        break;
                    case 'no_receive':
                        $filter['status'] = array(1,2);    //status IN(1,2)
                        break;
                    case 'no_comment':
                        $filter['status'] = 3;    //status == 3
                        break;
                    case 'complete':
                        $filter['status'] = '#>=4';    //status >= 4
                        break;
                    default: //all
                        $status = 'all';
                        $filter['status'] = array(-5, -4, -1, 0, 1, 2, 3, 4, 5);    //status != -2,-3
                }
                if (empty($filter)) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $list = M::t('superman_mall_order')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['order_status_title'] = SupermanUtil::get_order_status_title($li['status'], $li['dispatch_type']);
                        $li['order_items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $li['id']), '', 0, -1);
                        /*if ($li['order_items']) {
                            foreach ($li['order_items'] as &$item) {
                                $item['item'] = M::t('superman_mall_item')->fetch($item['itemid']);
                            }
                            unset($item);
                        }*/
                        $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                    }
                    unset($li);
                }
            }
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($list));
            }
            //print_r($list);die;
        } else if ($act == 'detail') {
            $title = '订单详情';
            $orderid = intval($_GPC['orderid']);
            if (!$orderid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            $shop = M::t('superman_mall_shop')->fetch($order['shopid']);
            if ($order['uid'] != $_W['member']['uid']) {
                $this->json(ERRNO::INVALID_REQUEST);
            }

            $self_refund = true;
            //拼团订单付款后不能申请退款
            if ($order['type'] == 1) {
                $self_refund = false;
                $mgroupon = M::t('superman_mall_merge_groupon')->fetch(array(
                    'orderid' => $orderid
                ));
                if ($mgroupon['mgid'] != 0) {
                    $sponsor = M::t('superman_mall_merge_groupon')->fetch($mgroupon['mgid']);
                    $mgroupon['status'] = $sponsor['status'];
                }
            }
            //到付订单不能申请退款
            if ($order['pay_type'] == 3) {
                $self_refund = false;
            }
            //读取交易设置
            $order_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_ORDER);
            if (isset($order_setting['self_refund_limit']) && $order_setting['self_refund_limit'] > 0) {
                if ((TIMESTAMP - $order['updatetime'])/60 > $order_setting['self_refund_limit']) {
                    //超过可申请退款时间限制
                    $self_refund = false;
                }
            }
            if ($order['dispatch_type'] == 2) {
                $extend = $order['extend']?iunserializer($order['extend']):'';
                if ($extend['myfetch']) {
                    $myfetch = '地址：'.$extend['myfetch']['address'].' '.$extend['myfetch']['title'].'<br>联系方式：'.$extend['myfetch']['mobile'].' '.$extend['myfetch']['username'];
                }
            }
            $order['order_items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $orderid), '', 0, -1);
            $order['is_virtual'] = empty($order['username'])&&empty($order['mobile'])&&empty($order['address'])?true:false;
            if ($order['order_items']) {
                foreach ($order['order_items'] as &$item) {
                    //$item['item'] = M::t('superman_mall_item')->fetch($item['itemid']);
                    $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                }
                unset($item);
            }
        } else if ($act == 'receive') {
            $orderid = intval($_GPC['orderid']);
            if (!$orderid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order || $order['status'] == -2) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            if ($order['uid'] != $_W['member']['uid']) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            if ($order['pay_type'] == 3) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $condition = array(
                'id' => $orderid,
            );
            $data = array(
                'status' => 3,
                'updatetime' => TIMESTAMP,
            );
            M::t('superman_mall_order')->update($data, $condition);
            $extra_info = "\n\n==订单详情==\n";
            $extra_info .= "订单号：{$order['ordersn']}\n";
            $extra_info .= "金额：￥{$order['price']}\n";
            $item_info = '';
            $order_items = M::t('superman_mall_order_item')->fetchall(array(
                'orderid' => $order['id']
            ));
            foreach ($order_items as $item) {
                if ($item_info != '') {
                    $item_info .= '、';
                }
                $item_info .= "{$item['title']}(x{$item['total']})";
            }
            $extra_info .= "商品：{$item_info}\n";
            if ($order['username']) {
                $extra_info .= "收货人：{$order['username']} {$order['mobile']} {$order['address']}\n";
            }
            if ($order['express_no']) {
                $extra_info .= "物流：{$order['express_title']} {$order['express_no']}";
            }
            //商户触发器
            $param = array(
                'action' => 'order_receive',
                'shopid' => $order['shopid'],
                'extra_info' => $extra_info,
                'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $orderid))
            );
            Trigger::init('shop')->send($param);
            //平台触发器
            $extra_info = "\n\n==订单详情==\n";
            $shop = M::t('superman_mall_shop')->fetch($order['shopid']);
            $extra_info .= "商户：{$shop['title']}\n";
            $extra_info .= "订单号：{$order['ordersn']}\n";
            $param = array(
                'action' => 'order_receive',
                'uniacid' => $_W['uniacid'],
                'extra_info' => $extra_info,
                'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $orderid))
            );
            Trigger::init('platform')->send($param);

            $url = $this->createMobileUrl('order', array('status' => 'no_comment'));
            $this->json(ERRNO::OK, '确认成功，跳转中...', array('url' => $url));
        } else if ($act == 'cancel') {
            $orderid = intval($_GPC['orderid']);
            if (!$orderid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order || $order['status'] == -2) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            if ($order['uid'] != $_W['member']['uid']) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            if ($order['pay_credit'] > 0) {
                $log = array(
                    $_W['uid'],
                    '订单'.$order['ordersn'].'支付余额退还',
                    'superman_mall'
                );
                $this->order_refund($order['uid'], $order['pay_credit'], $log);
            }
            $extend = $order['extend']?iunserializer($order['extend']):array();
            //已扣抵现积分退还
            if (isset($extend['discount_status']['cash_deduct'])) {
                $ret = mc_credit_update($order['uid'], $order['credit_type'], $order['cash_credit'], array($order['uid'], '取消订单退还抵现积分', 'superman_mall'));
                if (is_error($ret)) {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
                unset($extend['discount_status']['cash_deduct']);
            }
            $data = array(
                'extend' => $extend?iserializer($extend):'',
                'status' => -1,
                'updatetime' => TIMESTAMP,
            );
            $ret = M::t('superman_mall_order')->update($data, array('id' => $orderid));
            if ($ret !== false) {
                //取消订单，发送模板消息
                $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('order', array(
                    'act' => 'detail',
                    'orderid' => $orderid,
                ));
                $this->send_order_tmplmsg('cancel', $order, $_W['openid'], $url);
                //取消订单归还库存
                $order_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_ORDER);
                if ($order_setting && $order_setting['cancel_update_total']) {
                    $order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $orderid), '', 0, -1);
                    if ($order_items) {
                        foreach ($order_items as $order_item) {
                            $item = M::t('superman_mall_item')->fetch($order_item['itemid']);
                            //拍下减库存的商品归还库存
                            if ($item && $item['minus_total'] == 2) {
                                //拍下减库存的商品归还库存
                                $this->update_item_total($item, $order_item, '+');
                            }
                        }
                    }
                }
            }
            $from_status = in_array($_GPC['from_status'], array('no_pay', 'no_receive', 'no_comment', 'complete'))?$_GPC['from_status']:'all';
            $url = $this->createMobileUrl('order', array('status' => $from_status));
            $this->json(ERRNO::OK, '取消成功，跳转中...', array('url' => $url));
        } else if ($act == 'delete') {
            $orderid = intval($_GPC['orderid']);
            if (!$orderid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order || $order['status'] == -2) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            if ($order['uid'] != $_W['member']['uid']) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $condition = array(
                'id' => $orderid,
            );
            $data = array(
                'status' => -2,
                'updatetime' => TIMESTAMP,
            );
            M::t('superman_mall_order')->update($data, $condition);
            $from_status = in_array($_GPC['from_status'], array('no_pay', 'no_receive', 'no_comment', 'complete'))?$_GPC['from_status']:'all';
            $url = $this->createMobileUrl('order', array('status' => $from_status));
            $this->json(ERRNO::OK, '删除成功，跳转中...', array('url' => $url));
        } else if ($act == 'checkout') {
            $orderid = intval($_GPC['orderid']);
            if ($orderid <= 0) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            if ($order['status'] < 1 || $order['status'] > 3) {
                $this->json(ERRNO::ORDER_CANNOT_CHECKOUT);
            }
            if ($order['uid'] != $_W['member']['uid']) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $order['shopid'],
                'orderid' => $orderid
            );
            $checkout_log = M::t('superman_mall_checkout_log')->fetch($filter);
            if ($checkout_log) {
                $this->json(ERRNO::ORDER_HAD_CHECKOUT);
            }
            $code = $_GPC['code'];
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $order['shopid'],
                'code' => $code
            );
            $check_code = M::t('superman_mall_checkout_code')->fetch($filter);
            if (!$check_code) {
                $this->json(ERRNO::CODE_NOT_EXIST);
            }
            $data = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $order['shopid'],
                'uid' => $order['uid'],
                'orderid' => $orderid,
                'ordersn' => $order['ordersn'],
                'checkout_id' => $check_code['id'],
                'type' => 2,
                'remark' => '',
                'dateline' => TIMESTAMP,
            );
            $new_id = M::t('superman_mall_checkout_log')->insert($data);
            if ($new_id) {
                $ret = M::t('superman_mall_order')->update(array('status' => 3), array('id' => $orderid));
                $extra_info = "\n\n==订单详情==\n";
                $extra_info .= "订单号：{$order['ordersn']}\n";
                $extra_info .= "金额：￥{$order['price']}\n";
                $item_info = '';
                $order_items = M::t('superman_mall_order_item')->fetchall(array(
                    'orderid' => $order['id']
                ));
                foreach ($order_items as $item) {
                    if ($item_info != '') {
                        $item_info .= '、';
                    }
                    $item_info .= "{$item['title']}(x{$item['total']})";
                }
                $extra_info .= "商品：{$item_info}\n";
                if ($order['username']) {
                    $extra_info .= "收货人：{$order['username']} {$order['mobile']} {$order['address']}\n";
                }
                if ($order['express_no']) {
                    $extra_info .= "物流：{$order['express_title']} {$order['express_no']}";
                }
                //商户触发器
                $param = array(
                    'action' => 'order_receive',
                    'shopid' => $order['shopid'],
                    'extra_info' => $extra_info,
                    'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                );
                Trigger::init('shop')->send($param);
                //平台触发器
                $extra_info = "\n\n==订单详情==\n";
                $shop = M::t('superman_mall_shop')->fetch($order['shopid']);
                $extra_info .= "商户：{$shop['title']}\n";
                $extra_info .= "订单号：{$order['ordersn']}\n";
                $param = array(
                    'action' => 'order_receive',
                    'uniacid' => $_W['uniacid'],
                    'extra_info' => $extra_info,
                    'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                );
                Trigger::init('platform')->send($param);
                if ($ret !== false) {
                    $this->json(ERRNO::OK, '核销成功，跳转中...');
                } else {
                    $this->json(ERRNO::SYSTEM_ERROR, '核销成功，但订单状态更新失败，请手动确认收货');
                }
            } else {
                $this->json(ERRNO::SYSTEM_ERROR, '核销失败，请稍后再试');
            }
        } else if ($act == 'refund') {  //发货前退款
            $orderid = intval($_GPC['orderid']);
            $remark = $_GPC['remark'];
            if (!$orderid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }

            /**
             *可申请退款条件
             *- 待支付订单
             *- 本人申请
             *- 非拼团订单
             *- 非到付订单
             *- 在可申请退款时间范围内
            **/
            if ($order['status'] != 1 || $order['uid'] != $_W['member']['uid'] || $order['type'] == 1 || $order['pay_type'] == 3) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            //读取交易设置
            $order_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_ORDER);
            if (isset($order_setting['self_refund_limit']) && $order_setting['self_refund_limit'] > 0) {
                if ((TIMESTAMP - $order['updatetime'])/60 > $order_setting['self_refund_limit']) {
                    //超过可申请退款时间限制
                    $this->json(ERRNO::ORDER_OUT_OF_REFUND_LIMIT);
                }
            }
            $extend = $order['extend']?iunserializer($order['extend']):array();
            $extend['refund']['remark'] = cutstr($remark, 60);
            $extend = iserializer($extend);
            $data = array(
                'status' => -4,
                'extend' => $extend,
                'updatetime' => TIMESTAMP,
            );
            $ret = M::t('superman_mall_order')->update($data, array('id' => $orderid));
            if ($ret !== false) {
                $this->json(ERRNO::OK, '申请成功，跳转中...');
            } else {
                $this->json(ERRNO::SYSTEM_ERROR, '系统错误，请稍后再试');
            }
        } else if ($act == 'logistics') {
            $this->checkauth();
            $title = '查看物流';
            $orderid = intval($_GPC['orderid']);
            if (!$orderid) {
                $this->message('非法请求', '', 'warn');
            }
            $order = M::t('superman_mall_order')->fetch($orderid);
            if (!$order) {
                $this->message('订单不存在或已删除', '', 'warn');
            }
            if ($order['uid'] != $_W['member']['uid']) {
                //分销商查看物流权限判断
                $partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
                if ($partner_setting && !$partner_setting['base']['express_info']) {
                    $this->message('没有权限', '', 'warn');
                }
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $_W['member']['uid'],
                );
                $partner = M::t('superman_mall_partner')->fetch($filter);
                if (!$partner || !in_array($partner['id'], array(
                        $order['partner1_id'],
                        $order['partner2_id'],
                        $order['partner3_id'],
                    ))) {
                    $this->message('没有权限', '', 'warn');
                }
            }
            $express = new SupermanMallExpress('kuaidi100', $order['express_alias'], $order['express_no']);
            $info = $express->query();
            $order['express_info'] = $info?$info:array();
            //print_r($order);
        }
		include $this->template('order');
    }
}
$obj = new Superman_mall_doMobileOrder;