<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileLogout extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        unset($_SESSION);
        session_destroy();
        isetcookie('logout', 1, 60);
        @header('Location: '.$this->createMobileUrl('home'));
        exit;
    }
}
$obj = new Superman_mall_doMobileLogout;