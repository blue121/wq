<?php

//decode by  http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
load()->func('tpl');
load()->func('file');
load()->model('mc');
load()->model('module');
require IA_ROOT . '/addons/superman_mall/version.php';
require IA_ROOT . '/addons/superman_mall/const.php';
require MODULE_ROOT . '/class/WxpayAPI.class.php';
require MODULE_ROOT . '/class/errno.class.php';
require MODULE_ROOT . '/class/menu.class.php';
require MODULE_ROOT . '/class/nav.class.php';
require MODULE_ROOT . '/class/table.class.php';
require MODULE_ROOT . '/class/util.class.php';
require MODULE_ROOT . '/class/data.class.php';
require MODULE_ROOT . '/class/sms.class.php';
require MODULE_ROOT . '/class/trigger.class.php';
require MODULE_ROOT . '/class/express.class.php';
require MODULE_ROOT . '/class/printer.class.php';
require MODULE_ROOT . '/class/cloud.class.php';
require MODULE_ROOT . '/superman.class.php';
class Superman_mallModuleSite extends Superman
{
}