<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebReadme extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
		include $this->template('web/readme');
    }
}

$obj = new Superman_mall_doWebReadme;
