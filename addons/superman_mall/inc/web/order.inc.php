<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebOrder extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_order');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('overview', 'display', 'post', 'delete', 'export',
            'refund', 'refund-post', 'refund-new', 'express', 'batch',
        ))?$_GPC['act']:'display';
        $nav['title'] = '订单管理';
        if ($act == 'overview') {
            $nav['subtitle'] = '订单概况';
            $stat = array(
                'order_total' => 0,
                'order_payed_total' => 0,
                'order_payed_rate' => 0,
                'income' => 0.00,
                'payed_income' => 0.00,
                'payed_income_rate' => 0,
            );
            //总订单
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => '#>0', //过滤父订单
                'status' => array(1, 2, 3, 4),

            );
            if (isset($_GPC['datelimit'])) {
                $filter['createtime#1'] = '#>='.strtotime($_GPC['datelimit']['start']);
                $filter['createtime#2'] = '#<='.strtotime($_GPC['datelimit']['end']);
            }
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_order')->count($filter);
            if ($total > 0) {
                $stat['order_total'] = $total;
            }

            //总收入
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => '#>0',
                'status' => array(1, 2, 3, 4),
            );
            if (isset($_GPC['datelimit'])) {
                $filter['createtime#1'] = '#>='.strtotime($_GPC['datelimit']['start']);
                $filter['createtime#2'] = '#<='.strtotime($_GPC['datelimit']['end']);
            }
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_order')->sum($filter, 'price');
            if ($total > 0) {
                //分销佣金
                $commission1 = M::t('superman_mall_order')->sum($filter, 'partner1_commission');
                $commission2 = M::t('superman_mall_order')->sum($filter, 'partner2_commission');
                $commission3 = M::t('superman_mall_order')->sum($filter, 'partner3_commission');
                $com = floatval($commission1) + floatval($commission2) + floatval($commission3);
                $stat['income'] = SupermanUtil::float_format($total-$com);
            }

            $scroll = intval($_GPC['scroll']);
            $st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime('-30day');
            $et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d 23:59:59'));
            $starttime = min($st, $et);
            $endtime = max($st, $et);
            $list = array();
            for ($i = $starttime; $i <= $endtime; $i += (24*3600)) {
                if ($i == $starttime) {          //每日开始时间戳
                    $t1 = $i;
                } else {
                    $t1 = strtotime(date('Y-m-d 0:0:0', $i));
                }
                $t2 = strtotime(date('Y-m-d 23:59:59', $i));

                //日期
                $list['label'][] = date('m-d', $t1);

                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => '#>0',
                    'start_time' => $t1,
                    'end_time' => $t2,
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }

                //待支付
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => '#>0',
                    'status' => 0,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }
                $count1 = M::t('superman_mall_order')->count($filter);
                $list['datasets']['flow1'][] = $count1;

                //待发货
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => '#>0',
                    'status' => 1,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }
                $count2 = M::t('superman_mall_order')->count($filter);
                $list['datasets']['flow2'][] = $count2;

                //已发货
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => '#>0',
                    'status' => 2,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }
                $count3 = M::t('superman_mall_order')->count($filter);
                $list['datasets']['flow3'][] = $count3;

                //已收货
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => '#>0',
                    'status' => 3,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }
                $count4 = M::t('superman_mall_order')->count($filter);
                $list['datasets']['flow4'][] = $count4;
            }
            if ($_W['isajax']) {
                echo json_encode($list);
                exit;
            }
        } else if ($act == 'display') {
            if($_GPC['status'] == 'all') {
                $nav['subtitle'] = '全部订单';
            } else if ($_GPC['pay_type'] == '3') {
                $nav['subtitle'] = '货到付款';
            } else if ($_GPC['status'] == '0') {
                $nav['subtitle'] = '待支付';
            } else if ($_GPC['status'] == '1') {
                $nav['subtitle'] = '待发货';
            } else if ($_GPC['dispatch_type'] == '2') {
                $nav['subtitle'] = '自提订单';
            } else if ($_GPC['type'] == '1') {
                $nav['subtitle'] = '拼团订单';
            } else if ($_GPC['status'] == '-5') {
                $nav['subtitle'] = '已退款';
            } else if ($_GPC['status'] == '-1') {
                $nav['subtitle'] = '已取消';
            } else if ($_GPC['status'] == '-4') {
                $nav['subtitle'] = '申请退款';
            } else if ($_GPC['status'] == '2') {
                $nav['subtitle'] = '已发货';
            } else if ($_GPC['status'] == '3') {
                $nav['subtitle'] = '已收货';
            } else if ($_GPC['status'] == '4') {
                $nav['subtitle'] = '已完成';
            }
            //取消发货
            if (checksubmit('cancel_send')) {
                $orderid = intval($_GPC['orderid']);
                if (!$orderid) {
                    message('非法请求', referer(), 'error');
                }
                M::t('superman_mall_order')->update(array(
                    'status' => 1,
                    'express_alias' => '',
                    'express_title' => '',
                    'express_no' => '',
                ), array('id' => $orderid));
                message('操作成功！', referer(), 'success' );
            }
            //批量删除
            if (checksubmit('batch_delete')) {
                if ($_GPC['ids']) {
                    M::t('superman_mall_order')->delete(array('id' => $_GPC['ids']));
                }
                message('操作成功！', referer(), 'success');
            }
            //快速发货
            if (checksubmit('quick_send')) {
                $orderid = intval($_GPC['orderid']);
                $express_no = $_GPC['express_no'];
                $custom_delivery = $_GPC['custom_delivery'];
                if ($orderid <= 0) {
                    message('非法请求', referer(), 'error');
                }
                $order = M::t('superman_mall_order')->fetch($orderid);
                $sql = "SELECT a.* FROM ".tablename('superman_mall_express_company')." AS a left join ".tablename('superman_mall_shop_express')." AS b on a.id=b.ecomid WHERE b.shopid={$order['shopid']}";
                $list_shop_express = pdo_fetchall($sql, array(), 'id');
                $expressid = $_GPC['expressid'];
                if (!isset($list_shop_express[$expressid]) && !$custom_delivery) {
                    message('快递公司或自定义配送，请至少填写一种配送方式', referer(), 'error');
                }
                $data = array(
                    'express_title' => isset($list_shop_express[$expressid])?$list_shop_express[$expressid]['title']:'',
                    'express_alias' => isset($list_shop_express[$expressid])?$list_shop_express[$expressid]['alias']:'',
                    'express_no' => $express_no,
                    'custom_delivery' => $custom_delivery,
                    'status' => 2,
                );
                $ret = M::t('superman_mall_order')->update($data, array('id' => $orderid));
                if ($ret !== false) {
                    $order = array_merge($order, $data); //更新order变量为最新数据
                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                            'act' => 'detail',
                            'orderid' => $order['id'],
                        ));
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
                    if ($list_shop_express[$expressid]['title']) {
                        $extra_info .= "物流：{$list_shop_express[$expressid]['title']} {$express_no}\n";
                    }
                    if ($custom_delivery) {
                        $extra_info .= "配送：{$custom_delivery}";
                    }
                    //商户触发器
                    $param = array(
                        'action' => 'order_ship',
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
                        'action' => 'order_ship',
                        'uniacid' => $_W['uniacid'],
                        'extra_info' => $extra_info,
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);

                    $this->send_order_tmplmsg('send', $order, SupermanUtil::uid2openid($order['uid']), $url);
                    message('发货成功，订单已更改为已发货状态', referer(), 'success');
                } else {
                    message('系统错误，请稍候再试', referer(), 'error');
                }
            }
            //获取自定义配送信息
            if (isset($_GPC['get_shop_delivery']) && $_GPC['get_shop_delivery'] == 1) {
                $shopid = intval($_GPC['id']);
                $list = M::t('superman_mall_shop_delivery')->fetchall(array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $shopid,
                ), '', 0, -1);
                die(json_encode($list));
            }
            //获取快递公司
            if (isset($_GPC['get_shop_express']) && $_GPC['get_shop_express'] == 1) {
                $shopid = intval($_GPC['id']);
                $sql = "SELECT a.* FROM ".tablename('superman_mall_express_company')." AS a left join ".tablename('superman_mall_shop_express')." AS b on a.id=b.ecomid WHERE b.shopid=$shopid ";
                $list = pdo_fetchall($sql);
                die(json_encode($list));
            }
            //获取物流信息
            if (isset($_GPC['get_logistics']) && $_GPC['get_logistics'] == 1) {
                $orderid = intval($_GPC['orderid']);
                $order = M::t('superman_mall_order')->fetch($orderid);
                $exp = new SupermanMallExpress('kuaidi100', $order['express_alias'], $order['express_no']);
                $result = array(
                    'data' => $exp->query(),
                );
                die(json_encode($result));
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export']) && $_GPC['export'] == 'yes'?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $type = $_GPC['type'];
            $pay_type = in_array($_GPC['pay_type'], array('1', '2', '3'))?$_GPC['pay_type']:'';
            $status = in_array($_GPC['status'], array('-5', '-4', '-2', '-1', '0', '1', '2', '3', '4', '5', 'all'))?$_GPC['status']:'all';
            $dispatch_type = in_array($_GPC['dispatch_type'], array('1', '2', 'all'))?$_GPC['dispatch_type']:'all';
            $ordersn = $_GPC['ordersn']==''?'':trim($_GPC['ordersn']);
            $starttime = $_GPC['createtime']['start'] ? strtotime($_GPC['createtime']['start']) : strtotime('-3month');
            $endtime = $_GPC['createtime']['end'] ? strtotime($_GPC['createtime']['end'])+86399 : strtotime(date('Y-m-d 23:59:59'));
            $title = $_GPC['title']==''?'':$_GPC['title'];
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => '#>0',
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            if ($status != 'all') {
                $filter['status'] = $status;
            }
            if ($dispatch_type != 'all') {
                $filter['dispatch_type'] = $dispatch_type;
                if ($dispatch_type == 2) {
                    $filter['status'] = array(1, 2);
                }
            }
            if ($pay_type != '') {
                $filter['pay_type'] = $pay_type;
            }
            $filter['createtime#1'] = '#>='.$starttime;
            $filter['createtime#2'] = '#<='.$endtime;
            if ($ordersn) {
                $filter['ordersn'] = "# LIKE '%{$ordersn}%'";
            }

            $list = array();
            if ($type == 1) { //拼团订单
                $where = " WHERE o.type=:type AND o.id=m.orderid AND o.uniacid=:uniacid";
                $params = array(
                    ':uniacid' => $_W['uniacid'],
                    ':type' => $type,
                );
                if ($this->shop) {
                    $where .= " AND o.shopid=:shopid";
                    $params[':shopid'] = $this->shop['id'];
                } else {
                    $where .= " AND o.shopid>0";
                }
                $sql = "SELECT o.*,m.id AS mgid FROM ".tablename('superman_mall_order').' AS o,'.tablename('superman_mall_merge_groupon').' AS m';
                if (isset($_GPC['mgid'])) {
                    $where .= " AND (m.id=:id OR m.mgid=:id)";
                    $params[':id'] = $_GPC['mgid'];
                }
                if ($ordersn) {
                    $where .= " AND o.ordersn LIKE '%{$ordersn}%'";
                }
                $count_sql = "SELECT count(*) FROM ".tablename('superman_mall_order').' AS o,'.tablename('superman_mall_merge_groupon').' AS m'.$where;
                $total = pdo_fetchcolumn($count_sql, $params);
                $sql .= " {$where} ORDER BY o.id DESC";
                if ($pagesize > 0) {
                    $sql .= " LIMIT {$start},{$pagesize}";
                }
                $list = pdo_fetchall($sql, $params);
            } else {
                $total = M::t('superman_mall_order')->count($filter);
                $list = M::t('superman_mall_order')->fetchall($filter, '', $start, $pagesize);
            }
            if ($list) {
                foreach ($list as &$li) {
                    if ($type == 1) {
                        $li['mgroupon'] = M::t('superman_mall_merge_groupon')->fetch(array('orderid' => $li['id']));
                        if ($li['status'] >= 1) {
                            if ($li['mgroupon']['mgid'] != 0) { //非团长
                                //取团长
                                $inviter = M::t('superman_mall_merge_groupon')->fetch($li['mgroupon']['mgid']);
                            } else {
                                $inviter = $li['mgroupon'];
                            }
                            $joiner_count = M::t('superman_mall_merge_groupon')->count(array(
                                'mgid' => $inviter['id'],
                                'pay_status' => 1
                            ));
                            if (intval($joiner_count) + 1 >= $inviter['limit']) {   //人数已满
                                $li['mgroupon']['statu'] = '成功';
                            } else if ($inviter['expiretime'] > TIMESTAMP) {
                                $li['mgroupon']['statu'] = '进行中';
                            } else {
                                $li['mgroupon']['statu'] = '失败';
                            }
                        } else {
                            $li['mgroupon']['statu'] = '';
                        }
                    }
                    $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                    $li['member'] = mc_fetch($li['uid'], array('nickname','avatar'));
                    $li['is_virtual'] = empty($li['username'])&&empty($li['mobile'])&&empty($li['address'])?true:false;
                    $li['order_items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $li['id']), '', 0, -1);
                    $li['redirect_express_url'] = $this->createWebUrl('shop', array(
                        'act' => 'switch',
                        'shopid' => $li['shopid'],
                        'referer' => $_W['siteroot'].'web/'.$this->createWebUrl('order', array('act' => 'express')),
                    ));
                }
                unset($li);
                $pager = pagination($total, $pindex, $pagesize);
            }
            if (isset($_GPC['export']) && $_GPC['export'] == 'yes') {
                $this->export_order($list);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            //虚拟商品发货
            if (isset($_GPC['virtualsubmit']) && $_GPC['id'] && $_GPC['value'] != '') {
                $row = M::t('superman_mall_order_item')->fetch($_GPC['id']);
                if ($row) {
                    $extend = $row['extend']?iunserializer($row['extend']):array();
                    $extend['virtual_info'] = $_GPC['value'];
                    M::t('superman_mall_order_item')->update(array(
                        'extend' => iserializer($extend),
                    ), array('id' => $_GPC['id']));
                    exit('success');
                } else {
                    exit('非法请求');
                }
            }
            $id = intval($_GPC['id']);
            $order = M::t('superman_mall_order')->fetch($id);
            if (!$order) {
                message('订单不存在或已删除！', referer(), 'error');
            }
            if ($order['credit_type']) {
                $credit_group = $this->get_credit_titles();
            }
            $extend = $order['extend']?iunserializer($order['extend']):array();
            $order['core_paylog'] = M::t('core_paylog')->fetch(array(
                'tid' => $id,
                'module' => SUPERMAN_MODULE_NAME,
            ));

            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $order['shopid']
            );
            $deliverys = M::t('superman_mall_shop_delivery')->fetchall($filter, '', 0, -1);

            //申请退款处理退款
            if (checksubmit('self_refund')) {
                //订单状态检查
                if ($order['status'] != -4) {
                    message('非法请求', referer(), 'error');
                }
                $refund_type = intval($_GPC['refund_type']);
                if ($refund_type == 1) {  //余额支付
                    $log = array(
                        $_W['uid'],
                        '订单'.$order['ordersn'].'申请退款审核成功',
                        'superman_mall'
                    );
                    $ret = $this->order_refund($order['uid'], $order['price'], $log);
                    if ($ret !== false) {
                        //退款成功
                        $extend['refund']['time'] = TIMESTAMP;      //退款时间
                        $extend['refund']['type'] = $refund_type;   //退款类型
                        $extend['refund']['price'] = $order['price'];//退款金额
                        $extend['refund']['result'] = 'success';    //退款结果
                        /*  退款结果存储（旧）
                        $extend['refund_time'] = TIMESTAMP; //记录退款时间
                        */
                        $_data = array(
                            'extend' => iserializer($extend),
                            'status' => -5,     //已退款
                        );
                        M::t('superman_mall_order')->update($_data, array('id' => $id));
                        //更新库存
                        $order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
                        if ($order_items) {
                            foreach ($order_items as $item) {
                                if ($item['skuid'] > 0) {   //有规格
                                    M::t('superman_mall_item_sku')->increment(array('total'=>$item['total']), array('id' => $item['skuid']));
                                    M::t('superman_mall_item_sku')->decrement(array('sales'=>$item['total']), array('id' => $item['skuid']));
                                }
                                M::t('superman_mall_item')->increment(array('total'=>$item['total']), array('id' => $item['itemid']));
                                M::t('superman_mall_item')->decrement(array('sales'=>$item['total']), array('id' => $item['itemid']));
                            }
                            unset($item, $order_items);
                        }
                        $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                        $credit_type = $setting['creditbehaviors']['currency'];
                        $url = $this->createMobileUrl('creditlog', array('credittype' => $credit_type));
                        $this->send_order_tmplmsg('refund', $order, SupermanUtil::uid2openid($order['uid']), $url);
                        message('退款成功', referer(), 'success');
                    }
                    message('系统错误，退款失败', referer(), 'error');
                } else if ($refund_type == 2) {   //微信支付
                    if ($order['pay_type'] != 2) {
                        message('非微信支付订单，无法返回到微信钱包', referer(), 'error');
                    }
                    if ($order['payment_no'] == '') {
                        message('微信支付单号为空，无法进行退款操作', referer(), 'error');
                    }
                    $setting = uni_setting($_W['uniacid'], array('payment'));
                    $pay = $setting['payment'];
                    if (empty($pay)) {
                        message('请配置和开启微信支付', url('profile/payment'), 'error');
                    }
                    $paycert = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                    if (empty($paycert) || empty($paycert['apiclient_cert']) || empty($paycert['apiclient_key']) || empty($paycert['rootca'])) {
                        message('微信支付证书未设置', $this->createWebUrl('platform', array('act' => 'paycert')), 'error');
                    }
                    $mchid = $pay['wechat']['mchid'];
                    if (empty($mchid)) {
                        message('微信支付商户号(MchId)参数未设置', url('profile/module/setting', array('m' => 'superman_mall')), 'error');
                    }
                    if ($order['pay_credit'] > 0) {
                        $log = array(
                            $_W['uid'],
                            '订单'.$order['ordersn'].'申请退款审核成功',
                            'superman_mall'
                        );
                        $this->order_refund($order['uid'], $order['pay_credit'], $log);
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
                        //退款成功
                        $extend['refund']['time'] = TIMESTAMP;      //退款时间
                        $extend['refund']['type'] = $refund_type;   //退款类型
                        $extend['refund']['price'] = $order['price'];//退款金额
                        $extend['refund']['result'] = 'success';    //退款结果
                        $extend['refund']['refund_id'] = $ret['refund_id'];         //微信退款交易号
                        $extend['refund']['out_refund_no'] = $ret['out_refund_no']; //退款单号
                        /*  退款存储方式（旧）
                        $extend['refund_result'] = is_array($ret)?implode("\n", $ret):$ret;
                        $extend['out_refund_no'] = $ret['out_refund_no'];
                        $extend['refund_id'] = $ret['refund_id'];
                        $extend['refund_time'] = TIMESTAMP; //记录退款时间*/
                        $_data = array(
                            'extend' => iserializer($extend),
                            'status' => -5,     //已退款
                        );
                        M::t('superman_mall_order')->update($_data, array('id' => $id));
                        //更新库存
                        $order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
                        if ($order_items) {
                            foreach ($order_items as $item) {
                                if ($item['skuid'] > 0) {   //有规格
                                    M::t('superman_mall_item_sku')->increment(array('total'=>$item['total']), array('id' => $item['skuid']));
                                    M::t('superman_mall_item_sku')->decrement(array('sales'=>$item['total']), array('id' => $item['skuid']));
                                }
                                M::t('superman_mall_item')->increment(array('total'=>$item['total']), array('id' => $item['itemid']));
                                M::t('superman_mall_item')->decrement(array('sales'=>$item['total']), array('id' => $item['itemid']));
                            }
                            unset($item, $order_items);
                        }
                        message('退款成功', referer(), 'success');
                    } else {
                        //退款失败
                        /*  存储格式（旧）
                         * $extend['refund_result'] = is_array($ret)?implode("\n", $ret):$ret;
                         */
                        $extend['refund']['time'] = TIMESTAMP;      //退款时间
                        $extend['refund']['type'] = $refund_type;   //退款类型
                        $extend['refund']['price'] = $order['price'];//退款金额
                        $extend['refund']['result'] = $ret;         //退款结果

                        $_data = array(
                            'extend' => iserializer($extend),   //失败原因记录
                        );
                        M::t('superman_mall_order')->update($_data, array('id' => $id));
                        message('退款失败', referer(), 'error');
                    }
                }
            }
            if ($order['dispatch_type'] == 2) {
                if ($extend['myfetch']) {
                    $myfetch = '地址：'.$extend['myfetch']['address'].'<br>门店：'.$extend['myfetch']['title'].'<br>联系人：'.$extend['myfetch']['username'].'<br>电话：'.$extend['myfetch']['mobile'];
                }
            }
            $order['is_virtual'] = empty($order['username'])&&empty($order['mobile'])&&empty($order['address'])?true:false;
            $order['order_items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
            if ($order['order_items']) {
                foreach ($order['order_items'] as &$item) {
                    $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                }
                unset($item);
            }
            $order['member'] = mc_fetch($order['uid'], array('nickname', 'avatar'));
            $order['fans'] = mc_fansinfo($order['uid']);
            $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
            $titles = $this->get_credit_titles();
            $credit_title = $titles[$setting['creditbehaviors']['currency']]['title'];
            if ($order['type'] == 1) {   //拼团订单 并且 已支付
                $order['mgroupon']['statu'] = '';
                $order['mgroupon'] = M::t('superman_mall_merge_groupon')->fetch(array('orderid' => $order['id']));
                if ($order['status'] >= 1) {
                    if ($order['mgroupon']['mgid'] != 0) { //非团长
                        //取团长
                        $inviter = M::t('superman_mall_merge_groupon')->fetch($order['mgroupon']['mgid']);
                    } else {
                        $inviter = $order['mgroupon'];
                    }
                    $joiner_count = M::t('superman_mall_merge_groupon')->count(array('mgid' => $inviter['id']));
                    if (intval($joiner_count) + 1 >= $inviter['limit']) {   //人数已满
                        $order['mgroupon']['statu'] = '成功';
                    } else if ($inviter['expiretime'] > TIMESTAMP) {
                        $order['mgroupon']['statu'] = '进行中';
                    } else {
                        $order['mgroupon']['statu'] = '失败';
                    }
                }
                if ($order['mgroupon']['status'] == 3) {
                    //拼团失败自动退款成功
                    $order['mgroupon']['refund_msg'] = '拼团失败自动退款成功';
                } else if ($order['mgroupon']['status'] == 4) {
                    //订单状态不符合自动退款条件，请选择手动退款
                    $order['mgroupon']['refund_msg'] = '订单状态不符合自动退款条件，请选择手动退款';
                }
            }
            if ($order['partner1_id']) {
                $order['partner1'] = M::t('superman_mall_partner')->fetch(array('id' => $order['partner1_id']));
                if ($order['partner1']) {
                    $order['partner1']['group'] =  M::t('superman_mall_partner_group')->fetch(array('id' => $order['partner1']['groupid']));
                    $order['partner1']['member'] =  mc_fetch($order['partner1']['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                }
            }
            if ($order['partner2_id']) {
                $order['partner2'] = M::t('superman_mall_partner')->fetch(array('id' => $order['partner2_id']));
                if ($order['partner2']) {
                    $order['partner2']['group'] =  M::t('superman_mall_partner_group')->fetch(array('id' => $order['partner2']['groupid']));
                    $order['partner2']['member'] =  mc_fetch($order['partner2']['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                }
            }
            if ($order['partner3_id']) {
                $order['partner3'] = M::t('superman_mall_partner')->fetch(array('id' => $order['partner3_id']));
                if ($order['partner3']) {
                    $order['partner3']['group'] =  M::t('superman_mall_partner_group')->fetch(array('id' => $order['partner3']['groupid']));
                    $order['partner3']['member'] =  mc_fetch($order['partner3']['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                }
            }

            //修改订单信息
            if (checksubmit('submit')) {
                //退款条件：取消订单 && 已支付
                if ($_GPC['status'] == '-1' && $_GPC['refund'] == 1 && $order['status'] > 0 && $_GPC['money'] > 0) {
                    $logs = array(
                        $_W['uid'],
                        '订单('.$order['ordersn'].')取消退款',
                    );
                    $this->order_refund($order['uid'], $_GPC['money'], $logs);
                    //商户触发器
                    $param = array(
                        'action' => 'order_refund',
                        'shopid' => $order['shopid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('shop')->send($param);
                    //平台触发器
                    $param = array(
                        'action' => 'order_refund',
                        'uniacid' => $_W['uniacid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);
                    $refund = 1;
                }

                $data = array(
                    'express_title' => $_GPC['express_title'],
                    'express_no' => $_GPC['express_no'],
                    'remark' => trim($_GPC['remark']),
                    'status' => $_GPC['status'],
                    'checkout' => $_GPC['checkout'] == 1?1:0,
                    'custom_delivery' => $_GPC['custom_delivery']
                );
                if ($order['status'] == 0) {
                    $data['price'] = abs($_GPC['price']);
                    $data['express_fee'] = abs($_GPC['express_fee']);
                }
                /*if ($_GPC['virtual_key']) {
                    $data['extend'] = $order['extend'];
                    $data['extend']['virtual_result']['key'] = $_GPC['virtual_key'];
                    $data['extend'] = iserializer($data['extend']);
                }*/
                $ret = M::t('superman_mall_order')->update($data, array('id' => $id));
                if ($ret !== false) {
                    $order = array_merge($order, $data); //更新order变量为最新数据
                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                            'act' => 'detail',
                            'orderid' => $order['id'],
                        ));
                    if ($_GPC['status'] == -1) {
                        if (isset($refund) && $refund == 1) {
                            //订单已退款
                            $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                            $credit_type = $setting['creditbehaviors']['currency'];
                            $url = $this->createMobileUrl('creditlog', array('credittype' => $credit_type));
                            $this->send_order_tmplmsg('refund', $order, SupermanUtil::uid2openid($order['uid']), $url);
                        } else {
                            //订单取消，发送模板消息
                            $this->send_order_tmplmsg('cancel', $order, SupermanUtil::uid2openid($order['uid']), $url);
                        }
                    } else if ($_GPC['status'] == 2) {
                        $extra_info = "\n\n==订单详情==\n";
                        $extra_info .= "订单号：{$order['ordersn']}\n";
                        $extra_info .= "金额：￥{$order['price']}\n";
                        $item_info = '';
                        foreach ($order['order_items'] as $item) {
                            if ($item_info != '') {
                                $item_info .= '、';
                            }
                            $item_info .= "{$item['title']}(x{$item['total']})";
                        }
                        $extra_info .= "商品：{$item_info}\n";
                        if ($order['username']) {
                            $extra_info .= "收货人：{$order['username']} {$order['mobile']} {$order['address']}\n";
                        }
                        if ($_GPC['express_title']) {
                            $extra_info .= "物流：{$_GPC['express_title']} {$_GPC['express_no']}\n";
                        }
                        if ($_GPC['custom_delivery']) {
                            $extra_info .= "配送：{$_GPC['custom_delivery']}";
                        }
                        //商户触发器
                        $param = array(
                            'action' => 'order_ship',
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
                            'action' => 'order_ship',
                            'uniacid' => $_W['uniacid'],
                            'extra_info' => $extra_info,
                            'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                        );
                        Trigger::init('platform')->send($param);
                        //订单发货，发送模板消息
                        $this->send_order_tmplmsg('send', $order, SupermanUtil::uid2openid($order['uid']), $url);
                    }
                } else {
                    message('系统错误，请稍候再试', referer(), 'error');
                }
                message('操作成功！', $this->createWebUrl('order', array('status' => $order['status'])), 'success');
            }
        } else if ($act == 'delete') {
            if (empty($_SERVER['HTTP_REFERER'])) {
                message('非法请求！', '', 'error');
            }
            $id = intval($_GPC['id']);
            M::t('superman_mall_order')->delete(array('id' => $id));
            M::t('superman_mall_order_item')->delete(array('orderid' => $id));
            message('操作成功！', referer(), 'success');
        } else if ($act == 'refund') {
            $nav['subtitle'] = '售后管理';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export']) && $_GPC['export'] == 'yes'?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => '#>0',
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            $list = M::t('superman_mall_service')->fetchall($filter, '', $start, $pagesize);
            $total = M::t('superman_mall_service')->count($filter);
            $pager = pagination($total, $pindex, $pagesize);

            if ($list) {
                foreach ($list as &$item) {
                    $filter = array(
                        'srvid' => $item['id']
                    );
                    $orderby = 'ORDER BY dateline DESC';
                    $progress = M::t('superman_mall_service_progress')->fetch($filter, $orderby);
                    $item['progress'] = $progress;
                    $order_item = M::t('superman_mall_order_item')->fetch($item['oiid']);
                    if ($order_item) {
                        $item['item'] = array(
                            'cover' => $order_item['cover'],
                            'title' => $order_item['title'],
                            'sku' => $order_item['sku']
                        );
                        $order = M::t('superman_mall_order')->fetch($order_item['orderid']);
                        if ($order) {
                            $item['order'] = array(
                                'createtime' => $order['createtime']?date('Y-m-d H:i:s', $order['createtime']):'',
                                'ordersn' => $order['ordersn'],
                                'pay_type' => $order['pay_type']
                            );
                            $item['member'] = mc_fetch($order['uid'], array('nickname', 'avatar'));
                        }
                    }
                }
            }

            //退款
            if (checksubmit()) {
                $refund_price = $_GPC['refund_price'];
                $refund_type = in_array($_GPC['refund_type'], array(1, 2))?$_GPC['refund_type']:0;
                $refund_close = $_GPC['refund_close']=='on'?true:false;
                $srvid = intval($_GPC['srvid']);

                if ($srvid <= 0) {
                    message('非法请求', referer(), 'error');
                }
                if ($refund_type <= 0) {
                    message('非法请求', referer(), 'error');
                }
                $service = M::t('superman_mall_service')->fetch($srvid);
                if (!$service) {
                    message('售后信息不存在', referer(), 'error');
                }
                $order_item = M::t('superman_mall_order_item')->fetch($service['oiid']);
                if (!$order_item) {
                    message('订单商品不存在', referer(), 'error');
                }
                $order = M::t('superman_mall_order')->fetch($order_item['orderid']);
                if (!$order) {
                    message('订单不存在', referer(), 'error');
                }
                if ($refund_type == 2 && $order['pay_type'] != 2) {
                    message('该订单使用余额支付，无法退回微信钱包', referer(), 'error');
                }
                if ($refund_type > 0 && $refund_price > 0) {
                    if ($refund_type == 1) {    //退回到余额类型积分中
                        $credit_log = array(
                            $_W['uid'],
                            '商品('.$order_item['title'].')退款'
                        );
                        $ret = $this->order_refund($order['uid'], $refund_price, $credit_log);
                        if ($ret !== false) {
                            $_data = array(
                                'status' => 5,     //已退款
                            );
                            M::t('superman_mall_order')->update($_data, array('id' => $order['id']));
                        } else {
                            message('系统错误，余额退款失败', referer(), 'error');
                        }
                    } else if ($refund_type == 2) { //退回到微信钱包
                        if ($order['payment_no'] == '') {
                            message('微信支付单号为空，无法进行退款操作', referer(), 'error');
                        }
                        $setting = uni_setting($_W['uniacid'], array('payment'));
                        $pay = $setting['payment'];
                        if (empty($pay)) {
                            message('请配置和开启微信支付', url('profile/payment'), 'error');
                        }
                        $paycert = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                        if (empty($paycert) || empty($paycert['apiclient_cert']) || empty($paycert['apiclient_key']) || empty($paycert['rootca'])) {
                            message('微信支付证书未设置', $this->createWebUrl('platform', array('act' => 'paycert')), 'error');
                        }
                        $mchid = $pay['wechat']['mchid'];
                        if (empty($mchid)) {
                            message('微信支付商户号(MchId)参数未设置', url('profile/module/setting', array('m' => 'superman_mall')), 'error');
                        }
                        $params = array(
                            'appid' => $_W['account']['key'],
                            'mch_id' => $mchid,
                            'nonce_str' => random(32),
                            'transaction_id' => $order['payment_no'],
                            'out_refund_no' => random(22, true),   //退款订单号
                            'total_fee' => $order['price'],
                            'refund_fee' => $refund_price-$order['pay_credit'],
                            'op_user_id' => $mchid,
                        );
                        $extra = array();
                        $extra['sign_key'] = $pay['wechat']['signkey'];
                        $path = SupermanUtil::attachment_path();
                        $extra['apiclient_cert'] = $path.$paycert['apiclient_cert'];
                        $extra['apiclient_key'] = $path.$paycert['apiclient_key'];
                        $extra['rootca'] = $path.$paycert['rootca'];
                        $ret = WxpayAPI::refund($params, $extra);
                        $extend = $order['extend']?iunserializer($order['extend']):array();
                        if (is_array($ret) && isset($ret['success'])) {
                            //退款成功
                            $extend['refund'][] = array(
                                'refund_result' => is_array($ret)?implode("\n", $ret):$ret,
                                'out_refund_no' => $ret['out_refund_no'],
                                'refund_id' => $ret['refund_id'],
                                'refund_time' => TIMESTAMP,
                            );
                            $_data = array(
                                'extend' => iserializer($extend),
                                'status' => 5,     //已退款
                            );
                            M::t('superman_mall_order')->update($_data, array('id' => $order['id']));
                        } else {
                            //退款失败
                            $extend['refund'][] = array(
                                'refund_result' => is_array($ret)?implode("\n", $ret):$ret
                            );
                            $_data = array(
                                'extend' => iserializer($extend),   //失败原因记录
                            );
                            M::t('superman_mall_order')->update($_data, array('id' => $order['id']));
                            message('退款失败，进入该售后查看原因', referer(), 'error');
                        }
                    }
                    M::t('superman_mall_service')->update(array(
                        'updatetime' => TIMESTAMP,
                        'extend' => iserializer(array('money' => $refund_price)),
                    ), array('id' => $srvid));
                    $pro_data = array(
                        'srvid' => $srvid,
                        'userid' => $_W['uid'],
                        'title' => '已退款',
                        'remark' => '已将'.$refund_price.'元退回'.($refund_type==1?'帐户余额':'微信钱包'),
                        'dateline' => TIMESTAMP
                    );
                    M::t('superman_mall_service_progress')->insert($pro_data);
                    //商户触发器
                    $param = array(
                        'action' => 'order_refund',
                        'shopid' => $order['shopid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('shop')->send($param);
                    //平台触发器
                    $param = array(
                        'action' => 'order_refund',
                        'uniacid' => $_W['uniacid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);
                }
                if ($refund_close) {
                    $srv_data = array(
                        'updatetime' => TIMESTAMP,
                        'status' => 1,
                    );
                    M::t('superman_mall_service')->update($srv_data, array('id' => $srvid));
                    $pro_data = array(
                        'dateline' => TIMESTAMP,
                        'srvid' => $srvid,
                        'userid' => $_W['uid'],
                        'title' => '已完成',
                        'remark' => '平台已确认完成'
                    );
                    M::t('superman_mall_service_progress')->insert($pro_data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'refund-post') {
            $nav['subtitle'] = '编辑';
            $srvid = intval($_GPC['srvid']);
            if ($srvid <= 0) {
                message('非法请求', referer(), 'error');
            }
            $service = M::t('superman_mall_service')->fetch($srvid);
            $order_item = M::t('superman_mall_order_item')->fetch($service['oiid']);
            if ($order_item['orderid']) {
                $order = M::t('superman_mall_order')->fetch($order_item['orderid']);
                $order['extend'] = $order['extend']?iunserializer($order['extend']):array();
            }
            $filter = array(
                'srvid' => $srvid
            );
            $orderby = 'ORDER BY dateline ASC';
            $progress = M::t('superman_mall_service_progress')->fetchall($filter, $orderby, 0, -1);
            if (checksubmit('submit')) {
                //更新自定义属性
                if (isset($_GPC['title']) && $_GPC['title']) {
                    $count = count($_GPC['title']);
                    foreach ($_GPC['title'] as $key=>$value) {
                        if ($_GPC['title'][$key] == '' && $_GPC['title'][$key] == '') {
                            continue;
                        }
                        $data = array(
                            'srvid' => $_GPC['srvid'],
                            'userid' => $_W['uid'],
                            'title' => $_GPC['title'][$key],
                            'remark' => $_GPC['remark'][$key],
                            'dateline' => strtotime($_GPC['dateline'][$key]),
                        );
                        M::t('superman_mall_service_progress')->insert($data);
                        M::t('superman_mall_service')->update(array(
                            'updatetime' => TIMESTAMP
                        ), array('id' => $_GPC['srvid']));
                    }
                }
                message('更新成功！', $this->createWebUrl('order',array('act'=>'refund')), 'success');
            }
        } else if ($act == 'refund-new') {
            include $this->template('order/refund-new');
            exit;
        } else if ($act == 'express') {
            $nav['subtitle'] = '快递公司';
            $op = in_array($_GPC['op'], array('display', 'insert', 'delete'))?$_GPC['op']:'display';
            $this->check_web_shop();
            if ($op  == 'display') {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                );
                $total = M::t('superman_mall_express_company')->count($filter);
                $orderby = 'ORDER BY id ASC';
                if ($total) {
                    $list = M::t('superman_mall_express_company')->fetchall($filter, $orderby, '', -1);
                }
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $orderby = 'ORDER BY id ASC';
                if ($total) {
                    $list_shop_express = M::t('superman_mall_shop_express')->fetchall($filter, $orderby, '', -1, 'ecomid');
                }
            } else {
                $id = $_GPC['id'];
                if ($id <= 0) {
                    echo '非法请求';
                    exit;
                }
                if ($op == 'insert') {
                    M::t('superman_mall_shop_express')->insert(
                        array(
                            'shopid' => $this->shop['id'],
                            'ecomid' => $id,
                        )
                    );
                    echo 'success';
                    exit;
                } else if ($op == 'delete') {
                    M::t('superman_mall_shop_express')->delete(
                        array(
                            'shopid' => $this->shop['id'],
                            'ecomid' => $id,
                        )
                    );
                    echo 'success';
                    exit;
                }
            }
        } else if ($act == 'batch') {
            $this->batch_order();
        }
        include $this->template('order/index');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('overview', 'display', 'post', 'delete', 'export',
            'refund', 'refund-post', 'refund-new', 'express', 'batch',
        ))?$_GPC['act']:'display';
        $nav['title'] = '订单管理';
        if ($act == 'overview') {
            $nav['subtitle'] = '订单概况';
            $stat = array(
                'order_total' => 0,
                'order_payed_total' => 0,
                'order_payed_rate' => 0,
                'income' => 0.00,
                'payed_income' => 0.00,
                'payed_income_rate' => 0,
            );

            //总订单
            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => array(1, 2, 3, 4)
            );
            if (isset($_GPC['datelimit'])) {
                $filter['createtime#1'] = '#>='.strtotime($_GPC['datelimit']['start']);
                $filter['createtime#2'] = '#<='.strtotime($_GPC['datelimit']['end']);
            }
            $total = M::t('superman_mall_order')->count($filter);
            if ($total > 0) {
                $stat['order_total'] = $total;
            }

            //总收入
            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => array(1, 2, 3, 4)
            );
            if (isset($_GPC['datelimit'])) {
                $filter['createtime#1'] = '#>='.strtotime($_GPC['datelimit']['start']);
                $filter['createtime#2'] = '#<='.strtotime($_GPC['datelimit']['end']);
            }
            $total = M::t('superman_mall_order')->sum($filter, 'price');
            if ($total > 0) {
                //分销佣金
                $commission1 = M::t('superman_mall_order')->sum($filter, 'partner1_commission');
                $commission2 = M::t('superman_mall_order')->sum($filter, 'partner2_commission');
                $commission3 = M::t('superman_mall_order')->sum($filter, 'partner3_commission');
                $com = floatval($commission1) + floatval($commission2) + floatval($commission3);
                $stat['income'] = SupermanUtil::float_format($total-$com);
            }

            $scroll = intval($_GPC['scroll']);
            $st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime('-30day');
            $et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d 23:59:59'));
            $starttime = min($st, $et);
            $endtime = max($st, $et);
            $list = array();
            for ($i = $starttime; $i <= $endtime; $i += (24*3600)) {
                if ($i == $starttime) {          //每日开始时间戳
                    $t1 = $i;
                } else {
                    $t1 = strtotime(date('Y-m-d 0:0:0', $i));
                }
                $t2 = strtotime(date('Y-m-d 23:59:59', $i));

                //日期
                $list['label'][] = date('m-d', $t1);

                $filter = array(
                    'shopid' => $this->shop['id'],
                    'start_time' => $t1,
                    'end_time' => $t2,
                );

                //待支付
                $count1 = M::t('superman_mall_order')->count(array(
                    'shopid' => $this->shop['id'],
                    'status' => 0,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                ));
                $list['datasets']['flow1'][] = $count1;

                //待发货
                $count2 = M::t('superman_mall_order')->count(array(
                    'shopid' => $this->shop['id'],
                    'status' => 1,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                ));
                $list['datasets']['flow2'][] = $count2;

                //已发货
                $count3 = M::t('superman_mall_order')->count(array(
                    'shopid' => $this->shop['id'],
                    'status' => 2,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                ));
                $list['datasets']['flow3'][] = $count3;

                //已收货
                $count4 = M::t('superman_mall_order')->count(array(
                    'shopid' => $this->shop['id'],
                    'status' => 3,
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                ));
                $list['datasets']['flow4'][] = $count4;
            }
            if ($_W['isajax']) {
                echo json_encode($list);
                exit;
            }
        } else if ($act == 'display') {
            if($_GPC['status'] == 'all') {
                $nav['subtitle'] = '全部订单';
            } else if ($_GPC['status'] == '0') {
                $nav['subtitle'] = '待支付';
            } else if ($_GPC['pay_type'] == '3') {
                $nav['subtitle'] = '货到付款';
            } else if ($_GPC['status'] == '1') {
                $nav['subtitle'] = '待发货';
            } else if ($_GPC['dispatch_type'] == '2') {
                $nav['subtitle'] = '自提订单';
            } else if ($_GPC['type'] == '1') {
                $nav['subtitle'] = '拼团订单';
            } else if ($_GPC['status'] == '-5') {
                $nav['subtitle'] = '已退款';
            } else if ($_GPC['status'] == '-1') {
                $nav['subtitle'] = '已取消';
            } else if ($_GPC['status'] == '-4') {
                $nav['subtitle'] = '申请退款';
            } else if ($_GPC['status'] == '2') {
                $nav['subtitle'] = '已发货';
            } else if ($_GPC['status'] == '3') {
                $nav['subtitle'] = '已收货';
            } else if ($_GPC['status'] == '4') {
                $nav['subtitle'] = '已完成';
            }
            //取消发货
            if (checksubmit('cancel_send')) {
                $orderid = intval($_GPC['orderid']);
                if (!$orderid) {
                    message('非法请求', referer(), 'error');
                }
                M::t('superman_mall_order')->update(array(
                    'status' => 1,
                    'express_alias' => '',
                    'express_title' => '',
                    'express_no' => '',
                ), array('id' => $orderid));
                message('操作成功！', referer(), 'success' );
            }
            //快速发货
            if (checksubmit('quick_send')) {
                $orderid = intval($_GPC['orderid']);
                $express_no = $_GPC['express_no'];
                $custom_delivery = $_GPC['custom_delivery'];
                if ($orderid <= 0) {
                    message('非法请求', referer(), 'error');
                }
                $order = M::t('superman_mall_order')->fetch($orderid);
                $sql = "SELECT a.* FROM ".tablename('superman_mall_express_company')." AS a left join ".tablename('superman_mall_shop_express')." AS b on a.id=b.ecomid WHERE b.shopid={$order['shopid']}";
                $list_shop_express = pdo_fetchall($sql, array(), 'id');
                $expressid = $_GPC['expressid'];
                if (!isset($list_shop_express[$expressid]) && !$custom_delivery) {
                    message('快递公司或自定义配送，请至少填写一种配送方式', referer(), 'error');
                }
                $data = array(
                    'express_title' => isset($list_shop_express[$expressid])?$list_shop_express[$expressid]['title']:'',
                    'express_alias' => isset($list_shop_express[$expressid])?$list_shop_express[$expressid]['alias']:'',
                    'express_no' => $express_no,
                    'custom_delivery' => $custom_delivery,
                    'status' => 2,
                );
                $ret = M::t('superman_mall_order')->update($data, array('id' => $orderid));
                if ($ret !== false) {
                    $order = array_merge($order, $data); //更新order变量为最新数据
                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                            'act' => 'detail',
                            'orderid' => $order['id'],
                        ));
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
                    if ($list_shop_express[$expressid]['title']) {
                        $extra_info .= "物流：{$list_shop_express[$expressid]['title']} {$express_no}\n";
                    }
                    if ($custom_delivery) {
                        $extra_info .= "配送：{$custom_delivery}";
                    }
                    //商户触发器
                    $param = array(
                        'action' => 'order_ship',
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
                        'action' => 'order_ship',
                        'uniacid' => $_W['uniacid'],
                        'extra_info' => $extra_info,
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);

                    $this->send_order_tmplmsg('send', $order, SupermanUtil::uid2openid($order['uid']), $url);
                    message('发货成功，订单已更改为已发货状态', referer(), 'success');
                } else {
                    message('系统错误，请稍候再试', referer(), 'error');
                }
            }
            //获取自定义配送信息
            if (isset($_GPC['get_shop_delivery']) && $_GPC['get_shop_delivery'] == 1) {
                $shopid = intval($_GPC['id']);
                $list = M::t('superman_mall_shop_delivery')->fetchall(array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $shopid,
                ), '', 0, -1);
                die(json_encode($list));
            }
            //获取快递公司
            if (isset($_GPC['get_shop_express']) && $_GPC['get_shop_express'] == 1) {
                $shopid = $this->shop['id'];
                $sql = "SELECT a.* FROM ".tablename('superman_mall_express_company')." AS a left join ".tablename('superman_mall_shop_express')." AS b on a.id=b.ecomid WHERE b.shopid=$shopid ";
                $list_shop_express = pdo_fetchall($sql);
                die(json_encode($list_shop_express));
            }
            //获取物流信息
            if (isset($_GPC['get_logistics']) && $_GPC['get_logistics'] == 1) {
                $orderid = intval($_GPC['orderid']);
                $order = M::t('superman_mall_order')->fetch($orderid);
                $exp = new SupermanMallExpress('kuaidi100', $order['express_alias'], $order['express_no']);
                $result = array(
                    'data' => $exp->query(),
                );
                die(json_encode($result));
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export']) && $_GPC['export'] == 'yes'?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $type = intval($_GPC['type'])?1:0;
            $pay_type = in_array($_GPC['pay_type'], array('1', '2', '3'))?$_GPC['pay_type']:'';
            $status = in_array($_GPC['status'], array('-5', '-4', '-2', '-1', '0', '1', '2', '3', '4', '5', 'all'))?$_GPC['status']:'all';
            $dispatch_type = in_array($_GPC['dispatch_type'], array('1', '2', 'all'))?$_GPC['dispatch_type']:'all';
            $ordersn = $_GPC['ordersn']==''?'':$_GPC['ordersn'];
            $starttime = $_GPC['createtime']['start'] ? strtotime($_GPC['createtime']['start']) : strtotime('-1year');
            $endtime = $_GPC['createtime']['end'] ? strtotime($_GPC['createtime']['end'])+86399 : strtotime(date('Y-m-d 23:59:59'));
            $title = $_GPC['title']==''?'':$_GPC['title'];
            $filter = array(
                'shopid' => $this->shop['id'],
                'type' => $type
            );
            if ($status != 'all') {
                $filter['status'] = $status;
            }
            if ($dispatch_type != 'all') {
                $filter['dispatch_type'] = $dispatch_type;
                if ($dispatch_type == 2) {
                    $filter['status'] = array(1, 2);
                }
            }
            if ($pay_type != '') {
                $filter['pay_type'] = $pay_type;
            }
            $filter['createtime#1'] = '#>='.$starttime;
            $filter['createtime#2'] = '#<='.$endtime;
            if ($ordersn) {
                $filter['ordersn'] = "# LIKE '%{$ordersn}%'";
            }
            $list = array();
            if ($type == 1) { //拼团订单
                $where = " WHERE o.type=1 AND o.id=m.orderid AND o.shopid=:shopid";
                $params = array(
                    ':shopid' => $this->shop['id'],
                );
                $sql = "SELECT o.*,m.id AS mgid FROM ".tablename('superman_mall_order').' AS o,'.tablename('superman_mall_merge_groupon').' AS m';
                if (isset($_GPC['mgid'])) {
                    $where .= " AND (m.id=:id OR m.mgid=:id)";
                    $params[':id'] = $_GPC['mgid'];
                }
                if ($ordersn) {
                    $where .= " AND o.ordersn LIKE '%{$ordersn}%'";
                }
                $count_sql = "SELECT count(*) FROM ".tablename('superman_mall_order').' AS o,'.tablename('superman_mall_merge_groupon').' AS m'.$where;
                $total = pdo_fetchcolumn($count_sql, $params);
                $sql .= " {$where} ORDER BY o.id DESC";
                if ($pagesize > 0) {
                    $sql .= " LIMIT {$start},{$pagesize}";
                }
                $list = pdo_fetchall($sql, $params);
            } else {
                $total = M::t('superman_mall_order')->count($filter);
                $list = M::t('superman_mall_order')->fetchall($filter, '', $start, $pagesize);
            }
            if ($list) {
                foreach ($list as &$li) {
                    if ($type == 1) {
                        $li['mgroupon'] = M::t('superman_mall_merge_groupon')->fetch(array('orderid' => $li['id']));
                        if ($li['status'] >= 1) {
                            if ($li['mgroupon']['mgid'] != 0) { //非团长
                                //取团长
                                $inviter = M::t('superman_mall_merge_groupon')->fetch($li['mgroupon']['mgid']);
                            } else {
                                $inviter = $li['mgroupon'];
                            }
                            $joiner_count = M::t('superman_mall_merge_groupon')->count(array('mgid' => $inviter['id']));
                            if (intval($joiner_count) + 1 >= $inviter['limit']) {   //人数已满
                                $li['mgroupon']['statu'] = '成功';
                            } else if ($inviter['expiretime'] > TIMESTAMP) {
                                $li['mgroupon']['statu'] = '进行中';
                            } else {
                                $li['mgroupon']['statu'] = '失败';
                            }
                        } else {
                            $li['mgroupon']['statu'] = '';
                        }
                    }
                    $li['member'] = mc_fetch($li['uid'], array('nickname','avatar'));
                    $li['is_virtual'] = empty($li['username'])&&empty($li['mobile'])&&empty($li['address'])?true:false;
                    $li['order_items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $li['id']), '', 0, -1);
                    $li['redirect_express_url'] = $this->createWebUrl('order', array(
                        'act' => 'express',
                    ));
                }
                unset($li);
                $pager = pagination($total, $pindex, $pagesize);
            }
            if (isset($_GPC['export']) && $_GPC['export'] == 'yes') {
                $this->export_order($list);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            //虚拟商品发货
            if (isset($_GPC['virtualsubmit']) && $_GPC['id'] && $_GPC['value'] != '') {
                $row = M::t('superman_mall_order_item')->fetch($_GPC['id']);
                if ($row) {
                    $extend = $row['extend']?iunserializer($row['extend']):array();
                    $extend['virtual_info'] = $_GPC['value'];
                    M::t('superman_mall_order_item')->update(array(
                        'extend' => iserializer($extend),
                    ), array('id' => $_GPC['id']));
                    exit('success');
                } else {
                    exit('非法请求');
                }
            }
            $id = intval($_GPC['id']);
            $order = M::t('superman_mall_order')->fetch($id);
            if (!$order) {
                message('订单不存在或已删除！', referer(), 'error');
            }
            if ($order['credit_type']) {
                $credit_group = $this->get_credit_titles();
            }
            $extend = $order['extend']?iunserializer($order['extend']):array();

            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $order['shopid']
            );
            $deliverys = M::t('superman_mall_shop_delivery')->fetchall($filter, '', 0, -1);

            //申请退款处理退款
            if (checksubmit('self_refund')) {
                //订单状态检查
                if ($order['status'] != -4) {
                    message('非法请求', referer(), 'error');
                }
                $refund_type = intval($_GPC['refund_type']);
                if ($refund_type == 1) {  //余额支付
                    $log = array(
                        $_W['uid'],
                        '订单'.$order['ordersn'].'申请退款审核成功',
                        'superman_mall'
                    );
                    $ret = $this->order_refund($order['uid'], $order['price'], $log);
                    if ($ret !== false) {
                        //退款成功
                        $extend['refund']['time'] = TIMESTAMP;      //退款时间
                        $extend['refund']['type'] = $refund_type;   //退款类型
                        $extend['refund']['price'] = $order['price'];//退款金额
                        $extend['refund']['result'] = 'success';    //退款结果
                        /*  退款结果存储（旧）
                        $extend['refund_time'] = TIMESTAMP; //记录退款时间
                        */
                        $_data = array(
                            'extend' => iserializer($extend),
                            'status' => -5,     //已退款
                        );
                        M::t('superman_mall_order')->update($_data, array('id' => $id));
                        //更新库存
                        $order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
                        if ($order_items) {
                            foreach ($order_items as $item) {
                                if ($item['skuid'] > 0) {   //有规格
                                    M::t('superman_mall_item_sku')->increment(array('total'=>$item['total']), array('id' => $item['skuid']));
                                    M::t('superman_mall_item_sku')->decrement(array('sales'=>$item['total']), array('id' => $item['skuid']));
                                }
                                M::t('superman_mall_item')->increment(array('total'=>$item['total']), array('id' => $item['itemid']));
                                M::t('superman_mall_item')->decrement(array('sales'=>$item['total']), array('id' => $item['itemid']));
                            }
                            unset($item, $order_items);
                        }
                        $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                        $credit_type = $setting['creditbehaviors']['currency'];
                        $url = $this->createMobileUrl('creditlog', array('credittype' => $credit_type));
                        $this->send_order_tmplmsg('refund', $order, SupermanUtil::uid2openid($order['uid']), $url);
                        message('退款成功', referer(), 'success');
                    }
                    message('系统错误，退款失败', referer(), 'error');
                } else if ($refund_type == 2) {   //微信支付
                    if ($order['pay_type'] != 2) {
                        message('非微信支付订单，无法返回到微信钱包', referer(), 'error');
                    }
                    if ($order['payment_no'] == '') {
                        message('微信支付单号为空，无法进行退款操作', referer(), 'error');
                    }
                    $setting = uni_setting($_W['uniacid'], array('payment'));
                    $pay = $setting['payment'];
                    if (empty($pay)) {
                        message('请配置和开启微信支付', url('profile/payment'), 'error');
                    }
                    $paycert = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                    if (empty($paycert) || empty($paycert['apiclient_cert']) || empty($paycert['apiclient_key']) || empty($paycert['rootca'])) {
                        message('微信支付证书未设置', $this->createWebUrl('platform', array('act' => 'paycert')), 'error');
                    }
                    $mchid = $pay['wechat']['mchid'];
                    if (empty($mchid)) {
                        message('微信支付商户号(MchId)参数未设置', url('profile/module/setting', array('m' => 'superman_mall')), 'error');
                    }
                    if ($order['pay_credit'] > 0) {
                        $log = array(
                            $_W['uid'],
                            '订单'.$order['ordersn'].'申请退款审核成功',
                            'superman_mall'
                        );
                        $this->order_refund($order['uid'], $order['pay_credit'], $log);
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
                        //退款成功
                        $extend['refund']['time'] = TIMESTAMP;      //退款时间
                        $extend['refund']['type'] = $refund_type;   //退款类型
                        $extend['refund']['price'] = $order['price'];//退款金额
                        $extend['refund']['result'] = 'success';    //退款结果
                        $extend['refund']['refund_id'] = $ret['refund_id'];         //微信退款交易号
                        $extend['refund']['out_refund_no'] = $ret['out_refund_no']; //退款单号
                        /*  退款存储方式（旧）
                        $extend['refund_result'] = is_array($ret)?implode("\n", $ret):$ret;
                        $extend['out_refund_no'] = $ret['out_refund_no'];
                        $extend['refund_id'] = $ret['refund_id'];
                        $extend['refund_time'] = TIMESTAMP; //记录退款时间*/
                        $_data = array(
                            'extend' => iserializer($extend),
                            'status' => -5,     //已退款
                        );
                        M::t('superman_mall_order')->update($_data, array('id' => $id));
                        //更新库存
                        $order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
                        if ($order_items) {
                            foreach ($order_items as $item) {
                                if ($item['skuid'] > 0) {   //有规格
                                    M::t('superman_mall_item_sku')->increment(array('total'=>$item['total']), array('id' => $item['skuid']));
                                    M::t('superman_mall_item_sku')->decrement(array('sales'=>$item['total']), array('id' => $item['skuid']));
                                }
                                M::t('superman_mall_item')->increment(array('total'=>$item['total']), array('id' => $item['itemid']));
                                M::t('superman_mall_item')->decrement(array('sales'=>$item['total']), array('id' => $item['itemid']));
                            }
                            unset($item, $order_items);
                        }
                        message('退款成功', referer(), 'success');
                    } else {
                        //退款失败
                        /*  存储格式（旧）
                         * $extend['refund_result'] = is_array($ret)?implode("\n", $ret):$ret;
                         */
                        $extend['refund']['time'] = TIMESTAMP;      //退款时间
                        $extend['refund']['type'] = $refund_type;   //退款类型
                        $extend['refund']['price'] = $order['price'];//退款金额
                        $extend['refund']['result'] = $ret;         //退款结果

                        $_data = array(
                            'extend' => iserializer($extend),   //失败原因记录
                        );
                        M::t('superman_mall_order')->update($_data, array('id' => $id));
                        message('退款失败', referer(), 'error');
                    }
                }
            }
            if ($order['dispatch_type'] == 2) {
                if ($extend['myfetch']) {
                    $myfetch = '地址：'.$extend['myfetch']['address'].'<br>门店：'.$extend['myfetch']['title'].'<br>联系人：'.$extend['myfetch']['username'].'<br>电话：'.$extend['myfetch']['mobile'];
                }
            }
            $order['is_virtual'] = empty($order['username'])&&empty($order['mobile'])&&empty($order['address'])?true:false;
            $order['order_items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
            if ($order['order_items']) {
                foreach ($order['order_items'] as &$item) {
                    $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                }
                unset($item);
            }
            $order['member'] = mc_fetch($order['uid'], array('nickname', 'avatar'));
            $order['fans'] = mc_fansinfo($order['uid']);
            $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
            $titles = $this->get_credit_titles();
            $credit_title = $titles[$setting['creditbehaviors']['currency']]['title'];
            if ($order['type'] == 1) {   //拼团订单 并且 已支付
                $order['mgroupon']['statu'] = '';
                $order['mgroupon'] = M::t('superman_mall_merge_groupon')->fetch(array('orderid' => $order['id']));
                if ($order['status'] >= 1) {
                    if ($order['mgroupon']['mgid'] != 0) { //非团长
                        //取团长
                        $inviter = M::t('superman_mall_merge_groupon')->fetch($order['mgroupon']['mgid']);
                    } else {
                        $inviter = $order['mgroupon'];
                    }
                    $joiner_count = M::t('superman_mall_merge_groupon')->count(array('mgid' => $inviter['id']));
                    if (intval($joiner_count) + 1 >= $inviter['limit']) {   //人数已满
                        $order['mgroupon']['statu'] = '成功';
                    } else if ($inviter['expiretime'] > TIMESTAMP) {
                        $order['mgroupon']['statu'] = '进行中';
                    } else {
                        $order['mgroupon']['statu'] = '失败';
                    }
                }
                if ($order['mgroupon']['status'] == 3) {
                    //拼团失败自动退款成功
                    $order['mgroupon']['refund_msg'] = '拼团失败自动退款成功';
                } else if ($order['mgroupon']['status'] == 4) {
                    //订单状态不符合自动退款条件，请选择手动退款
                    $order['mgroupon']['refund_msg'] = '订单状态不符合自动退款条件，请选择手动退款';
                }
            }
            if ($order['partner1_id']) {
                $order['partner1'] = M::t('superman_mall_partner')->fetch(array('id' => $order['partner1_id']));
                if ($order['partner1']) {
                    $order['partner1']['group'] =  M::t('superman_mall_partner_group')->fetch(array('id' => $order['partner1']['groupid']));
                    $order['partner1']['member'] =  mc_fetch($order['partner1']['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                }
            }
            if ($order['partner2_id']) {
                $order['partner2'] = M::t('superman_mall_partner')->fetch(array('id' => $order['partner2_id']));
                if ($order['partner2']) {
                    $order['partner2']['group'] =  M::t('superman_mall_partner_group')->fetch(array('id' => $order['partner2']['groupid']));
                    $order['partner2']['member'] =  mc_fetch($order['partner2']['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                }
            }
            if ($order['partner3_id']) {
                $order['partner3'] = M::t('superman_mall_partner')->fetch(array('id' => $order['partner3_id']));
                if ($order['partner3']) {
                    $order['partner3']['group'] =  M::t('superman_mall_partner_group')->fetch(array('id' => $order['partner3']['groupid']));
                    $order['partner3']['member'] =  mc_fetch($order['partner3']['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                }
            }

            //修改订单信息
            if (checksubmit('submit')) {
                //退款条件：取消订单 && 已支付
                if ($_GPC['status'] == '-1' && $_GPC['refund'] == 1 && $order['status'] > 0 && $_GPC['money'] > 0) {
                    $logs = array(
                        $_W['uid'],
                        '订单('.$order['ordersn'].')取消退款',
                    );
                    $this->order_refund($order['uid'], $_GPC['money'], $logs);
                    //商户触发器
                    $param = array(
                        'action' => 'order_refund',
                        'shopid' => $order['shopid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('shop')->send($param);
                    //平台触发器
                    $param = array(
                        'action' => 'order_refund',
                        'uniacid' => $_W['uniacid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);
                    $refund = 1;
                }

                $data = array(
                    'express_title' => $_GPC['express_title'],
                    'express_no' => $_GPC['express_no'],
                    'remark' => trim($_GPC['remark']),
                    'status' => $_GPC['status'],
                    'checkout' => $_GPC['checkout'] == 1?1:0,
                    'custom_delivery' => $_GPC['custom_delivery']
                );
                if ($order['status'] == 0) {
                    $data['price'] = abs($_GPC['price']);
                    $data['express_fee'] = abs($_GPC['express_fee']);
                }
                /*if ($_GPC['virtual_key']) {
                    $data['extend'] = $order['extend'];
                    $data['extend']['virtual_result']['key'] = $_GPC['virtual_key'];
                    $data['extend'] = iserializer($data['extend']);
                }*/
                $ret = M::t('superman_mall_order')->update($data, array('id' => $id));
                if ($ret !== false) {
                    $order = array_merge($order, $data); //更新order变量为最新数据
                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                            'act' => 'detail',
                            'orderid' => $order['id'],
                        ));
                    if ($_GPC['status'] == -1) {
                        if (isset($refund) && $refund == 1) {
                            //订单已退款
                            $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                            $credit_type = $setting['creditbehaviors']['currency'];
                            $url = $this->createMobileUrl('creditlog', array('credittype' => $credit_type));
                            $this->send_order_tmplmsg('refund', $order, SupermanUtil::uid2openid($order['uid']), $url);
                        } else {
                            //订单取消，发送模板消息
                            $this->send_order_tmplmsg('cancel', $order, SupermanUtil::uid2openid($order['uid']), $url);
                        }
                    } else if ($_GPC['status'] == 2) {
                        $extra_info = "\n\n==订单详情==\n";
                        $extra_info .= "订单号：{$order['ordersn']}\n";
                        $extra_info .= "金额：￥{$order['price']}\n";
                        $item_info = '';
                        foreach ($order['order_items'] as $item) {
                            if ($item_info != '') {
                                $item_info .= '、';
                            }
                            $item_info .= "{$item['title']}(x{$item['total']})";
                        }
                        $extra_info .= "商品：{$item_info}\n";
                        if ($order['username']) {
                            $extra_info .= "收货人：{$order['username']} {$order['mobile']} {$order['address']}\n";
                        }
                        if ($_GPC['express_title']) {
                            $extra_info .= "物流：{$_GPC['express_title']} {$_GPC['express_no']}\n";
                        }
                        if ($_GPC['custom_delivery']) {
                            $extra_info .= "配送：{$_GPC['custom_delivery']}";
                        }
                        //商户触发器
                        $param = array(
                            'action' => 'order_ship',
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
                            'action' => 'order_ship',
                            'uniacid' => $_W['uniacid'],
                            'extra_info' => $extra_info,
                            'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                        );
                        Trigger::init('platform')->send($param);
                        //订单发货，发送模板消息
                        $this->send_order_tmplmsg('send', $order, SupermanUtil::uid2openid($order['uid']), $url);
                    }
                } else {
                    message('系统错误，请稍候再试', referer(), 'error');
                }
                message('操作成功！', $this->createWebUrl('order', array('status' => $order['status'])), 'success');
            }
        } else if ($act == 'delete') {
            if (empty($_SERVER['HTTP_REFERER'])) {
                message('非法请求！', '', 'error');
            }
            $id = intval($_GPC['id']);
            M::t('superman_mall_order')->delete(array('id' => $id));
            M::t('superman_mall_order_item')->delete(array('orderid' => $id));
            message('操作成功！', referer(), 'success');
        } else if ($act == 'refund') {
            $nav['subtitle'] = '售后管理';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export']) && $_GPC['export'] == 'yes'?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $this->shop['id']
            );
            $list = M::t('superman_mall_service')->fetchall($filter, '', $start, $pagesize);
            $total = M::t('superman_mall_service')->count($filter);
            $pager = pagination($total, $pindex, $pagesize);

            if ($list) {
                foreach ($list as &$item) {
                    $filter = array(
                        'srvid' => $item['id']
                    );
                    $orderby = 'ORDER BY dateline DESC';
                    $progress = M::t('superman_mall_service_progress')->fetch($filter, $orderby);
                    $item['progress'] = $progress;
                    $order_item = M::t('superman_mall_order_item')->fetch($item['oiid']);
                    if ($order_item) {
                        $item['item'] = array(
                            'cover' => $order_item['cover'],
                            'title' => $order_item['title'],
                            'sku' => $order_item['sku']
                        );
                        $order = M::t('superman_mall_order')->fetch($order_item['orderid']);
                        if ($order) {
                            $item['order'] = array(
                                'createtime' => $order['createtime']?date('Y-m-d H:i:s', $order['createtime']):'',
                                'ordersn' => $order['ordersn'],
                                'pay_type' => $order['pay_type']
                            );
                            $item['member'] = mc_fetch($order['uid'], array('nickname', 'avatar'));
                        }
                    }
                }
            }

            //退款
            if (checksubmit()) {
                $refund_price = $_GPC['refund_price'];
                $refund_type = in_array($_GPC['refund_type'], array(1, 2))?$_GPC['refund_type']:0;
                $refund_close = $_GPC['refund_close']=='on'?true:false;
                $srvid = intval($_GPC['srvid']);

                if ($srvid <= 0) {
                    message('非法请求', referer(), 'error');
                }
                if ($refund_type <= 0) {
                    message('非法请求', referer(), 'error');
                }
                $service = M::t('superman_mall_service')->fetch($srvid);
                if (!$service) {
                    message('售后信息不存在', referer(), 'error');
                }
                $order_item = M::t('superman_mall_order_item')->fetch($service['oiid']);
                if (!$order_item) {
                    message('订单商品不存在', referer(), 'error');
                }
                $order = M::t('superman_mall_order')->fetch($order_item['orderid']);
                if (!$order) {
                    message('订单不存在', referer(), 'error');
                }
                if ($refund_type == 2 && $order['pay_type'] != 2) {
                    message('该订单使用余额支付，无法退回微信钱包', referer(), 'error');
                }
                if ($refund_type > 0 && $refund_price > 0) {
                    if ($refund_type == 1) {    //退回到余额类型积分中
                        $credit_log = array(
                            $_W['uid'],
                            '商品('.$order_item['title'].')退款'
                        );
                        $ret = $this->order_refund($order['uid'], $refund_price, $credit_log);
                        if ($ret !== false) {
                            $_data = array(
                                'status' => 5,     //已退款
                            );
                            M::t('superman_mall_order')->update($_data, array('id' => $order['id']));
                        } else {
                            message('系统错误，余额退款失败', referer(), 'error');
                        }
                    } else if ($refund_type == 2) { //退回到微信钱包
                        if ($order['payment_no'] == '') {
                            message('微信支付单号为空，无法进行退款操作', referer(), 'error');
                        }
                        $setting = uni_setting($_W['uniacid'], array('payment'));
                        $pay = $setting['payment'];
                        if (empty($pay)) {
                            message('请配置和开启微信支付', url('profile/payment'), 'error');
                        }
                        $paycert = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                        if (empty($paycert) || empty($paycert['apiclient_cert']) || empty($paycert['apiclient_key']) || empty($paycert['rootca'])) {
                            message('微信支付证书未设置', $this->createWebUrl('platform', array('act' => 'paycert')), 'error');
                        }
                        $mchid = $pay['wechat']['mchid'];
                        if (empty($mchid)) {
                            message('微信支付商户号(MchId)参数未设置', url('profile/module/setting', array('m' => 'superman_mall')), 'error');
                        }
                        $params = array(
                            'appid' => $_W['account']['key'],
                            'mch_id' => $mchid,
                            'nonce_str' => random(32),
                            'transaction_id' => $order['payment_no'],
                            'out_refund_no' => random(22, true),   //退款订单号
                            'total_fee' => $order['price']-$order['pay_credit'],
                            'refund_fee' => $refund_price,
                            'op_user_id' => $mchid,
                        );
                        $extra = array();
                        $extra['sign_key'] = $pay['wechat']['signkey'];
                        $path = SupermanUtil::attachment_path();
                        $extra['apiclient_cert'] = $path.$paycert['apiclient_cert'];
                        $extra['apiclient_key'] = $path.$paycert['apiclient_key'];
                        $extra['rootca'] = $path.$paycert['rootca'];
                        $ret = WxpayAPI::refund($params, $extra);
                        $extend = $order['extend']?iunserializer($order['extend']):array();
                        if (is_array($ret) && isset($ret['success'])) {
                            //退款成功
                            $extend['refund'][] = array(
                                'refund_result' => is_array($ret)?implode("\n", $ret):$ret,
                                'out_refund_no' => $ret['out_refund_no'],
                                'refund_id' => $ret['refund_id'],
                                'refund_time' => TIMESTAMP,
                            );
                            $_data = array(
                                'extend' => iserializer($extend),
                                'status' => 5,     //已退款
                            );
                            M::t('superman_mall_order')->update($_data, array('id' => $order['id']));
                        } else {
                            //退款失败
                            $extend['refund'][] = array(
                                'refund_result' => is_array($ret)?implode("\n", $ret):$ret
                            );
                            $_data = array(
                                'extend' => iserializer($extend),   //失败原因记录
                            );
                            M::t('superman_mall_order')->update($_data, array('id' => $order['id']));
                            message('退款失败，进入该售后查看原因', referer(), 'error');
                        }
                    }
                    M::t('superman_mall_service')->update(array(
                        'updatetime' => TIMESTAMP,
                        'extend' => iserializer(array('money' => $refund_price)),
                    ), array('id' => $srvid));
                    $pro_data = array(
                        'srvid' => $srvid,
                        'userid' => $_W['uid'],
                        'title' => '已退款',
                        'remark' => '已将'.$refund_price.'元退回'.($refund_type==1?'帐户余额':'微信钱包'),
                        'dateline' => TIMESTAMP
                    );
                    M::t('superman_mall_service_progress')->insert($pro_data);
                    //商户触发器
                    $param = array(
                        'action' => 'order_refund',
                        'shopid' => $order['shopid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('shop')->send($param);
                    //平台触发器
                    $param = array(
                        'action' => 'order_refund',
                        'uniacid' => $_W['uniacid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);
                }
                if ($refund_close) {
                    $srv_data = array(
                        'updatetime' => TIMESTAMP,
                        'status' => 1,
                    );
                    M::t('superman_mall_service')->update($srv_data, array('id' => $srvid));
                    $pro_data = array(
                        'dateline' => TIMESTAMP,
                        'srvid' => $srvid,
                        'userid' => $_W['uid'],
                        'title' => '已完成',
                        'remark' => '商户已确认完成'
                    );
                    M::t('superman_mall_service_progress')->insert($pro_data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'refund-post') {
            $nav['subtitle'] = '编辑';
            $srvid = intval($_GPC['srvid']);
            if ($srvid <= 0) {
                message('非法请求', referer(), 'error');
            }
            $service = M::t('superman_mall_service')->fetch($srvid);
            $order_item = M::t('superman_mall_order_item')->fetch($service['oiid']);
            if ($order_item['orderid']) {
                $order = M::t('superman_mall_order')->fetch($order_item['orderid']);
                $order['extend'] = $order['extend']?iunserializer($order['extend']):array();
            }
            $filter = array(
                'srvid' => $srvid
            );
            $orderby = 'ORDER BY dateline ASC';
            $progress = M::t('superman_mall_service_progress')->fetchall($filter, $orderby, 0, -1);
            if (checksubmit('submit')) {
                //更新自定义属性
                if (isset($_GPC['title']) && $_GPC['title']) {
                    $count = count($_GPC['title']);
                    foreach ($_GPC['title'] as $key=>$value) {
                        if ($_GPC['title'][$key] == '' && $_GPC['title'][$key] == '') {
                            continue;
                        }
                        $data = array(
                            'srvid' => $_GPC['srvid'],
                            'userid' => $_W['uid'],
                            'title' => $_GPC['title'][$key],
                            'remark' => $_GPC['remark'][$key],
                            'dateline' => strtotime($_GPC['dateline'][$key]),
                        );
                        M::t('superman_mall_service_progress')->insert($data);
                        M::t('superman_mall_service')->update(array(
                            'updatetime' => TIMESTAMP
                        ), array('id' => $_GPC['srvid']));
                    }
                }
                message('更新成功！', $this->createWebUrl('order',array('act'=>'refund')), 'success');
            }
        } else if ($act == 'refund-new') {
            //include $this->template('order/refund-new');
            include $this->template('order/refund-new');
            exit;
        } else if ($act == 'express') {
            $nav['subtitle'] = '快递公司';
            $op = in_array($_GPC['op'], array('display', 'insert', 'delete'))?$_GPC['op']:'display';
            if ($op  == 'display') {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                );
                $total = M::t('superman_mall_express_company')->count($filter);
                $orderby = 'ORDER BY id ASC';
                if ($total) {
                    $list = M::t('superman_mall_express_company')->fetchall($filter, $orderby, '', -1);
                }
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $orderby = 'ORDER BY id ASC';
                if ($total) {
                    $list_shop_express = M::t('superman_mall_shop_express')->fetchall($filter, $orderby, '', -1, 'ecomid');
                }
            } else {
                $id = $_GPC['id'];
                if ($id <= 0) {
                    echo '非法请求';
                    exit;
                }
                if ($op == 'insert') {
                    M::t('superman_mall_shop_express')->insert(
                        array(
                            'shopid' => $this->shop['id'],
                            'ecomid' => $id,
                        )
                    );
                    echo 'success';
                    exit;
                } else if ($op == 'delete') {
                    M::t('superman_mall_shop_express')->delete(
                        array(
                            'shopid' => $this->shop['id'],
                            'ecomid' => $id,
                        )
                    );
                    echo 'success';
                    exit;
                }
            }
        } else if ($act == 'batch') {
            $this->batch_order();
        }
        //include $this->template('order/index');
        include $this->template('order/index');
    }
    private function export_order($list) {
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/IOFactory.php';
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/Writer/Excel5.php';
        $resultPHPExcel = new PHPExcel();
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
                    //'color' => array('argb' => 'FFFF0000'),
                ),
            ),
        );
        $style_fill = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => '0xFFFF00')
            ),
        );
        if (isset($list[0]['shop']) && $list[0]['shop']) {
            $resultPHPExcel->getActiveSheet()->getStyle('A1:T1')->applyFromArray(($styleArray + $style_fill));
            $resultPHPExcel->getActiveSheet()->setCellValue('A1', '商户名称');
            $resultPHPExcel->getActiveSheet()->setCellValue('B1', '订单号');
            $resultPHPExcel->getActiveSheet()->setCellValue('C1', '货号');
            $resultPHPExcel->getActiveSheet()->setCellValue('D1', '订单金额');
            $resultPHPExcel->getActiveSheet()->setCellValue('E1', '商品名称');
            $resultPHPExcel->getActiveSheet()->setCellValue('F1', '商品规格');
            $resultPHPExcel->getActiveSheet()->setCellValue('G1', '单价');
            $resultPHPExcel->getActiveSheet()->setCellValue('H1', '数量');
            $resultPHPExcel->getActiveSheet()->setCellValue('I1', '收货人');
            $resultPHPExcel->getActiveSheet()->setCellValue('J1', '收货人地址');
            $resultPHPExcel->getActiveSheet()->setCellValue('K1', '收货人电话');
            $resultPHPExcel->getActiveSheet()->setCellValue('L1', '快递费');
            $resultPHPExcel->getActiveSheet()->setCellValue('M1', '快递公司');
            $resultPHPExcel->getActiveSheet()->setCellValue('N1', '快递单号');
            $resultPHPExcel->getActiveSheet()->setCellValue('O1', '会员ID');
            $resultPHPExcel->getActiveSheet()->setCellValue('P1', '昵称');
            $resultPHPExcel->getActiveSheet()->setCellValue('Q1', '创建时间');
            $resultPHPExcel->getActiveSheet()->setCellValue('R1', '订单状态');
            $resultPHPExcel->getActiveSheet()->setCellValue('S1', '支付方式');
            $resultPHPExcel->getActiveSheet()->setCellValue('T1', '买家留言');
            $i = 2;
            foreach ($list as $item) {
                foreach ($item['order_items'] as $li) {
                    $resultPHPExcel->getActiveSheet()->setCellValue('A' . $i, $item['shop']['title']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('B' . $i, $item['ordersn']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('C' . $i, $li['number']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('D' . $i, $item['price']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('E' . $i, $li['title']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('F' . $i, $li['sku']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('G' . $i, $li['price']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('H' . $i, $li['total']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('I' . $i, $item['username']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('J' . $i, $item['address']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('K' . $i, $item['mobile']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('L' . $i, $item['express_title']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('M' . $i, $item['express_fee']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('N' . $i, $item['express_no']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('O' . $i, $item['uid']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('P' . $i, $item['member']['nickname']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('Q' . $i, date('Y-m-d H:i:s', $item['createtime']));
                    $resultPHPExcel->getActiveSheet()->setCellValue('R' . $i, SupermanUtil::get_order_status_title($item['status']));
                    $resultPHPExcel->getActiveSheet()->setCellValue('S' . $i, SupermanUtil::get_pay_type_title($item['pay_type']));
                    $resultPHPExcel->getActiveSheet()->setCellValue('T' . $i, $item['remark']);
                    $resultPHPExcel->getActiveSheet()->getStyle('A' . $i . ':T' . $i)->applyFromArray($styleArray);
                    $i++;
                }
            }
        } else {
            $resultPHPExcel->getActiveSheet()->getStyle('A1:S1')->applyFromArray(($styleArray + $style_fill));
            $resultPHPExcel->getActiveSheet()->setCellValue('A1', '订单号');
            $resultPHPExcel->getActiveSheet()->setCellValue('B1', '货号');
            $resultPHPExcel->getActiveSheet()->setCellValue('C1', '订单金额');
            $resultPHPExcel->getActiveSheet()->setCellValue('D1', '商品名称');
            $resultPHPExcel->getActiveSheet()->setCellValue('E1', '商品规格');
            $resultPHPExcel->getActiveSheet()->setCellValue('F1', '单价');
            $resultPHPExcel->getActiveSheet()->setCellValue('G1', '数量');
            $resultPHPExcel->getActiveSheet()->setCellValue('H1', '收货人');
            $resultPHPExcel->getActiveSheet()->setCellValue('I1', '收货人地址');
            $resultPHPExcel->getActiveSheet()->setCellValue('J1', '收货人电话');
            $resultPHPExcel->getActiveSheet()->setCellValue('K1', '快递费');
            $resultPHPExcel->getActiveSheet()->setCellValue('L1', '快递公司');
            $resultPHPExcel->getActiveSheet()->setCellValue('M1', '快递单号');
            $resultPHPExcel->getActiveSheet()->setCellValue('N1', '会员ID');
            $resultPHPExcel->getActiveSheet()->setCellValue('O1', '昵称');
            $resultPHPExcel->getActiveSheet()->setCellValue('P1', '创建时间');
            $resultPHPExcel->getActiveSheet()->setCellValue('Q1', '订单状态');
            $resultPHPExcel->getActiveSheet()->setCellValue('R1', '支付方式');
            $resultPHPExcel->getActiveSheet()->setCellValue('S1', '买家留言');
            $i = 2;
            foreach ($list as $item) {
                foreach ($item['order_items'] as $li) {
                    $resultPHPExcel->getActiveSheet()->setCellValue('A' . $i, $item['ordersn']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('B' . $i, $li['number']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('C' . $i, $item['price']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('D' . $i, $li['title']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('E' . $i, $li['sku']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('F' . $i, $li['price']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('G' . $i, $li['total']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('H' . $i, $item['username']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('I' . $i, $item['address']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('J' . $i, $item['mobile']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('K' . $i, $item['express_title']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('L' . $i, $item['express_fee']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('M' . $i, $item['express_no']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('N' . $i, $item['uid']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('O' . $i, $item['member']['nickname']);
                    $resultPHPExcel->getActiveSheet()->setCellValue('P' . $i, date('Y-m-d H:i:s', $item['createtime']));
                    $resultPHPExcel->getActiveSheet()->setCellValue('Q' . $i, SupermanUtil::get_order_status_title($item['status']));
                    $resultPHPExcel->getActiveSheet()->setCellValue('R' . $i, SupermanUtil::get_pay_type_title($item['pay_type']));
                    $resultPHPExcel->getActiveSheet()->setCellValue('S' . $i, $item['remark']);
                    $resultPHPExcel->getActiveSheet()->getStyle('A' . $i . ':S' . $i)->applyFromArray($styleArray);
                    $i++;
                }
            }
        }
        $resultPHPExcel->getActiveSheet()->setCellValue('A' . $i, '订单总数：' . count($list));
        $resultPHPExcel->getActiveSheet()->getStyle('A' . $i)->applyFromArray(array('font' => array('bold' => true)));
        $outputFileName = 'data'.date('YmdHi').'.xls';
        $xlsWriter = new PHPExcel_Writer_Excel5($resultPHPExcel);
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
        $xlsWriter->save("php://output");
        exit;
    }
    //批量发货
    private function batch_order() {
        global $_W;
        if (checksubmit()) {
            $_W['setting']['upload']['image']['limit'] = 10000; //KB
            $_W['setting']['upload']['image']['extentions'][] = 'csv';
            if (!isset($_FILES['batch_order']['tmp_name']) || $_FILES['batch_order']['tmp_name'] == '') {
                message('-1:请上传合法文件！', referer(), 'error');
            }
            //读取文件内容
            $contents = file_get_contents($_FILES['batch_order']['tmp_name']);
            if (!$contents) {
                message('-2:文件内容为空，请上传合法文件！', referer(), 'error');
            }
            $contents = iconv('gbk', 'utf-8', $contents);
            $list = explode("\n", $contents);
            if ($list) {
                $filter = array(
                    'uniacid' => $_W['uniacid']
                );
                $express_company = M::t('superman_mall_express_company')->fetchall($filter, '', 0, -1, 'title');
                foreach ($list as $k => $row) {
                    $li = explode(",", $row);
                    if ($k <= 1) {
                        continue;
                    }
                    if (count($li) > 4) {
                        WeUtility::logging('warning', '[batch_order] file format error, fields count='.count($li));
                        continue;
                    }
                    $ordersn = $li[0];
                    $express_title = $li[1];
                    $express_no = $li[2];
                    if (isset($li[3])) {
                        $custom_delivery = $li[3];
                    }

                    $order = M::t('superman_mall_order')->fetch(array(
                        'ordersn' => $ordersn
                    ));
                    //是否检查是否该商户的订单
                    if (!$order) {
                        WeUtility::logging('warning', '[batch_order] not found order, ordersn='.$ordersn);
                        continue;
                    }
                    if (defined('IN_SUPERMAN_MALL_ADMIN') && $order['shopid'] != $this->shop['id']) {
                        WeUtility::logging('warning', '[batch_order] invalid shopid, order_shopid='.$order['shopid'].', shopid='.$this->shop['id']);
                        continue;
                    }
                    //更新订单状态
                    $data = array(
                        'status' => 2,
                        'updatetime' => TIMESTAMP
                    );
                    if ($express_title != '不需要快递') {  //需要快递
                        $data['express_title'] = $express_title;
                        $data['express_alias'] = isset($express_company[$express_title])?$express_company[$express_title]['alias']:'';
                        $data['express_no'] = $express_no;
                    }
                    if (isset($custom_delivery)) { //自定义配送
                        $data['custom_delivery'] = $custom_delivery;
                    }
                    M::t('superman_mall_order')->update($data, array(
                        'id' => $order['id']
                    ));

                    //订单发货触发器（平台，商户）
                    $extra_info = "\n\n==订单详情==\n";
                    $extra_info .= "订单号：{$order['ordersn']}\n";
                    $extra_info .= "金额：￥{$order['price']}\n";
                    $item_info = '';
                    $order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $order['id']), '', 0, -1);
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
                    if (isset($data['express_title'])) {
                        $extra_info .= "物流：{$data['express_title']} {$data['express_no']}";
                    }
                    //商户触发器
                    $param = array(
                        'action' => 'order_ship',
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
                        'action' => 'order_ship',
                        'uniacid' => $_W['uniacid'],
                        'extra_info' => $extra_info,
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);

                    //发货模版消息
                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                            'act' => 'detail',
                            'orderid' => $order['id'],
                        ));
                    $this->send_order_tmplmsg('send', $order, SupermanUtil::uid2openid($order['uid']), $url);
                }
            }
            message('操作成功！', referer(), 'success');
        }
    }
}
$obj = new Superman_mall_doWebOrder;