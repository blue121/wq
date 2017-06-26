<?php
require '../../../framework/bootstrap.inc.php';
$site = WeUtility::createModuleSite('junsion_exclusivenews');
if(!is_error($site)) {
	$method = 'yunPayResult';
	if (method_exists($site, $method)) {
		$ret = array();
		$ret['from'] = 'notify';
		$ret['tid'] = $_REQUEST['i2'];
		$ret['fee'] = $_REQUEST['i1'];
		$ret['sign'] = $_REQUEST['i3'];
		$ret['trade_no'] = $_REQUEST['i4'];
		$site->$method($ret);
		exit();
	}
}
