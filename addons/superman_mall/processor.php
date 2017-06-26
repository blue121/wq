<?php

//decode by  http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
load()->func('tpl');
load()->func('file');
load()->model('mc');
load()->model('module');
require IA_ROOT . '/addons/superman_mall/const.php';
require MODULE_ROOT . '/class/WxpayAPI.class.php';
require MODULE_ROOT . '/class/errno.class.php';
require MODULE_ROOT . '/class/table.class.php';
require MODULE_ROOT . '/class/util.class.php';
require MODULE_ROOT . '/class/data.class.php';
require MODULE_ROOT . '/class/sms.class.php';
require MODULE_ROOT . '/class/trigger.class.php';
require MODULE_ROOT . '/processor/abstract.class.php';
class Superman_mallModuleProcessor extends WeModuleProcessor
{
	public function respond()
	{
		$rule = M::t('rule')->fetch($this->rule);
		if (!$rule) {
			WeUtility::logging('warning', '[Superman_mallModuleProcessor] not found rule, rid=' . $this->rule);
			return;
		}
		$arr = explode(':', $rule['name']);
		$filepath = MODULE_ROOT . '/processor/' . str_replace('_', '/', $arr[0]) . '.php';
		if (!file_exists($filepath)) {
			WeUtility::logging('warning', '[Superman_mallModuleProcessor] not found processor file, path=' . $filepath . ', rule=' . var_export($rule, true));
			return;
		}
		require $filepath;
		$classname = 'SupermanMallProcessor_' . $arr[0];
		if (!class_exists($classname)) {
			WeUtility::logging('warning', '[Superman_mallModuleProcessor] not found class, name=' . $classname . ', rule=' . var_export($rule, true));
			return;
		}
		$obj = new $classname($arr[1]);
		if (!method_exists($obj, 'respond')) {
			WeUtility::logging('warning', '[Superman_mallModuleProcessor] not found method respond, class=' . $classname . ', rule=' . var_export($rule, true));
			return;
		}
		$obj->message = $this->message;
		$obj->rule = $this->rule;
		$obj->priority = $this->priority;
		$obj->inContext = $this->inContext;
		return $obj->respond();
	}
}