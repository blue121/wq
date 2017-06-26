<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mgstore';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
mload()->model('manage');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$store = $_W['we7_wmall']['store'];

if($op == 'index') {

}

if($op == 'is_in_business') {
	if($_W['isajax']) {
		$is_in_business =  intval($_GPC['is_in_business']);
		pdo_update('tiny_wmall_store', array('is_in_business' => $is_in_business), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		$info = array('关店成功', '开店成功');
		message(error(0, $info[$is_in_business]), '', 'ajax');
	}
}
include $this->template('manage/store-index');