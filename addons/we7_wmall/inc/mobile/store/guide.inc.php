<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'guide';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$url = $this->createMobileUrl('index');
if($_W['we7_wmall']['config']['version'] == 2) {
	$url = $this->createMobileUrl('goods', array('sid' => $_W['we7_wmall']['config']['default_sid']));
}
if($op == 'index') {
	$slides = sys_fetch_slide();
	if(empty($slides)) {
		header('location:' . $url);
		die;
	}
}
include $this->template('guide');

