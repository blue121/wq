<?php
if(empty($_GPC)){
	require_once '../../../framework/bootstrap.inc.php';
	if($_GPC['c'] == 'utility'){
		require IA_ROOT. '/addons/feng_fightgroups/web/merch.php';
		exit;
	}
}
define('IN_MOBILE', true);
define('SIGN', false);
wl_load()->func('template');
wl_load()->model('setting');
wl_load()->model('permissions');
load()->func('communication');
global $_W,$_GPC;
	
session_start();
$_SESSION['role']='';
define('UNIACID', '');
define('TG_ID', '');
define('TG_MERCHANTID','');
define('MERCHANTID','');
//$resp = ihttp_request('http://weixin.weliam.cn/addons/weliam_manage/api.php', array('type' => 'code','module' => 'feng_fightgroups'),null,1);
$customizedSign = json_decode($resp['content'],1);

$controller = $_GPC['do'];
$action = $_GPC['ac'];
$op = $_GPC['op'];
if(empty($controller) || empty($action)) {
	$_GPC['do'] = $controller = 'store';
	$_GPC['ac'] = $action = 'setting';
}
$getlistFrames = 'get'.$controller.'Frames';
$frames = $getlistFrames();
$top_menus = get_top_menus();
$headerconfig = tgsetting_load();
$file = TG_WEB . 'controller/'.$controller.'/'.$action.'.ctrl.php';
if (!file_exists($file)) {
	header("Location: index.php?c=site&a=entry&m=feng_fightgroups&do=store&ac=setting&op=display&");
	exit;
}
$array_get_domain = app_url('home/auto_task');
$data1 = ihttp_request($array_get_domain,'',null,60);
require $file;

