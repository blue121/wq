<?php

//decode by  http://www.tule5.com/
define('SUPERMAN_MALL_MANUAL', 'http://www.kancloud.cn/supermanapp/mall/');
class SupermanUtil
{
	public static function money_format($number, $places = 2, $symbol = '&#165;', $thousand = ',', $decimal = '.')
	{
		return $symbol . number_format($number, $places, $decimal, $thousand);
	}
	public static function attachment_path()
	{
		global $_W;
		if (!defined('ATTACHMENT_ROOT')) {
			$path = IA_ROOT . '/attachment/';
			define('ATTACHMENT_ROOT', $path);
			return $path;
		}
		if (substr(ATTACHMENT_ROOT, -1, 1) != '/') {
			return ATTACHMENT_ROOT . '/';
		}
		return ATTACHMENT_ROOT;
	}
	public static function img_placeholder($returnsrc = true)
	{
		global $_W;
		$src = $_W['siteroot'] . "addons/superman_mall/template/mobile/images/placeholder.jpg";
		if ($returnsrc) {
			return $src;
		} else {
			return "<img src='{$src}'/>";
		}
	}
	public static function hide_mobile($mobile)
	{
		return preg_replace('/(\d{3})(\d{4})/', "$1****", $mobile);
	}
	public static function hide_nickname($nickname, $length = 1, $suffix = '**')
	{
		return cutstr($nickname, $length) . $suffix;
	}
	public static function hide_password($password, $suffix = '***')
	{
		$firstStr = mb_substr($password, 0, 1, 'utf-8');
		$lastStr = mb_substr($password, -1, 1, 'utf-8');
		return $firstStr . $suffix . $lastStr;
	}
	public static function random_float($min = 0, $max = 1)
	{
		return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}
	public static function fix_path($path)
	{
		global $_W;
		$path = strpos($path, 'http://') !== false || strpos($path, 'https://') !== false ? str_replace($_W['attachurl'], '', $path) : $path;
		$path = strpos($path, 'http://') !== false || strpos($path, 'https://') !== false ? str_replace($_W['siteroot'], '', $path) : $path;
		return $path;
	}
	public static function get_item_status_title($status)
	{
		switch ($status) {
			case 0:
				return '下架';
				break;
			case 1:
				return '上架';
				break;
			case 2:
				return '禁售';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_shop_status_title($status)
	{
		switch ($status) {
			case -1:
				return '审核失败';
				break;
			case 0:
				return '待审核';
				break;
			case 1:
				return '审核通过';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_shop_status_style($status)
	{
		switch ($status) {
			case -1:
				return 'label label-danger';
				break;
			case 0:
				return 'label label-default';
				break;
			case 1:
				return 'label label-success';
				break;
			default:
				return 'label label-default';
				break;
		}
	}
	public static function get_order_status_title($status, $dispatch_type = 1)
	{
		switch ($status) {
			case -3:
				return '已关闭';
				break;
			case -2:
				return '已删除';
				break;
			case -1:
				return '已取消';
				break;
			case 0:
				return '待支付';
				break;
			case 1:
				return $dispatch_type == 2 ? '待自提' : '待发货';
				break;
			case 2:
				return $dispatch_type == 2 ? '待自提' : '已发货';
				break;
			case 3:
				return '已收货';
				break;
			case 4:
				return '已完成';
				break;
			case 5:
				return '已退款';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_order_status_style($status)
	{
		switch ($status) {
			case -3:
			case -2:
			case -1:
			case 5:
				return 'label label-danger';
				break;
			case 0:
				return 'label label-default';
				break;
			case 1:
				return 'label label-info';
				break;
			case 2:
				return 'label label-primary';
				break;
			case 3:
				return 'label label-warning';
				break;
			case 4:
				return 'label label-success';
				break;
			default:
				return 'label label-default';
				break;
		}
	}
	public static function get_pay_type_title($pay_type)
	{
		switch ($pay_type) {
			case 1:
				return '余额支付';
				break;
			case 2:
				return '微信支付';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_comment_star($score)
	{
		$star = '';
		$score = $score > 0 && $score < 5 ? $score : 5;
		for ($i = 0; $i < $score; $i++) {
			$star .= '<span class="icon iconfont font7">&#xe630;</span>';
		}
		for ($i = 0; $i < 5 - $score; $i++) {
			$star .= '<span class="icon iconfont font7">&#xe631;</span>';
		}
		return $star;
	}
	public static function sort_displayorder_desc($m1, $m2)
	{
		if ($m1['displayorder'] == $m2['displayorder']) {
			return 0;
		}
		return $m1['displayorder'] < $m2['displayorder'] ? 1 : -1;
	}
	public static function sort_displayorder_asc($m1, $m2)
	{
		if ($m1['displayorder'] == $m2['displayorder']) {
			return 0;
		}
		return $m1['displayorder'] < $m2['displayorder'] ? -1 : 1;
	}
	public static function get_skey($name, $params = null)
	{
		if (strpos($name, ':%') !== false) {
			if (is_array($params)) {
				while ($params) {
					$name = sprintf($name, array_shift($params));
				}
			} else {
				$name = sprintf($name, $params);
			}
		}
		return $name;
	}
	public static function get_seckill_params()
	{
		$time_group = array(array('key' => 8, 'start' => strtotime('8:00'), 'end' => strtotime('11:59:59')), array('key' => 12, 'start' => strtotime('12:00'), 'end' => strtotime('15:59:59')), array('key' => 16, 'start' => strtotime('16:00'), 'end' => strtotime('19:59:59')), array('key' => 20, 'start' => strtotime('20:00'), 'end' => strtotime('23:59:59')));
		$p = $time_group[0];
		foreach ($time_group as $item) {
			if ($item['start'] < TIMESTAMP && TIMESTAMP < $item['end']) {
				$p = $item;
				break;
			}
		}
		return $p;
	}
	public static function qrcode_png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 10, $margin = 4, $saveandprint = false)
	{
		include_once IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
		QRcode::png($text, $outfile, $level, $size, $margin, $saveandprint);
	}
	public static function uid2openid($uid)
	{
		$fans = mc_fansinfo($uid);
		return $fans && $fans['openid'] ? $fans['openid'] : '';
	}
	public static function get_thumb_filename($filename, $thumb_suffix = '_thumb')
	{
		$arr = explode('.', $filename);
		return $arr[0] . $thumb_suffix . '.' . $arr[1];
	}
	public static function send_sms($account, $mobile, $message)
	{
		if (!is_array($account) || empty($account)) {
			return false;
		}
		if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
			return false;
		}
		if (!strexists($message, $account['signature'])) {
			$message .= $account['signature'];
		}
		$urls = parse_url($account['url']);
		$str = "action=send&userid=&account=%s&password=%s&mobile=%s&sendTime=&content=%s";
		$post_data = sprintf($str, $account['username'], $account['password'], $mobile, $message);
		$httpheader = "POST {$account['url']} HTTP/1.0\r\n";
		$httpheader .= "Host:{$urls['host']}\r\n";
		$httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
		$httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
		$httpheader .= "Connection:close\r\n\r\n";
		$httpheader .= $post_data;
		$fd = fsockopen($urls['host'], 80);
		fwrite($fd, $httpheader);
		$ret = '';
		while (!feof($fd)) {
			$ret .= fread($fd, 128);
		}
		fclose($fd);
		$start = strpos($ret, '<?xml');
		$data = substr($ret, $start);
		$xml = simplexml_load_string($data);
		if ($xml->returnstatus == 'Success') {
			WeUtility::logging('trace', "[send_sms] success, mobile={$mobile}, message={$message}");
			return true;
		} else {
			WeUtility::logging('fatal', "[send_sms] failed, mobile={$mobile}, message={$message}, result={$xml->message}");
			return $xml->message;
		}
	}
	public static function short_url($url)
	{
		load()->func('communication');
		$data = array('url' => $url);
		$result = ihttp_post('http://dwz.cn/create.php', $data);
		if (empty($result)) {
			WeUtility::logging('fatal', "[short_url] failed, result is NULL, data=" . var_export($data, true));
			return null;
		}
		$result = json_decode($result['content'], true);
		if ($result['status'] != 0) {
			WeUtility::logging('fatal', "[short_url] failed, status={$result['status']}, err_msg={$result['err_msg']}");
			return null;
		}
		return $result['tinyurl'];
	}
}