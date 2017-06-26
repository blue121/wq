<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '订单列表-' . $_W['we7_wmall']['config']['title'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'stat';

header('location: ' . $this->createWebUrl('ptforder-takeout'));
die;
include $this->template('plateform/order-stat');