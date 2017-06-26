<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('deliveryer');
$do = 'index';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
checkdeliveryer();
header('location:' . $this->createMobileUrl('dyorder'));
die;