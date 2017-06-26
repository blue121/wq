<?php
defined('IN_IA') || die('Access Denied');
class Ld_CardModule extends WeModule
{
	public function settingsDisplay($settings)
	{
		global $_W;
		global $_GPC;
		$settings = $this->module['config'];
		if (checksubmit()) {
			$cfg = array('sendtype' => $_GPC['sendtype'], 'appid' => $_GPC['appid'], 'appSecret' => $_GPC['appSecret'], 'mch_id' => $_GPC['mch_id'], 'sub_mch_id' => $_GPC['sub_mch_id'], 'appkey' => $_GPC['appkey'], 'ip' => $_GPC['ip'], 'act_name' => $_GPC['act_name'], 'remark' => $_GPC['remark'], 'wishing' => $_GPC['wishing'], 'send_name' => $_GPC['send_name'], 'id' => $_GPC['id'], 'tplid' => $_GPC['tplid'], 'title' => $_GPC['title'], 'desc' => $_GPC['desc'], 'erweima' => $_GPC['erweima'], 'href' => $_GPC['href'], 'content' => $_GPC['content'], 'rankshow' => $_GPC['rankshow'], 'ranknum' => $_GPC['ranknum']);
			load()->func('file');
			if (!empty($_FILES['apiclient_cert']['name'])) {
				$apiclient_cert = file_upload($_FILES['apiclient_cert'], 'audio');
				$cfg['apiclient_cert'] = $apiclient_cert['path'];
			} else {
				$cfg['apiclient_cert'] = $this->module['config']['apiclient_cert'];
			}
			if (!empty($_FILES['apiclient_key']['name'])) {
				$apiclient_key = file_upload($_FILES['apiclient_key'], 'audio');
				$cfg['apiclient_key'] = $apiclient_key['path'];
			} else {
				$cfg['apiclient_key'] = $this->module['config']['apiclient_key'];
			}
			if (!empty($_FILES['rootca']['name'])) {
				$rootca = file_upload($_FILES['rootca'], 'audio');
				$cfg['rootca'] = $rootca['path'];
			} else {
				$cfg['rootca'] = $this->module['config']['rootca'];
			}
			if ($this->saveSettings($cfg)) {
				message('保存参数成功', 'refresh');
			}
		}
		if (empty($settings['ip'])) {
			$settings['ip'] = $_SERVER['SERVER_ADDR'];
			$yz = '程序自动填充';
		}
		include $this->template('setting');
	}
}