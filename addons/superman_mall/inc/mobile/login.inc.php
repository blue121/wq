<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileLogin extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $do = $do?$do:'login';
        $act = in_array($_GPC['act'], array('check'))?$_GPC['act']:'check';
		if ($act == 'check') {
            $this->checkauth();
            $this->json(ERRNO::OK);
        }
    }
}
$obj = new Superman_mall_doMobileLogin;