<?php
/**
 * 微擎外送模块
 *
 * @author 微擎团队&TuLe wei系列
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');

class We7_wmallModule extends WeModule {
	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		include $this->template('settings');
	}
}
