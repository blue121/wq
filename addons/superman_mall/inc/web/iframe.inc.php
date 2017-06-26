<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebIframe extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC;
        $height = !empty($_GPC['_h'])?intval($_GPC['_h']):5000;
        echo '<script>parent.parent.iframeChangeSize('.$height.');</script>';
        exit;
    }
}
$obj = new Superman_mall_doWebIframe;