<?php

//decode by  http://www.tule5.com/
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
	public static function hide_ordersn($ordersn, $symbol = '***')
	{
		return substr($ordersn, 0, 8) . $symbol . substr($ordersn, -3, 3);
	}
	public static function hide_expressno($expressno, $symbol = '***')
	{
		return substr($expressno, 0, 4) . $symbol . substr($expressno, -3, 3);
	}
	public static function hide_idcard($idcard, $symbol = '**********')
	{
		return substr($idcard, 0, 4) . $symbol . substr($idcard, -4, 4);
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
			case 3:
				return '隐藏';
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
	public static function get_getcash_account_type_title($account_type)
	{
		switch ($account_type) {
			case 'wechat':
				return '微信';
				break;
			case 'bank':
				return '银行';
				break;
			case 'alipay':
				return '支付宝';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_getcash_status_title($status)
	{
		switch ($status) {
			case 0:
				return '未支付';
				break;
			case 1:
				return '已支付';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_getcash_status_style($status)
	{
		switch ($status) {
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
	public static function get_money_status_title($status)
	{
		switch ($status) {
			case 0:
				return '未结算';
				break;
			case 1:
				return '已结算';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_money_status_style($status)
	{
		switch ($status) {
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
			case -5:
				return '已退款';
				break;
			case -4:
				return '申请退款';
				break;
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
				return '已售后';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_order_status_style($status)
	{
		switch ($status) {
			case -5:
				return 'label label-warning';
				break;
			case -4:
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
			case 3:
				return '货到付款';
				break;
			default:
				return 'unknown';
				break;
		}
	}
	public static function get_partner_status_style($status)
	{
		switch ($status) {
			case -2:
				return 'label label-danger';
				break;
			case -1:
				return 'label label-warning';
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
	public static function get_partner_status_title($status)
	{
		switch ($status) {
			case -2:
				return '禁用';
				break;
			case -1:
				return '等待中';
				break;
			case 0:
				return '待审核';
				break;
			case 1:
				return '正常';
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
	public static function get_web_comment_star($score)
	{
		$star = '';
		$score = $score > 0 && $score < 5 ? $score : 5;
		for ($i = 0; $i < $score; $i++) {
			$star .= '<span class="fa fa-star" style="color: #f6383a"></span>';
		}
		for ($i = 0; $i < 5 - $score; $i++) {
			$star .= '<span class="fa fa-star-o" style="color: #f6383a"></span>';
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
	public static function get_seckill_time()
	{
		$hour = date('H');
		if ($hour >= 12 && $hour < 16) {
			$seckill_time = 12;
		} else {
			if ($hour >= 16 && $hour < 20) {
				$seckill_time = 16;
			} else {
				if ($hour >= 20 && $hour <= 23) {
					$seckill_time = 20;
				} else {
					$seckill_time = 8;
				}
			}
		}
		return $seckill_time;
	}
	public static function qrcode_png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 10, $margin = 4, $saveandprint = false)
	{
		include_once IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
		ob_clean();
		QRcode::png($text, $outfile, $level, $size, $margin, $saveandprint);
	}
	public static function uid2openid($uid)
	{
		$fans = mc_fansinfo($uid);
		return $fans && $fans['openid'] ? $fans['openid'] : '';
	}
	public static function get_thumb_filename($filename, $thumb_suffix = '_thumb')
	{
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$arr = explode('.' . $ext, $filename);
		$filename = $arr[0] . $thumb_suffix . '.' . $ext;
		return $filename;
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
	public static function get_file_lines($filename)
	{
		$lines = 0;
		$fp = @fopen($filename, 'r');
		if ($fp) {
			while (!feof($fp) && stream_get_line($fp, 8192, "\r\n") !== false) {
				$lines++;
			}
			@fclose($fp);
		}
		return $lines;
	}
	public static function get_sign($params = array(), $key = '')
	{
		ksort($params);
		$str = http_build_query($params);
		$str .= '&key=' . $key;
		return sha1($str);
	}
	public static function is_we7_encrypt($filename)
	{
		$content = file_get_contents($filename);
		return strstr($content, 'return;?>') !== false && substr($content, 5, 8) == ' define(' ? true : false;
	}
	public static function format_date($str)
	{
		return substr($str, 0, 4) . '-' . substr($str, 4, 2) . '-' . substr($str, 6, 2);
	}
	public static function get_partner_group_condition_title($type = -1)
	{
		$data = array('1' => '分销订单总数', '2' => '分销订单总金额', '3' => '下线分销商总人数', '4' => '一级分销商总人数', '5' => '二级分销商总人数', '6' => '三级分销商总人数');
		if (isset($data[$type])) {
			return $data[$type];
		}
		return $data;
	}
	public static function format_credit($credit)
	{
		return str_replace('.00', '', $credit);
	}
	public static function get_domain($siteurl, $level = 0)
	{
		$siteurl = preg_replace('/http[s]{0,1}:\/\//i', '', $siteurl, 1);
		$arr = explode(':', $siteurl);
		$siteurl = $arr[0];
		$arr = explode('/', $siteurl);
		$domain = $arr[0];
		if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $domain)) {
			return $domain;
		}
		$arr = explode('.', $domain);
		$tmp = array();
		if ($level <= 0) {
			$level = count($arr) - 1;
		}
		for ($i = 0, $j = count($arr) - 1; $i <= $level && $j >= 0; $i++, $j--) {
			array_push($tmp, $arr[$j]);
		}
		if ($tmp[count($tmp) - 1] == 'www') {
			unset($tmp[count($tmp) - 1]);
		}
		$tmp = array_reverse($tmp);
		$domain = implode('.', $tmp);
		return $domain;
	}
	public static function float_format($num, $len = 2)
	{
		$multiplier = pow(10, $len);
		$arr = explode('.', $num * $multiplier);
		$result = $arr[0] / $multiplier;
		return sprintf('%.' . $len . 'f', $result);
	}
	public static function short_url($url, $uniacid = 0, $acid = 0)
	{
		global $_W;
		$uniacid = !empty($uniacid) ? $uniacid : $_W['uniacid'];
		$acid = !empty($acid) ? $acid : $_W['acid'];
		if (defined('LOCAL_DEVELOPMENT')) {
			return $url;
		} else {
			if (in_array($_W['account']['level'], array(3, 4))) {
				if (empty($_W['account'])) {
					if ($uniacid) {
						$_W['account'] = uni_fetch($uniacid);
					} else {
						if ($acid) {
							$_W['account'] = account_fetch($acid);
						} else {
							WeUtility::logging('fatal', "[short_url] failed, not found uniacid & acid");
							return $url;
						}
					}
				}
				$account = WeAccount::create();
				if (is_null($account)) {
					WeUtility::logging('fatal', "[short_url] failed, not created");
					return $url;
				}
				if (is_error($account)) {
					WeUtility::logging('fatal', "[short_url] failed, url={$url}, account=" . var_export($account, true));
					return $url;
				}
				$ret = $account->long2short($url);
				if (is_error($ret)) {
					WeUtility::logging('fatal', "[short_url] failed, url={$url}, ret=" . var_export($ret, true));
					return $url;
				}
				return $ret['short_url'] ? $ret['short_url'] : $url;
			}
			return $url;
		}
	}
	public static function pagination($total, $pageIndex, $pageSize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => ''))
	{
		global $_W;
		$pdata = array('tcount' => 0, 'tpage' => 0, 'cindex' => 0, 'findex' => 0, 'pindex' => 0, 'nindex' => 0, 'lindex' => 0, 'options' => '');
		if ($context['ajaxcallback']) {
			$context['isajax'] = true;
		}
		$pdata['tcount'] = $total;
		$pdata['tpage'] = empty($pageSize) || $pageSize < 0 ? 1 : ceil($total / $pageSize);
		if ($pdata['tpage'] <= 1) {
			return '';
		}
		$cindex = $pageIndex;
		$cindex = min($cindex, $pdata['tpage']);
		$cindex = max($cindex, 1);
		$pdata['cindex'] = $cindex;
		$pdata['findex'] = 1;
		$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
		$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
		$pdata['lindex'] = $pdata['tpage'];
		if ($context['isajax']) {
			if (!$url) {
				$url = $_W['script_name'] . '?' . http_build_query($_GET);
			}
			$pdata['faa'] = 'href="javascript:;" page="' . $pdata['findex'] . '" ' . ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', this);return false;"' : '');
			$pdata['paa'] = 'href="javascript:;" page="' . $pdata['pindex'] . '" ' . ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', this);return false;"' : '');
			$pdata['naa'] = 'href="javascript:;" page="' . $pdata['nindex'] . '" ' . ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', this);return false;"' : '');
			$pdata['laa'] = 'href="javascript:;" page="' . $pdata['lindex'] . '" ' . ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', this);return false;"' : '');
		} else {
			if ($url) {
				$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
				$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
				$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
				$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
			} else {
				$_GET['page'] = $pdata['findex'];
				$pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
				$_GET['page'] = $pdata['pindex'];
				$pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
				$_GET['page'] = $pdata['nindex'];
				$pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
				$_GET['page'] = $pdata['lindex'];
				$pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			}
		}
		$html = '<div><ul class="pagination pagination-centered">';
		$html .= "<li><a {$pdata['faa']} data-name=\"head\" class=\"pager-nav\">首页</a></li>";
		$html .= "<li><a {$pdata['paa']} data-name=\"prev\" class=\"pager-nav\">&laquo;上一页</a></li>";
		if (!$context['before'] && $context['before'] != 0) {
			$context['before'] = 5;
		}
		if (!$context['after'] && $context['after'] != 0) {
			$context['after'] = 4;
		}
		if ($context['after'] != 0 && $context['before'] != 0) {
			$range = array();
			$range['start'] = max(1, $pdata['cindex'] - $context['before']);
			$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
			if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
				$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
				$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
			}
			for ($i = $range['start']; $i <= $range['end']; $i++) {
				if ($context['isajax']) {
					$aa = 'href="javascript:;" page="' . $i . '" ' . ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] . '(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', this);return false;"' : '');
				} else {
					if ($url) {
						$aa = 'href="?' . str_replace('*', $i, $url) . '"';
					} else {
						$_GET['page'] = $i;
						$aa = 'href="?' . http_build_query($_GET) . '"';
					}
				}
				$html .= $i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>';
			}
		}
		$html .= "<li><a {$pdata['naa']} data-name=\"next\" class=\"pager-nav\">下一页&raquo;</a></li>";
		$html .= "<li><a {$pdata['laa']} data-name=\"foot\" class=\"pager-nav\">尾页</a></li>";
		$html .= "<li class=\"total_pager\" data-tpage=\"{$pdata['tpage']}\"><a href='javascript:;'>页数&nbsp;<span class=\"current_pager\">{$pdata['cindex']}</span>/{$pdata['tpage']}</a></li>";
		$html .= '</ul></div>';
		return $html;
	}
	public static function hex2rgb($hex)
	{
		if ($hex == '') {
			$rgb = array('r' => '0', 'g' => '0', 'b' => '0');
		}
		$color = str_replace('#', '', $hex);
		if (strlen($color) > 3) {
			$rgb = array('r' => hexdec(substr($color, 0, 2)), 'g' => hexdec(substr($color, 2, 2)), 'b' => hexdec(substr($color, 4, 2)));
		} else {
			$color = $hex;
			$r = substr($color, 0, 1) . substr($color, 0, 1);
			$g = substr($color, 1, 1) . substr($color, 1, 1);
			$b = substr($color, 2, 1) . substr($color, 2, 1);
			$rgb = array('r' => hexdec($r), 'g' => hexdec($g), 'b' => hexdec($b));
		}
		return $rgb;
	}
	public static function get_avatar_localpath($imgpath)
	{
		global $_W;
		$ret = MODULE_ROOT . '/template/mobile/images/wechat.png';
		if ($imgpath == '') {
			return $ret;
		} else {
			if (strpos($imgpath, '://') !== false) {
				$path = 'data/' . $_W['uniacid'] . '/avatar';
				$filename = MODULE_ROOT . '/' . $path . '/' . md5($imgpath);
				$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
				if (empty($ext)) {
					$ext = 'jpg';
					$filename .= '.' . $ext;
				}
				if (file_exists($filename)) {
					return $filename;
				} else {
					mkdirs(MODULE_ROOT . '/' . $path);
					$content = file_get_contents($imgpath);
					if (empty($content)) {
						WeUtility::logging('fatal', '[get_avatar_localpath] failed, imgpath=' . $imgpath);
						return $ret;
					}
					file_put_contents($filename, $content);
					return $filename;
				}
			} else {
				if (strpost($imgpath, ATTACHMENT_ROOT) === false) {
					$ret = ATTACHMENT_ROOT . $imgpath;
				}
			}
		}
		return $ret;
	}
	public static function create_image($imgpath)
	{
		if (!file_exists($imgpath)) {
			WeUtility::logging('fatal', '[create_image] failed, image path no exist, imgpath=' . $imgpath);
			trigger_error('[create_image] failed, image path no exist, imgpath=' . $imgpath, E_USER_ERROR);
		}
		$ext = pathinfo($imgpath, PATHINFO_EXTENSION);
		$ext = strtolower($ext);
		$ext = $ext == 'jpg' ? 'jpeg' : $ext;
		$method = "imagecreatefrom{$ext}";
		if (!function_exists($method)) {
			WeUtility::logging('fatal', '[create_image] failed, function no exist, function=' . $method);
			trigger_error('[create_image] failed, function no exist, function=' . $method, E_USER_ERROR);
		}
		return $method($imgpath);
	}
	public static function save_image($img, $imgpath)
	{
		$ext = pathinfo($imgpath, PATHINFO_EXTENSION);
		$ext = strtolower($ext);
		$ext = $ext == 'jpg' ? 'jpeg' : $ext;
		$method = "image{$ext}";
		if (!function_exists($method)) {
			WeUtility::logging('fatal', '[save_image] failed, function no exist, function=' . $method);
			trigger_error('[save_image] failed, function no exist, function=' . $method, E_USER_ERROR);
		}
		return $method($img, $imgpath);
	}
	public static function get_partner_qrcode($invite_url, $create_img = true)
	{
		global $_W;
		$path = 'images/' . $_W['uniacid'] . '/' . date('Y/m/');
		mkdirs(ATTACHMENT_ROOT . '/' . $path);
		$qrcode_filename = md5('partner-qrcode-' . $invite_url) . '.png';
		$qrcode_path = $path . $qrcode_filename;
		if ($create_img && !file_exists(ATTACHMENT_ROOT . '/' . $path . $qrcode_filename)) {
			SupermanUtil::qrcode_png($invite_url, ATTACHMENT_ROOT . '/' . $path . $qrcode_filename);
		}
		return $qrcode_path;
	}
	public static function get_parter_poster_path($uid, $id)
	{
		global $_W;
		$path = 'data/' . $_W['uniacid'] . '/poster';
		mkdirs(MODULE_ROOT . '/' . $path);
		$poster_filename = md5('superman_mall:partner_poster:' . $uid) . '-' . $id . '.png';
		$poster_path = $path . '/' . $poster_filename;
		return $poster_path;
	}
	public static function get_bgimg_localpath($filepath)
	{
		global $_W;
		if (strexists($filepath, 'addons/')) {
			return IA_ROOT . '/' . $filepath;
		}
		if (file_exists(ATTACHMENT_ROOT . $filepath)) {
			return ATTACHMENT_ROOT . $filepath;
		}
		$url = tomedia($filepath);
		$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
		$path = 'data/' . $_W['uniacid'] . '/image';
		$filename = MODULE_ROOT . '/' . $path . '/' . md5($url) . '.' . $ext;
		if (empty($ext)) {
			$ext = 'jpg';
			$filename .= '.' . $ext;
		}
		if (file_exists($filename)) {
			return $filename;
		} else {
			mkdirs(MODULE_ROOT . '/' . $path);
			$content = file_get_contents($url);
			if (empty($content)) {
				WeUtility::logging('fatal', '[get_bgimg_localpath] failed, url=' . $url);
				return false;
			}
			file_put_contents($filename, $content);
			return $filename;
		}
	}
	public static function get_qrcode($url, $save_img = true)
	{
		global $_W;
		$path = 'data/' . $_W['uniacid'] . '/qrcode';
		mkdirs(MODULE_ROOT . '/' . $path);
		$filename = md5('superman_mall:qrcode:' . $url) . '.png';
		$qrcode_path = $path . '/' . $filename;
		if ($save_img && !file_exists(MODULE_ROOT . '/' . $qrcode_path)) {
			SupermanUtil::qrcode_png($url, MODULE_ROOT . '/' . $qrcode_path);
		}
		$ret = array('path' => $qrcode_path, 'abs_path' => MODULE_ROOT . '/' . $qrcode_path, 'url' => MODULE_URL . $qrcode_path, 'filename' => $filename);
		return $ret;
	}
	public static function gdVersion($user_ver = 0)
	{
		if (!extension_loaded('gd')) {
			return;
		}
		static $gd_ver = 0;
		if ($user_ver == 1) {
			$gd_ver = 1;
			return 1;
		}
		if ($user_ver != 2 && $gd_ver > 0) {
			return $gd_ver;
		}
		if (function_exists('gd_info')) {
			$ver_info = gd_info();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
			if ($user_ver == 2) {
				$gd_ver = 2;
				return 2;
			} else {
				$gd_ver = 1;
				return 1;
			}
		}
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];
		return $match[0];
	}
	public static function is_cart_url($url)
	{
		return strpos($url, 'do=cart') !== false && strpos($url, 'm=superman_mall') !== false ? true : false;
	}
}