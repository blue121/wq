<?php
defined('IN_IA') or exit('Access Denied');
$orderid = intval($_GPC['id']);
if($orderid){
	$order = model_order::getSingleOrder($orderid, '*');
	$goods = model_goods::getSingleGoods($order['g_id'], '*');
	$order['merchant_name'] = $order['merchantname'];
	if($order['is_hexiao']==1){
		foreach($goods['hexiao_id'] as$key=>$value){
			$stores[$key] =  pdo_fetch("select * from".tablename('tg_store')."where id ='{$value}' and uniacid='{$_W['uniacid']}'");
		}
		$qrcodeurl = urlencode($_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=feng_fightgroups&do=order&ac=check&mid=' . $order['orderno']);
	}
}
include wl_template('order/qrcode');
