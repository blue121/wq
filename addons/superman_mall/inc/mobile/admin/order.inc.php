<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
$this->check_user_permission('superman_mall_menu_mobile_admin_order');
if ($act == 'display') {
    $title = '订单列表';
    $pindex = max(1, intval($_GPC['page']));
    $pagesize = 10;
    $start = ($pindex - 1) * $pagesize;
    $status = in_array($_GPC['status'], array('-5', '-4', '-3', '-2', '-1', '0', '1', '2', '3', '4', '5'))?$_GPC['status']:'all';
    $type = in_array($_GPC['type'], array('0', '1'))?$_GPC['type']:'all';  //1为拼团订单，0为普通订单，'all'为全部订单
    $pay_type = in_array($_GPC['pay_type'], array('1', '2', '3'))?$_GPC['pay_type']:'';
    $dispatch_type = in_array($_GPC['dispatch_type'], array('1', '2', 'all'))?$_GPC['dispatch_type']:'all';
    $keyword = $_GPC['keyword'];
    if ($type == 1) {   //拼团订单
        $sql = "SELECT a.*,b.mgid,b.status AS mg_status FROM ".tablename('superman_mall_order')." AS a,".tablename('superman_mall_merge_groupon')." AS b";
        $where = " WHERE a.id=b.orderid AND a.shopid=:shopid";
        $params = array(
            ':shopid' => $this->shop['id'],
        );
        if ($dispatch_type == 2) {
            $where .= " AND a.status IN (1,2)";
        } else if ($status != 'all') {
            $params[':status'] = $status;
            $where .= " AND a.status=:status";
        }
        if ($pay_type != '') {
            $params[':pay_type'] = $pay_type;
            $where .= " AND a.pay_type=:pay_type";
        }
        if ($dispatch_type != 'all') {
            $params[':dispatch_type'] = $dispatch_type;
            $where .= " AND a.dispatch_type=:dispatch_type";
        }
        if ($keyword != '') {
            $where .= ' AND a.ordersn LIKE "%'.$keyword.'%"';
        }
        $orderby = " ORDER BY a.updatetime DESC,a.id DESC LIMIT {$start},{$pagesize}";

        $list = pdo_fetchall($sql.$where.$orderby, $params);
    } else {
        $filter = array(
            'shopid' => $this->shop['id'],
        );
        if ($type == 0) {
            $filter['type'] = 0;
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
        if ($keyword != '') {
            $filter['ordersn'] = '# LIKE "%'.$keyword.'%"';
        }
        $orderby = " ORDER BY updatetime DESC,id DESC";
        $list = M::t('superman_mall_order')->fetchall($filter, $orderby, $start, $pagesize);
    }
    if ($list) {
        foreach ($list as &$li) {
            $li['items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $li['id']), '', 0, -1);
            $li['status_title'] = SupermanUtil::get_order_status_title($li['status'], $li['dispatch_type']);
            if ($li['type'] == 1) { //拼团订单
                if ($li['mgid'] > 0) {  //团员
                    $sponsor = M::t('superman_mall_merge_groupon')->fetch($li['mgid']); //团长记录拼团状态
                    if ($sponsor) {
                        $li['mg_status'] = $sponsor['status'];
                    }
                }
             }
            unset($li);
        }
    }

    //加载更多
    if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
        die(json_encode($list));
    }
} else if ($act == 'post') {
    $title = '订单详情';
    $id = intval($_GPC['id']);
    if ($id <= 0) {
        $this->json(ERRNO::INVALID_REQUEST);
    }
    $order = M::t('superman_mall_order')->fetch($id);
    if (!$order || $order['shopid'] != $this->shop['id']) {
        $this->json(ERRNO::INVALID_REQUEST);
    }
    $order['status_title'] = SupermanUtil::get_order_status_title($order['status'], $order['dispatch_type']);
    $items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
    $total_price = 0;
    if ($items) {
        foreach ($items as $item) {
            $total_price += $item['price'] * $item['total'];
        }
    }
} else if ($act == 'send') {
    $title = '快速发货';
    $id = intval($_GPC['id']);
    if ($id <= 0) {
        $this->message('非法请求', '', 'warn');
    }
    $order = M::t('superman_mall_order')->fetch($id);
    if (!$order || $order['shopid'] != $this->shop['id']) {
        $this->message('非法请求', '', 'warn');
    }
    $order['status_title'] = SupermanUtil::get_order_status_title($order['status'], $order['dispatch_type']);
    $items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id), '', 0, -1);
    $total_price = 0;
    if ($items) {
        foreach ($items as $item) {
            $total_price += $item['price'] * $item['total'];
        }
    }
    //获取快递公司
    $sql = "SELECT a.* FROM ".tablename('superman_mall_express_company')." AS a left join ".tablename('superman_mall_shop_express')." AS b on a.id=b.ecomid WHERE b.shopid=:shopid";
    $params = array(
        ':shopid' => $this->shop['id']
    );
    $shop_express = pdo_fetchall($sql, $params);

    if (checksubmit('submit1')) {
        $delivery_type = $_GPC['delivery_type'];
        if ($delivery_type == 1) {
            $express_no = $_GPC['express_no'];
            $key = $_GPC['express_company'];
            if (!isset($shop_express[$key])) {
                $this->message('快递公司选择错误', '', 'warn');
            }
            if (!$express_no) {
                $this->message('快递单号不能为空', '', 'warn');
            }
            $_data = array(
                'express_title' => $shop_express[$key]['title'],
                'express_no' => $express_no,
                'express_alias' => $shop_express[$key]['alias']
            );
        }
        $_data['status'] = 2;
        $_data['updatetime'] = TIMESTAMP;
        $ret = M::t('superman_mall_order')->update($_data, array('id' => $id));
        if ($ret !== false) {
            $order = array_merge($order, $_data); //更新order变量为最新数据
            $extra_info = "\n\n==订单详情==\n";
            $extra_info .= "订单号：{$order['ordersn']}\n";
            $extra_info .= "金额：￥{$order['price']}\n";
            $item_info = '';
            if ($order['order_items']) {
                foreach ($order['order_items'] as $item) {
                    if ($item_info != '') {
                        $item_info .= '、';
                    }
                    $item_info .= "{$item['title']}(x{$item['total']})";
                }
            }
            $extra_info .= "商品：{$item_info}\n";
            if ($order['username']) {
                $extra_info .= "收货人：{$order['username']} {$order['mobile']} {$order['address']}\n";
            }
            if ($delivery_type == 1) {
                if ($shop_express[$key]['title']) {
                    $extra_info .= "物流：{$shop_express[$key]['title']} {$express_no}\n";
                }
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
            $extra_info .= "商户：{$this->shop['title']}\n";
            $extra_info .= "订单号：{$order['ordersn']}\n";
            $param = array(
                'action' => 'order_ship',
                'uniacid' => $_W['uniacid'],
                'extra_info' => $extra_info,
                'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
            );
            Trigger::init('platform')->send($param);
            //订单发货，发送模板消息
            $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                    'act' => 'detail',
                    'orderid' => $order['id'],
                ));
            $this->send_order_tmplmsg('send', $order, SupermanUtil::uid2openid($order['uid']), $url);
            $this->message('发货成功，跳转中...', $this->createMobileUrl('admin', array('route' => 'order.display', 'status' => $_GPC['status'], 'type' => $_GPC['type'], 'dispatch_type' => $_GPC['dispatch_type'], 'pay_type' => $_GPC['pay_type'])), 'success');
        }
    }
} else if ($act == 'modify') {  //修改
    $id = intval($_GPC['id']);
    if ($id <= 0) {
        $this->json(ERRNO::INVALID_REQUEST);
    }
    $order = M::t('superman_mall_order')->fetch($id);
    $field = $_GPC['field'];
    $value = $_GPC['value'];
    if (!$order || $order['shopid'] != $this->shop['id'] || !in_array($field, array('price', 'close')) || $value <= 0) {
        $this->json(ERRNO::INVALID_REQUEST);
    }
    if ($field == 'price') {
        $ret = M::t('superman_mall_order')->update(array('price' => SupermanUtil::float_format($value)), array('id' => $id));
    } else if ($field == 'close') {
        $ret = M::t('superman_mall_order')->update(array('status' => -3), array('id' => $id));
    }
    if ($ret !== false) {
        $this->json(ERRNO::OK);
    } else {
        $this->json(ERRNO::SYSTEM_ERROR);
    }
}
include $this->template('order/index');