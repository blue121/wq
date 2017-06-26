<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$sid = intval($_GPC['sid']);
isetcookie('__sid', $sid, 86400 * 7);
$url = $this->createWebUrl('order');
$forward = trim($_GPC['forward']);
if($forward == 'clerk') {
	$url = $this->createWebUrl('clerk', array('role' => 'manager', 'op' => 'post'));
}
$account = store_account($sid);
if(empty($account)) {
	$settle_config = sys_settle_config();
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'fee_limit' => $settle_config['get_cash_fee_limit'],
		'fee_rate' => $settle_config['get_cash_fee_rate'],
		'fee_min' => $settle_config['get_cash_fee_min'],
		'fee_max' => $settle_config['get_cash_fee_max'],
	);
	pdo_insert('tiny_wmall_store_account', $insert);
}
header('location: ' . $url);
exit();
