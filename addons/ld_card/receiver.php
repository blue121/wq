<?php
function send_qyfk($settings, $fromUser, $amount, $desc)
{
	$ret = array();
	$ret['code'] = 0;
	$ret['message'] = 'success';
	$ret['amount'] = $amount;
	$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
	$pars = array();
	$pars['mch_appid'] = $settings['appid'];
	$pars['mchid'] = $settings['mch_id'];
	$pars['nonce_str'] = random(32);
	$pars['partner_trade_no'] = random(10) . date('Ymd') . random(3);
	$pars['openid'] = $fromUser;
	$pars['check_name'] = 'NO_CHECK';
	$pars['amount'] = $amount;
	$pars['desc'] = $desc;
	$pars['spbill_create_ip'] = $settings['ip'];
	ksort($pars, SORT_STRING);
	$string1 = '';
	foreach ($pars as $k => $v) {
		$string1 .= $k . '=' . $v . '&';
	}
	$string1 .= 'key=' . $settings['appkey'];
	$pars['sign'] = strtoupper(md5($string1));
	$xml = array2xml($pars);
	$extras = array();
	$extras['CURLOPT_CAINFO'] = $settings['rootca'];
	$extras['CURLOPT_SSLCERT'] = $settings['apiclient_cert'];
	$extras['CURLOPT_SSLKEY'] = $settings['apiclient_key'];
	load()->func('communication');
	$procResult = NULL;
	$resp = ihttp_request($url, $xml, $extras);
	if (is_error($resp)) {
		$procResult = $resp['message'];
		$ret['code'] = -1;
		$ret['message'] = '-1:' . $procResult;
		return $ret;
	}
	$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
	$dom = new DOMDocument();
	if ($dom->loadXML($xml)) {
		$xpath = new DOMXPath($dom);
		$code = $xpath->evaluate('string(//xml/return_code)');
		$result = $xpath->evaluate('string(//xml/result_code)');
		if (strtolower($code) == 'success' && strtolower($result) == 'success') {
			$ret['code'] = 0;
			$ret['message'] = 'success';
			return $ret;
		}
		$error = $xpath->evaluate('string(//xml/err_code_des)');
		$ret['code'] = -2;
		$ret['message'] = '-2:' . $error;
		return $ret;
	}
	$ret['code'] = -3;
	$ret['message'] = 'error response';
	return $ret;
}
function send_xjhb($settings, $fromUser, $amount, $desc)
{
	$ret = array();
	$ret['code'] = 0;
	$ret['message'] = 'success';
	$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
	$pars = array();
	$pars['mch_id'] = $settings['mch_id'];
	$pars['wxappid'] = $settings['appid'];
	$pars['nonce_str'] = random(32);
	$pars['mch_billno'] = random(10) . date('Ymd') . random(3);
	$pars['nick_name'] = $settings['send_name'];
	$pars['send_name'] = $settings['send_name'];
	$pars['re_openid'] = $fromUser;
	$pars['total_amount'] = $amount;
	$pars['min_value'] = $amount;
	$pars['max_value'] = $amount;
	$pars['total_num'] = 1;
	$pars['wishing'] = $desc;
	$pars['client_ip'] = $settings['ip'];
	$pars['act_name'] = $settings['act_name'];
	$pars['remark'] = $settings['remark'];
	ksort($pars, SORT_STRING);
	$string1 = '';
	foreach ($pars as $k => $v) {
		$string1 .= $k . '=' . $v . '&';
	}
	$string1 .= 'key=' . $settings['appkey'];
	$pars['sign'] = strtoupper(md5($string1));
	$xml = array2xml($pars);
	$extras = array();
	$extras['CURLOPT_CAINFO'] = $settings['rootca'];
	$extras['CURLOPT_SSLCERT'] = $settings['apiclient_cert'];
	$extras['CURLOPT_SSLKEY'] = $settings['apiclient_key'];
	load()->func('communication');
	$procResult = NULL;
	$resp = ihttp_request($url, $xml, $extras);
	if (is_error($resp)) {
		$procResult = $resp['message'];
		$ret['code'] = -1;
		$ret['message'] = $procResult;
		return $ret;
	}
	$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
	$dom = new DOMDocument();
	if ($dom->loadXML($xml)) {
		$xpath = new DOMXPath($dom);
		$code = $xpath->evaluate('string(//xml/return_code)');
		$result = $xpath->evaluate('string(//xml/result_code)');
		if (strtolower($code) == 'success' && strtolower($result) == 'success') {
			$ret['code'] = 0;
			$ret['message'] = 'success';
			return $ret;
		}
		$error = $xpath->evaluate('string(//xml/err_code_des)');
		$ret['code'] = -2;
		$ret['message'] = $error;
		return $ret;
	}
	$ret['code'] = -3;
	$ret['message'] = '3error3';
	return $ret;
}
function getsign($pars, $appkey)
{
	ksort($pars, SORT_STRING);
	$string1 = '';
	foreach ($pars as $k => $v) {
		$string1 .= $k . '=' . $v . '&';
	}
	$string1 .= 'key=' . $appkey;
	return strtoupper(md5($string1));
}
defined('IN_IA') || die('Access Denied');
define('CARD_ROOT', IA_ROOT . '/addons/ld_card');
class Ld_cardModuleReceiver extends WeModuleReceiver
{
	public function receive()
	{
		global $_W;
		global $_GPC;
		$type = $this->message['type'];
		if ($this->message['event'] == 'user_get_card') {
			$card_id = $this->message['cardid'];
			$res = pdo_fetch('select * from ' . tablename('ld_card_cards') . ' where card_id=:card_id', array(':card_id' => $card_id));
			if (!empty($res)) {
				$userid = pdo_fetchcolumn('select userid from ' . tablename('ld_card_cards') . ' where card_id=:card_id', array(':card_id' => $card_id));
				$data = array('weid' => $_W['uniacid'], 'userid' => $userid, 'card_id' => $card_id, 'cardcode' => $this->message['usercardcode'], 'card_user' => $this->message['fromusername'], 'friend' => $this->message['friendusername'], 'isfriend' => $this->message['isgivebyfriend'], 'time' => $this->message['time'], 'status' => 'get');
				$result = pdo_insert('ld_card_log', $data);
			}
		} else {
			if ($this->message['event'] == 'user_consume_card') {
				$card_id = $this->message['cardid'];
				$res = pdo_fetch('select * from ' . tablename('ld_card_cards') . ' where card_id=:card_id', array(':card_id' => $card_id));
				if (!empty($res)) {
					$cards = pdo_fetch('select * from ' . tablename('ld_card_cards') . ' where card_id=:card_id', array(':card_id' => $card_id));
					$data = array('weid' => $_W['uniacid'], 'userid' => $cards['userid'], 'card_id' => $card_id, 'cardcode' => $this->message['usercardcode'], 'card_user' => $this->message['fromusername'], 'time' => $this->message['time'], 'status' => 'consume');
					$result = pdo_insert('ld_card_log', $data);
					$friendopid = pdo_fetchcolumn('select friend from ' . tablename('ld_card_log') . ' where cardcode=:cardcode', array(':cardcode' => $this->message['usercardcode']));
					if (!empty($friendopid)) {
						if ($cards['sendnum'] < $cards['hbnum']) {
							$amount = rand($cards['minhb'], $cards['maxhb']);
							$signtext = !empty($cards['sign']) ? $cards['sign'] : '您分享的卡券已被使用，特此奖励！';
							$settings = $this->module['config'];
							$settings['apiclient_cert'] = ATTACHMENT_ROOT . '/' . $settings['apiclient_cert'];
							$settings['apiclient_key'] = ATTACHMENT_ROOT . '/' . $settings['apiclient_key'];
							$settings['rootca'] = ATTACHMENT_ROOT . '/' . $settings['rootca'];
							if ($settings['sendtype'] == 0) {
								$jg = send_qyfk($settings, $friendopid, $amount, $signtext);
							}
							if ($settings['sendtype'] == 1) {
								$jg = send_xjhb($settings, $friendopid, $amount, $signtext);
							}
							WeUtility::logging('ld_card_sendhb', var_export($jg, true));
							if ($jg['message'] == 'success' && $jg['code'] == '0') {
								pdo_update('ld_card_cards', array('sendhb' => $amount, 'sendnum' => $cards['sendnum'] + 1), array('card_id' => $card_id));
								pdo_update('ld_card_log', array('sendhb' => $amount, 'hbopenid' => $friendopid), array('cardcode' => $this->message['usercardcode'], 'status' => 'consume'), $glue = 'AND');
							}
						}
					}
				}
			}
		}
		$c = '';
		foreach ($this->message as $key => $value) {
			$c .= $key . ' : ' . $value . " \r\n";
		}
		file_put_contents(CARD_ROOT . '/receive.txt', $c . $_W['uniacid']);
	}
}