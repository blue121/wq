<?php 
/**
 * [MicroEngine Mall] Copyright (c) 2014 WE7.CC
 * MicroEngine Mall is NOT a free software, it under the license terms, visited http://www.tule5.com/ for more details.
 */

defined('IN_IA') or exit('Access Denied');

$ops = array('ladder', 'lottery', 'activity','gift','bdelete','merchant','helpbuy','task');
$op_names = array('阶梯团', '抽奖团', '优惠券','拼团有礼','线下核销','商家管理','他人代付','计划任务');
foreach($ops as$key=>$value){
	permissions('do', 'ac', 'op', 'application', 'plugins', $ops[$key], '应用与营销', '插件列表', $op_names[$key]);
}
$op = in_array($op, $ops) ? $op : 'list';

if ($op == 'list') {
	$_W['page']['title'] = '应用和营销  - 应用列表';
	
	include wl_template('application/plugins_list');
}
