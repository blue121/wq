<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileShoplist extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        @header('Location: '.$this->createMobileUrl('shop', array('act' => 'list')));
        exit;
    }
}
$obj = new Superman_mall_doMobileShopreg;