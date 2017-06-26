<?php
function getTopDomainhuo()
{
	$host = $_SERVER['HTTP_HOST'];
	$host = strtolower($host);
	if (strpos($host, '/') !== false) {
		$parse = @parse_url($host);
		$host = $parse['host'];
	}
	$topleveldomaindb = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'top', 'wang', 'pro', 'name', 'so', 'in', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me', 'co', 'asia');
	$str = '';
	foreach ($topleveldomaindb as $v) {
		$str .= ($str ? '|' : '') . $v;
	}
	$matchstr = '[^\\.]+\\.(?:(' . $str . ')|\\w{2}|((' . $str . ')\\.\\w{2}))$';
	if (preg_match('/' . $matchstr . '/ies', $host, $matchs)) {
		$domain = $matchs[0];
	} else {
		$domain = $host;
	}
	return $domain;
}
unset($domain);
define('IN_SYS', true);
require '../framework/bootstrap.inc.php';
load()->web('common');
load()->web('template');
header('Content-Type: text/html; charset=UTF-8');
$uniacid = intval($_GPC['i']);
if (empty($uniacid)) {
	die('Access Denied.');
}
$site = WeUtility::createModuleSite('ewei_shopv2');
$_GPC['c'] = 'site';
$_GPC['a'] = 'entry';
$_GPC['m'] = 'ewei_shopv2';
$_GPC['do'] = 'web';
if (!isset($_GPC['r'])) {
	$_GPC['r'] = 'cashier.manage.index';
} else {
	$_GPC['r'] = 'cashier.manage.' . $_GPC['r'];
}
$_W['uniacid'] = (int) $_GPC['i'];
$_W['acid'] = (int) $_GPC['i'];
if (!is_error($site)) {
	$method = 'doWebWeb';
	$site->uniacid = $uniacid;
	$site->inMobile = false;
	if (method_exists($site, $method)) {
		$site->{$method}();
		die;
	}
}