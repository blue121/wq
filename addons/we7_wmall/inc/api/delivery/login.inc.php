<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC, $_POST;
$mobile = trim($_POST['mobile']);
$password = trim($_POST['password']);
if(empty($mobile) || empty($password)) {
	message(ierror(-1, '手机号或密码为空'), '', 'ajax');
}
$deliveryer = deliveryer_login($mobile, $password);
message($deliveryer, '', 'ajax');

