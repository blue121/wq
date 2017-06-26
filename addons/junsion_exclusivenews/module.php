<?php

//decode by QQ:97391583 http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
define('OD_ROOT', IA_ROOT . '/web/junsionnews');
define('IMG', '../addons/junsion_exclusivenews/template/mobile/');
class Junsion_exclusivenewsModule extends WeModule
{
	public function settingsDisplay($settings)
	{
		global $_W, $_GPC;
		if (checksubmit()) {
			$btns = array();
			$btitles = $_GPC['btitle'];
			foreach ($btitles as $k => $value) {
				if (!empty($value)) {
					$btns[] = array('title' => $value, 'link' => $_GPC['blink'][$k]);
				}
			}
			$multi = array();
			foreach ($_GPC['multi_url'] as $k => $value) {
				if (!empty($value)) {
					$multi[] = array('multi_url' => $value, 'multi_rate' => $_GPC['multi_rate'][$k]);
				}
			}
			$dat = array('prices' => $_GPC['prices'], 'rule' => htmlspecialchars_decode($_GPC['rule']), 'ptips' => $_GPC['ptips'], 'copyright' => htmlspecialchars_decode(str_replace('&quot;', '&#039;', $_GPC['copyright']), ENT_QUOTES), 'describe' => $_GPC['describe'], 'describeurl' => $_GPC['describeurl'], 'checked' => $_GPC['checked'], 'w_play' => $_GPC['w_play'], 'w_news' => $_GPC['w_news'], 'w_done' => $_GPC['w_done'], 'w_send' => $_GPC['w_send'], 'w_with' => $_GPC['w_with'], 'w_acts' => $_GPC['w_acts'], 'w_write' => $_GPC['w_write'], 'w_create' => $_GPC['w_create'], 'w_more' => $_GPC['w_more'], 'yun_pid' => $_GPC['yun_pid'], 'yun_user' => $_GPC['yun_user'], 'yun_key' => $_GPC['yun_key'], 'isyunpay' => $_GPC['isyunpay'], 'with_pay' => $_GPC['with_pay'], 'w_best' => $_GPC['w_best'], 'w_newest' => $_GPC['w_newest'], 'w_my' => $_GPC['w_my'], 'logo' => $_GPC['logo'], 'ftips' => $_GPC['ftips'], 'gtips' => $_GPC['gtips'], 'ptitle' => $_GPC['ptitle'], 'pdesc' => $_GPC['pdesc'], 'pthumb' => $_GPC['pthumb'], 'ntitle' => $_GPC['ntitle'], 'ndesc' => $_GPC['ndesc'], 'mask' => $_GPC['mask'], 'nthumb' => $_GPC['nthumb'], 'access_key' => $_GPC['access_key'], 'secret_key' => $_GPC['secret_key'], 'bucket' => $_GPC['bucket'], 'qiniuUrl' => $_GPC['qiniuUrl'], 'isqiniu' => $_GPC['isqiniu'], 'iswith' => $_GPC['iswith'], 'min_limit' => $_GPC['min_limit'], 'max_limit' => $_GPC['max_limit'], 'max_day' => $_GPC['max_day'], 'min_check' => $_GPC['min_check'], 'pipeline' => $_GPC['pipeline'], 'ismulti' => $_GPC['ismulti'], 'MSG_READ' => $_GPC['MSG_READ'], 'multi' => $multi, 'moreBtn' => $btns);
			load()->func('file');
			mkdirs(OD_ROOT . '/');
			$r = true;
			if (!empty($_GPC['cert'])) {
				$ret = file_put_contents(OD_ROOT . "/" . md5("apiclient_{$_W['uniacid']}cert") . ".pem", trim($_GPC['cert']));
				$r = $r && $ret;
			}
			if (!empty($_GPC['key'])) {
				$ret = file_put_contents(OD_ROOT . "/" . md5("apiclient_{$_W['uniacid']}key") . ".pem", trim($_GPC['key']));
				$r = $r && $ret;
			}
			if (!empty($_GPC['ca'])) {
				$ret = file_put_contents(OD_ROOT . "/" . md5("root{$_W['uniacid']}ca") . ".pem", trim($_GPC['ca']));
				$r = $r && $ret;
			}
			if (!$r) {
				message('证书保存失败, 请保证 ' . OD_ROOT . ' 目录可写');
			}
			$dat['api'] = array('mchid' => $_GPC['mchid'], 'password' => $_GPC['password'], 'ip' => $_GPC['ip'], 'appid' => $_GPC['appid'], 'secret' => $_GPC['secret']);
			$s = array_elements(array('title', 'provider', 'wish', 'remark'), $_GPC);
			$dat['redpacket'] = $s;
			if ($this->saveSettings($dat)) {
				message('保存成功！', 'refresh');
			}
		}
		$config = $settings['api'];
		$red = $settings['redpacket'];
		if (empty($config['ip'])) {
			$config['ip'] = $_SERVER['SERVER_ADDR'];
		}
		include $this->template('setting');
	}
}