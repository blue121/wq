<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '商家入驻-' . $_W['we7_wmall']['config']['title'];

$sid = $store['id'];
$do = 'merchantjoin';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';


include $this->template('plateform/merchantjoin');