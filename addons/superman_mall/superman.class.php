<?php

//decode by  http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
interface SuermanInterface
{
	public function coreMenus();
	public function businessMenus();
	public function customMenus();
	public function payResult($params);
}
abstract class Superman extends WeModuleSite implements SuermanInterface
{
	protected $share = array();
	protected $web = array('trade_status' => 0, 'user_permission' => array(), 'account_role' => '');
	protected $plugin_setting = array();
	protected $mgroupon_log = true;
	protected $payresult_log = true;
	protected $footer_nav = array();
	protected $do, $act;
	protected $cart_count = 0;
	protected $shop = array();
	protected $shop_user = array();
	protected $shop_navs = array();
	protected $shop_active_nav = array();
	protected $shop_active_menu = array();
	protected $shop_page_title = '';
	protected $web_template_name = '';
	protected $web_path = '';
	protected $mobile_template_name = '';
	protected $mobile_path = '';
	public function __construct()
	{
		global $_W, $_GPC;
		$this->uniacid = $this->weid = $_W['uniacid'];
		$this->modulename = SUPERMAN_MODULE_NAME;
		$this->module = module_fetch(SUPERMAN_MODULE_NAME);
		$this->__define = MODULE_ROOT . '/site.php';
		$this->inMobile = defined('IN_MOBILE');
		$this->do = trim($_GPC['do']) ? trim($_GPC['do']) : 'dashboard';
		$this->act = trim($_GPC['act']) ? trim($_GPC['act']) : 'display';
		$this->plugin_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PLUGIN);
	}
	public function init()
	{
		global $_W, $_GPC;
		$this->check_uniacid();
		SupermanMallData::initHomeApps();
		SupermanMallData::initFooterNav();
		SupermanMallData::initSiteRoot();
		$this->mobile_template_name = 'default';
		$this->mobile_path = $_W[siteroot] . 'addons/superman_mall/template/mobile/' . $this->mobile_template_name;
		$this->superman_placeholder = $this->mobile_path . '/images/placeholder.gif';
		if (defined('IN_SUPERMAN_MALL_ADMIN')) {
			$shop_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
			$this->web_template_name = isset($shop_setting['shop_admin_template']) ? $shop_setting['shop_admin_template'] : 'wechat';
			$this->web_path = MODULE_URL . 'template/web/shop_admin/' . $this->web_template_name;
			$css = $this->do . '/' . $this->act . '.css';
			if (file_exists(MODULE_ROOT . '/template/web/shop_admin/' . $this->web_template_name . '/css/app/' . $css)) {
				$this->superman_css = '<link rel="stylesheet" href="' . MODULE_URL . 'template/web/shop_admin/' . $this->web_template_name . '/css/app/' . $css . '?' . $this->module['version'] . '">';
			}
			$js = $this->do . '/' . $this->act . '.js';
			if (file_exists(MODULE_ROOT . '/template/web/shop_admin/' . $this->web_template_name . '/js/app/' . $js)) {
				$this->superman_js = '<script src="' . MODULE_URL . 'template/web/shop_admin/' . $this->web_template_name . '/js/app/' . $js . '?' . $this->module['version'] . '" charset="utf-8"></script>';
			}
		} else {
			$this->web_template_name = 'default';
			$this->web_path = MODULE_URL . 'template/web/' . $this->web_template_name;
		}
		if ($this->inMobile) {
			if (SUPERMAN_CONFIG_SCROLL_MEMORY || defined('LOCAL_DEVELOPMENT') || defined('ONLINE_DEVELOPMENT')) {
				define('SUPERMAN_EXTERNAL', '');
			} else {
				define('SUPERMAN_EXTERNAL', 'external');
			}
			if (defined('LOCAL_DEVELOPMENT') && !SupermanUtil::is_we7_encrypt(MODULE_ROOT . '/site.php')) {
				$this->superman_css = '<link rel="stylesheet" href="' . MODULE_URL . '/min/index.php?g=css&debug=1&' . $this->module['version'] . '">';
				$this->superman_admin_css = '<link rel="stylesheet" href="' . MODULE_URL . '/min/index.php?g=admin-css&debug=1&' . $this->module['version'] . '">';
				$this->superman_global_js = '<script src="' . MODULE_URL . '/min/index.php?g=global-js&debug=1&' . $this->module['version'] . '" charset="utf-8"></script>';
				$this->superman_main_js = '<script src="' . MODULE_URL . '/min/index.php?g=main-js&debug=1&' . $this->module['version'] . '" charset="utf-8"></script>';
				$this->superman_admin_js = '<script src="' . MODULE_URL . '/min/index.php?g=admin-js&debug=1&' . $this->module['version'] . '" charset="utf-8"></script>';
			} else {
				if (file_exists(MODULE_ROOT . '/template/mobile/cache/css.css') && !defined('ONLINE_DEVELOPMENT')) {
					$this->superman_css = '<link rel="stylesheet" href="' . MODULE_URL . '/template/mobile/cache/css.css?' . $this->module['version'] . '">';
					$this->superman_admin_css = '<link rel="stylesheet" href="' . MODULE_URL . '/template/mobile/cache/admin.css?' . $this->module['version'] . '">';
					$this->superman_global_js = '<script src="' . MODULE_URL . '/template/mobile/cache/global.js?' . $this->module['version'] . '" charset="utf-8"></script>';
					$this->superman_main_js = '<script src="' . MODULE_URL . '/template/mobile/cache/main.js?' . $this->module['version'] . '" charset="utf-8"></script>';
					$this->superman_admin_js = '<script src="' . MODULE_URL . '/template/mobile/cache/admin.js?' . $this->module['version'] . '" charset="utf-8"></script>';
				} else {
					$this->superman_css = '<link rel="stylesheet" href="' . MODULE_URL . '/min/index.php?g=css&' . $this->module['version'] . '">';
					$this->superman_admin_css = '<link rel="stylesheet" href="' . MODULE_URL . '/min/index.php?g=admin-css&' . $this->module['version'] . '">';
					$this->superman_global_js = '<script src="' . MODULE_URL . '/min/index.php?g=global-js&' . $this->module['version'] . '" charset="utf-8"></script>';
					$this->superman_main_js = '<script src="' . MODULE_URL . '/min/index.php?g=main-js&' . $this->module['version'] . '" charset="utf-8"></script>';
					$this->superman_admin_js = '<script src="' . MODULE_URL . '/min/index.php?g=admin-js&' . $this->module['version'] . '" charset="utf-8"></script>';
				}
			}
			if ($this->_check_wechat()) {
				if ($this->module['config']['base']['wechat'] && $_W['container'] != 'wechat') {
					$this->message('请在微信中打开！', '', 'info');
				}
			}
			$this->_check_debug();
			$this->_init_app();
		} else {
			SupermanMallData::initExpressCompany();
			$this->_init_web();
		}
		$this->_init_trade_status();
		$this->_background_running();
	}
	public function check_uniacid()
	{
		global $_W;
		if (empty($_W['uniacid'])) {
			message('这项功能需要你选择特定公众号才能使用！', wurl('account/display'), 'info');
		}
	}
	public function order_print($print_type, $orderid)
	{
		global $_W;
		$order = M::t('superman_mall_order')->fetch($orderid);
		if (empty($order)) {
			WeUtility::logging('warning', '[order_print] not found order, orderid=' . $orderid);
			return;
		}
		if (!isset($this->plugin_setting['printer']) || $this->plugin_setting['printer'] == 0) {
			WeUtility::logging('warning', '[order_print] plugin printer not open');
			return;
		}
		if ($order['shopid'] == 0) {
			$order_list = M::t('superman_mall_order')->fetchall(array('pid' => $orderid), '', 0, -1);
			foreach ($order_list as $li) {
				$print_permission = $this->check_plugin_permission('printer', $li['shopid']);
				if (!is_error($print_permission)) {
					$this->order_print($print_type, $li['orderid']);
				}
			}
			return;
		} else {
			$print_permission = $this->check_plugin_permission('printer', $order['shopid']);
			if (is_error($print_permission)) {
				WeUtility::logging('warning', '[order_print] shop plugin printer not open, shopid=' . $order['shopid']);
				return;
			}
		}
		$order['items'] = M::t('superman_mall_order_item')->fetchall(array('orderid' => $orderid), '', 0, -1);
		$printer_list = M::t('superman_mall_shop_printer')->fetchall(array('shopid' => $order['shopid'], 'print_type' => $print_type, 'status' => 1), '', 0, -1);
		if (empty($printer_list)) {
			WeUtility::logging('warning', '[order_print] not found printer, shopid=' . $order['shopid'] . ', print_type=' . $print_type);
			return;
		}
		foreach ($printer_list as $printer) {
			$ret = Printer::init($printer)->order_print($order);
			if ($ret !== false && $ret['errno'] == 0) {
				$data = array('uniacid' => $order['uniacid'], 'shopid' => $order['shopid'], 'searchid' => $ret['searchid'], 'printersn' => $printer['sn'], 'ordersn' => $order['ordersn'], 'status' => 0, 'dateline' => TIMESTAMP);
				$new_id = M::t('superman_mall_order_print')->insert($data);
				if (!$new_id) {
					WeUtility::logging('fatal', '[order_print] insert superman_mall_order_print failed, data=' . var_export($data, true));
				}
			}
		}
	}
	public function coreMenus()
	{
		global $_W, $_GPC;
	}
	public function businessMenus()
	{
		global $_W, $_GPC;
		return SuermanMallMenu::businessMenus();
	}
	public function customMenus()
	{
		global $_W, $_GPC;
		$menus = SuermanMallMenu::customMenus();
		if ($menus) {
			$this->_init_web_user_permission();
			foreach ($menus as $k => $m) {
				$name = 'superman_mall_menu_' . $m['do'];
				if (!$this->check_user_permission($name, false)) {
					unset($menus[$k]);
				}
			}
		}
		return $menus;
	}
	public function payResult($params)
	{
		global $_W, $_GPC;
		if (strpos($_W['siteroot'], '/addons/bm_payu') !== false) {
			$_W['siteroot'] = str_replace('/addons/bm_payu', '', $_W['siteroot']);
		}
		if ($this->payresult_log) {
			WeUtility::logging('trace', '[payResult:' . $params['from'] . '] params=' . var_export($params, true) . ', siteurl=' . $_W['siteurl'] . ', siteroot=' . $_W['siteroot']);
		}
		$orderid = $params['tid'];
		$order = M::t('superman_mall_order')->fetch($orderid);
		if (!$order) {
			$this->json(ERRNO::ORDER_NOT_EXIST);
		}
		if (!$_W['uniacid']) {
			$_W['uniacid'] = $order['uniacid'];
		}
		$paylog = M::t('core_paylog')->fetch(array('uniacid' => $params['uniacid'], 'openid' => $params['user'], 'tid' => $params['tid'], 'uniontid' => $params['uniontid']));
		if ($order['pay_type'] != 1) {
			$paylog['card_fee'] += $order['pay_credit'];
		}
		if (!$paylog || $paylog['card_fee'] != $order['price'] || $paylog['status'] != 1) {
			$this->json(ERRNO::INVALID_REQUEST);
		}
		$account = $this->_init_account();
		if ($order['type'] == 1) {
			$mgroupon = M::t('superman_mall_merge_groupon')->fetch(array('orderid' => $order['id']));
			$mgroupon_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('mgroupon', array('act' => 'invite', 'id' => $mgroupon['id'], 'shopid' => $order['shopid']));
		}
		$order_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('order', array('status' => 'no_receive'));
		if ($params['result'] == 'success' && $params['from'] == 'return' && $params['type'] == 'wechat') {
			$url = $order_url;
			if ($order['type'] == 1 && isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) {
				isetcookie('__mgroupon_id', 0, -1);
				$url = $mgroupon_url;
			}
			if ($this->payresult_log) {
				WeUtility::logging('trace', '[payResult:' . $params['from'] . '] url=' . $url);
			}
			if ($order['status'] == 0) {
				$msg = '正在支付中，请稍后刷新订单状态';
			} else {
				$msg = '支付成功，跳转中...';
			}
			$this->json(ERRNO::OK, $msg, array('url' => $url), true);
			die;
		}
		if ($params['result'] == 'success' && ($params['from'] == 'notify' || $params['from'] == 'return' && ($params['type'] == 'credit' || $params['type'] == 'cash')) && $order['status'] == 0) {
			if ($this->payresult_log) {
				WeUtility::logging('trace', '[payResult:' . $params['from'] . '] order=' . var_export($order, true));
			}
			$order_data = array('status' => 1, 'updatetime' => TIMESTAMP);
			$paytype = array('credit' => '1', 'wechat' => '2', 'cash' => '3');
			$order_data['pay_type'] = $paytype[$params['type']];
			if ($params['type'] == 'wechat') {
				$order_data['payment_no'] = $params['tag']['transaction_id'];
			}
			$order_data['pay_time'] = TIMESTAMP;
			if ($order['shopid'] == 0) {
				$ret = M::t('superman_mall_order')->update($order_data, array('pid' => $orderid));
				if ($ret === false) {
					WeUtility::logging('fatal', '[payResult:' . $params['from'] . '] 父订单更新子订单状态更新失败, pid=' . $orderid . ', data=' . var_export($order_data, true));
					$this->json(ERRNO::SYSTEM_ERROR, '父订单状态更新失败，请联系管理员');
				}
			}
			$ret = M::t('superman_mall_order')->update($order_data, array('id' => $orderid));
			if ($ret === false) {
				WeUtility::logging('fatal', '[payResult:' . $params['from'] . '] 订单状态更新失败, id=' . $orderid . ', data=' . var_export($order_data, true));
				$this->json(ERRNO::SYSTEM_ERROR, '订单状态更新失败，请联系管理员');
			}
			if ($order['shopid'] == 0) {
				$child_order = M::t('superman_mall_order')->fetchall(array('pid' => $orderid));
				if ($child_order) {
					foreach ($child_order as $child) {
						M::t('superman_mall_shop')->increment(array('order_payed' => 1), array('id' => $child['shopid']));
						$order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $child['id']));
						if ($order_items) {
							foreach ($order_items as $order_item) {
								$item = M::t('superman_mall_item')->fetch($order_item['itemid']);
								if ($item) {
									if ($item['minus_total'] == 1) {
										$this->update_item_total($item, $order_item);
									}
									$this->update_item_sales($item, $order_item);
								}
							}
						}
					}
				}
			} else {
				M::t('superman_mall_shop')->increment(array('order_payed' => 1), array('id' => $order['shopid']));
				$order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $orderid));
				if ($order_items) {
					foreach ($order_items as $order_item) {
						$item = M::t('superman_mall_item')->fetch($order_item['itemid']);
						if ($item) {
							if ($item['minus_total'] == 1) {
								$this->update_item_total($item, $order_item);
							}
							$this->update_item_sales($item, $order_item);
						}
					}
				}
			}
			if ($order['shopid'] == 0) {
				if ($child_order) {
					foreach ($child_order as $child) {
						$order_detail_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('order', array('act' => 'detail', 'orderid' => $child['id']));
						$this->send_order_tmplmsg('pay', $child, SupermanUtil::uid2openid($child['uid']), $order_detail_url);
					}
				}
			} else {
				$order_detail_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('order', array('act' => 'detail', 'orderid' => $orderid));
				$this->send_order_tmplmsg('pay', $order, SupermanUtil::uid2openid($order['uid']), $order_detail_url);
			}
			if ($order['shopid'] == 0) {
				if (isset($child_order) && $child_order) {
					foreach ($child_order as $child) {
						$extra_info = "\n\n==订单详情==\n";
						$extra_info .= "订单号：{$child['ordersn']}\n";
						$extra_info .= "金额：￥{$child['price']}\n";
						$item_info = '';
						$order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $child['id']));
						foreach ($order_items as $item) {
							if ($item_info != '') {
								$item_info .= '、';
							}
							$item_info .= "{$item['title']}(x{$item['total']})";
						}
						$extra_info .= "商品：{$item_info}\n";
						if ($child['username']) {
							$extra_info .= "收货人：{$child['username']} {$child['mobile']} {$child['address']}";
						}
						$param = array('action' => 'order_pay', 'shopid' => $child['shopid'], 'extra_info' => $extra_info, 'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $child['id'])));
						Trigger::init('shop')->send($param);
					}
					$extra_info = "\n\n==订单详情(" . count($child_order) . ")==\n";
					$shop_info = '';
					foreach ($child_order as $li) {
						if ($shop_info != '') {
							$shop_info .= "\n--\n";
						}
						$shop = M::t('superman_mall_shop')->fetch($li['shopid']);
						$shop_info .= "商户：{$shop['title']}\n";
						$shop_info .= "订单号：{$li['ordersn']}\n";
					}
					$extra_info .= $shop_info;
					$params = array('action' => 'order_submit', 'uniacid' => $_W['uniacid'], 'extra_info' => $extra_info, 'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id'])));
					Trigger::init('platform')->send($params);
				}
			} else {
				$extra_info = "\n\n==订单详情==\n";
				$extra_info .= "订单号：{$order['ordersn']}\n";
				$extra_info .= "金额：￥{$order['price']}\n";
				$item_info = '';
				$order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $order['id']));
				foreach ($order_items as $item) {
					if ($item_info != '') {
						$item_info .= '、';
					}
					$item_info .= "{$item['title']}(x{$item['total']})";
				}
				$extra_info .= "商品：{$item_info}\n";
				if ($order['username']) {
					$extra_info .= "收货人：{$order['username']} {$order['mobile']} {$order['address']}";
				}
				$param = array('action' => 'order_pay', 'shopid' => $order['shopid'], 'extra_info' => $extra_info, 'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id'])));
				Trigger::init('shop')->send($param);
				$extra_info = "\n\n==订单详情==\n";
				$shop = M::t('superman_mall_shop')->fetch($order['shopid']);
				$extra_info .= "商户：{$shop['title']}\n";
				$extra_info .= "订单号：{$order['ordersn']}\n";
				$param = array('action' => 'order_pay', 'uniacid' => $_W['uniacid'], 'extra_info' => $extra_info, 'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id'])));
				Trigger::init('platform')->send($param);
			}
			if ($order['type'] == 1) {
				if (isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) {
					if ($mgroupon) {
						if ($this->payresult_log) {
							WeUtility::logging('trace', '[payResult:' . $params['from'] . '] mgroupon=' . var_export($mgroupon, true));
						}
						isetcookie('__mgroupon_id', 0, -1);
						M::t('superman_mall_merge_groupon')->update(array('pay_status' => 1), array('id' => $mgroupon['id']));
						if ($mgroupon['mgid'] == 0) {
							$order['mgroupon_id'] = $mgroupon['id'];
							$order['remain_total'] = $mgroupon['limit'] - 1;
							$this->send_mgroupon_svcmsg('start', $order, SupermanUtil::uid2openid($order['uid']));
						} else {
							$sponsor = M::t('superman_mall_merge_groupon')->fetch(array('id' => $mgroupon['mgid']));
							if ($sponsor) {
								$children = M::t('superman_mall_merge_groupon')->fetchall(array('mgid' => $sponsor['id'], 'pay_status' => 1), '', 0, -1);
								$order['remain_total'] = $sponsor['limit'] - count($children) - 1;
								if ($order['remain_total'] < 0) {
									$order['remain_total'] = 0;
								}
								if ($order['remain_total'] == 0) {
									M::t('superman_mall_merge_groupon')->update(array('status' => 1), array('id' => $mgroupon['mgid']));
								}
								$order['mgroupon_id'] = $mgroupon['id'];
								$this->send_mgroupon_svcmsg('join', $order, SupermanUtil::uid2openid($order['uid']));
								$order['mgroupon_id'] = $sponsor['id'];
								$friend = mc_fetch($order['uid'], array('nickname'));
								$order['friend_nickname'] = $friend && $friend['nickname'] ? $friend['nickname'] : 'uid=' . $order['uid'];
								$this->send_mgroupon_svcmsg('notice', $order, SupermanUtil::uid2openid($sponsor['uid']));
								if ($order['remain_total'] == 0) {
									$this->send_mgroupon_svcmsg('success', $order, SupermanUtil::uid2openid($sponsor['uid']));
									if ($children) {
										foreach ($children as $c) {
											$child_order = M::t('superman_mall_order')->fetch(array('uid' => $c['uid'], 'id' => $c['orderid']));
											if ($child_order) {
												$this->send_mgroupon_svcmsg('success', $child_order, SupermanUtil::uid2openid($c['uid']));
											}
										}
									}
								}
							}
						}
					}
				}
			}
			$this->order_print(SUPERMAN_PAYED_ORDER_PRINT, $order['id']);
			if ($params['result'] == 'success' && $params['from'] == 'return' && ($params['type'] == 'credit' || $params['type'] == 'cash')) {
				$url = $order_url;
				if ($order['type'] == 1 && isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) {
					isetcookie('__mgroupon_id', 0, -1);
					$url = $mgroupon_url;
				}
				if ($this->payresult_log) {
					WeUtility::logging('trace', '[payResult:' . $params['from'] . '] url=' . $url);
				}
				if ($order['status'] == 0 && $order['pay_type'] != 3) {
					$msg = '正在支付中，请稍后刷新订单状态';
				} else {
					$msg = '支付成功，跳转中...';
				}
				$this->json(ERRNO::OK, $msg, array('redirect_url' => $url));
				die;
			}
		}
	}
	public function json($errno, $errmsg = '', $data = array(), $redirect = false)
	{
		global $_W, $_GPC;
		ob_clean();
		if ($errmsg == '') {
			$errmsg = ERRNO::$ERRMSG[$errno];
		}
		$result = array('errno' => $errno, 'errmsg' => $errmsg, 'data' => $data);
		if ($redirect && $result['data']['url']) {
			@header("Location: {$result['data']['url']}#wechat_redirect");
			die;
		}
		if ($_W['isajax']) {
			@header('Content-Type: application/json; charset=utf-8');
			echo json_encode($result);
		} else {
			$type = 'info';
			if ($errno == 0) {
				$type = 'success';
			}
			$this->message($errmsg, '', $type);
		}
		die;
	}
	public function message($msg, $redirect = '', $type = '')
	{
		global $_W, $_GPC;
		if ($redirect == 'refresh') {
			$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
		}
		if ($redirect == 'referer') {
			$redirect = referer();
		}
		$type = in_array($type, array('success', 'warn', 'info', 'waiting', 'safe_success', 'safe_warn')) ? $type : 'info';
		if (empty($msg) && !empty($redirect) && $redirect != 'close') {
			@header('Location: ' . $redirect);
		}
		include $this->template('common/message', TEMPLATE_INCLUDEPATH);
		die;
	}
	public function get_credit_titles()
	{
		global $_W, $_GPC;
		$credit_title = array();
		$uni_settings = uni_setting($_W['uniacid']);
		if ($uni_settings && $uni_settings['creditnames']) {
			$creditnames = iunserializer($uni_settings['creditnames']);
			if ($creditnames) {
				foreach ($creditnames as $k => $val) {
					if ($val['enabled']) {
						$credit_title[$k] = $val;
					}
				}
			}
		}
		return $credit_title;
	}
	public function send_mgroupon_svcmsg($type, $order, $openid, $url = '')
	{
		global $_W;
		if (!$_W['uniacid']) {
			$_W['uniacid'] = $order['uniacid'];
		}
		$account = $this->_init_account();
		if (is_error($account)) {
			WeUtility::logging('fatal', '[send_mgroupon_svcmsg] 发送客服消息失败 _init_account failed: account=' . var_export($account, true));
			return $account;
		}
		if (!in_array($_W['account']['level'], array(3, 4))) {
			WeUtility::logging('fatal', '[send_mgroupon_svcmsg] 非认证公众号没有客服消息权限, name=' . $_W['account']['name'] . ', level=' . $_W['account']['level']);
			return false;
		}
		if (!in_array($type, array('start', 'success', 'fail', 'notice', 'join'))) {
			WeUtility::logging('fatal', '[send_mgroupon_svcmsg] 非法参数，type=' . $type);
			return false;
		}
		if (!$openid) {
			WeUtility::logging('fatal', '[send_mgroupon_svcmsg] 非法参数，openid is null');
			return false;
		}
		$setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_MGROUPON);
		if (!$setting) {
			WeUtility::logging('fatal', '[send_mgroupon_svcmsg] 没有配置客服消息');
			return false;
		}
		$list_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('mgroupon', array('act' => 'list'));
		$my_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('mgroupon', array('act' => 'display'));
		$detail_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('mgroupon', array('act' => 'invite', 'id' => $order['mgroupon_id']));
		$vars = array('{订单号}' => $order['ordersn'], '{人数}' => $order['remain_total'], '{列表页}' => $list_url, '{我的拼团}' => $my_url, '{详情页}' => $detail_url, '{好友昵称}' => $order['friend_nickname']);
		$content = $setting[$type]['content'];
		foreach ($vars as $k => $v) {
			if (strpos($content, $k) !== false) {
				$content = str_replace($k, $v, $content);
			}
		}
		if ($url == '') {
			$url = $setting[$type]['url'];
			foreach ($vars as $k => $v) {
				if (strpos($url, $k) !== false) {
					$url = str_replace($k, $v, $url);
				}
			}
		}
		$message = array('msgtype' => 'news', 'news' => array('articles' => array(array('title' => urlencode($setting[$type]['title']), 'description' => urlencode($content), 'url' => urlencode($url), 'picurl' => tomedia($setting[$type]['picurl'])))), 'touser' => $openid);
		$ret = $account->sendCustomNotice($message);
		if (is_error($ret)) {
			WeUtility::logging("fatal", "[send_mgroupon_svcmsg] 客服消息发送失败：openid={$openid}, ret=" . var_export($ret, true) . ", message=" . var_export($message, true));
			return false;
		}
		if (defined('ONLINE_DEVELOPMENT')) {
			WeUtility::logging("trace", "[send_mgroupon_svcmsg] 客服消息发送成功：openid={$openid}, message=" . var_export($message, true) . ', title=' . $setting[$type]['title'] . ', content=' . $content . ', url=' . $url . ', picurl=' . tomedia($setting[$type]['picurl']));
		}
		return true;
	}
	public function send_shop_tmplmsg($shop, $openid, $status, $remark, $url = '')
	{
		global $_W;
		if (!$_W['uniacid']) {
			$_W['uniacid'] = $shop['uniacid'];
		}
		$account = $this->_init_account();
		if ($_W['account']['level'] != 4) {
			WeUtility::logging('fatal', '[send_shop_tmplmsg] 非认证服务号没有模板消息权限, name=' . $_W['account']['name'] . ', level=' . $_W['account']['level']);
			return false;
		}
		if (!$openid) {
			WeUtility::logging('fatal', '[send_shop_tmplmsg] 非法参数，openid is null');
			return false;
		}
		$setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_SHOP);
		if (!$setting) {
			WeUtility::logging('fatal', '[send_shop_tmplmsg] 没有配置模板消息');
			return false;
		}
		$vars = array('{商户名称}' => $shop['title'], '{审核结果}' => SupermanUtil::get_shop_status_title($status), '{审核备注}' => $remark, '{操作时间}' => date('Y-m-d H:i:s', TIMESTAMP));
		$message = array('template_id' => $setting['tmpl_id'], 'postdata' => array(), 'url' => $url, 'topcolor' => '#008000');
		$tmpl_variable = explode("\n", $setting['tmpl_variable']);
		foreach ($tmpl_variable as $line) {
			$arr = explode("=", trim($line));
			$arr = array_map('trim', $arr);
			$value = $arr[1];
			foreach ($vars as $k => $v) {
				if (strpos($value, $k) !== false) {
					$value = str_replace($k, $v, $value);
				}
			}
			$message['postdata'][$arr[0]] = array('value' => $value, 'color' => '#173177');
		}
		$ret = $account->sendTplNotice($openid, $message['template_id'], $message['postdata'], $message['url'], $message['topcolor']);
		if ($ret !== true) {
			WeUtility::logging("fatal", "[send_shop_tmplmsg] 模板消息发送失败：openid={$openid}, ret=" . var_export($ret, true) . ", message=" . var_export($message, true));
			return false;
		}
		if (defined('ONLINE_DEVELOPMENT')) {
			WeUtility::logging("trace", "[send_shop_tmplmsg] 模板消息发送成功：template_id={$message['template_id']}, openid={$openid}, message=" . var_export($message, true));
		}
		return true;
	}
	public function send_order_tmplmsg($type, $order, $openid, $url = '')
	{
		global $_W;
		if (!$_W['uniacid']) {
			$_W['uniacid'] = $order['uniacid'];
		}
		$account = $this->_init_account();
		if ($_W['account']['level'] != 4) {
			WeUtility::logging('fatal', '[send_order_tmplmsg] 非认证服务号没有模板消息权限, name=' . $_W['account']['name'] . ', level=' . $_W['account']['level']);
			return false;
		}
		if (!in_array($type, array('submit', 'pay', 'send', 'cancel', 'refund'))) {
			WeUtility::logging('fatal', '[send_order_tmplmsg] 非法参数，type=' . $type);
			return false;
		}
		if (!$openid) {
			WeUtility::logging('fatal', '[send_order_tmplmsg] 非法参数，openid is null');
			return false;
		}
		$setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_ORDER);
		if (!$setting) {
			WeUtility::logging('fatal', '[send_order_tmplmsg] 没有配置模板消息');
			return false;
		}
		$filter = array('orderid' => $order['id']);
		$order_item = M::t('superman_mall_order_item')->fetchall($filter, '', 0, -1);
		$titles = '';
		if ($order_item) {
			foreach ($order_item as $item) {
				$titles .= $item['title'] . '、';
			}
			$titles = trim($titles, '、');
			if (mb_strlen($titles) > 60) {
				$titles = cutstr($titles, 60) . '...';
			}
		}
		$vars = array('{订单号}' => $order['ordersn'], '{创建时间}' => date('Y-m-d H:i:s', $order['createtime']), '{订单金额}' => '￥' . $order['price'], '{操作时间}' => date('Y-m-d H:i:s', TIMESTAMP), '{快递公司}' => $order['express_title'] . ' ' . $order['custom_delivery'], '{快递单号}' => $order['express_no'], '{收货地址}' => $order['address'], '{订单商品}' => $titles);
		$message = array('template_id' => $setting[$type]['tmpl_id'], 'postdata' => array(), 'url' => $url, 'topcolor' => '#008000');
		$tmpl_variable = explode("\n", $setting[$type]['tmpl_variable']);
		foreach ($tmpl_variable as $line) {
			$arr = explode("=", trim($line));
			$arr = array_map('trim', $arr);
			$value = $arr[1];
			foreach ($vars as $k => $v) {
				if (strpos($value, $k) !== false) {
					$value = str_replace($k, $v, $value);
				}
			}
			$message['postdata'][$arr[0]] = array('value' => $value, 'color' => '#173177');
		}
		$ret = $account->sendTplNotice($openid, $message['template_id'], $message['postdata'], $message['url'], $message['topcolor']);
		if ($ret !== true) {
			WeUtility::logging("fatal", "[send_order_tmplmsg] 模板消息发送失败：openid={$openid}, ret=" . var_export($ret, true) . ", message=" . var_export($message, true));
			return false;
		}
		if (defined('ONLINE_DEVELOPMENT')) {
			WeUtility::logging("trace", "[send_order_tmplmsg] 模板消息发送成功：template_id={$message['template_id']}, openid={$openid}, message=" . var_export($message, true));
		}
		return true;
	}
	public function send_partner_manage_tmplmsg($partner, $status, $extra)
	{
		global $_W;
		if (!$_W['uniacid']) {
			$_W['uniacid'] = $partner['uniacid'];
		}
		$account = $this->_init_account();
		if ($_W['account']['level'] != 4) {
			WeUtility::logging('fatal', '[send_partner_manage_tmplmsg] 非认证服务号没有模板消息权限, name=' . $_W['account']['name'] . ', level=' . $_W['account']['level']);
			return false;
		}
		if (!$partner['openid']) {
			WeUtility::logging('fatal', '[send_partner_manage_tmplmsg] 非法参数，openid is null');
			return false;
		}
		$setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_PARTNER);
		if (!$setting['manage'] || $setting['manage']['tmpl_id'] == '') {
			WeUtility::logging('fatal', '[send_partner_manage_tmplmsg] 没有配置模板消息');
			return false;
		}
		$vars = array('{审核结果}' => SupermanUtil::get_partner_status_title($status), '{操作时间}' => date('Y-m-d H:i:s', TIMESTAMP));
		$message = array('template_id' => $setting['manage']['tmpl_id'], 'postdata' => array(), 'url' => $extra['url'], 'topcolor' => '#008000');
		$tmpl_variable = explode("\n", $setting['manage']['tmpl_variable']);
		foreach ($tmpl_variable as $line) {
			$arr = explode("=", trim($line));
			$arr = array_map('trim', $arr);
			$value = $arr[1];
			foreach ($vars as $k => $v) {
				if (strpos($value, $k) !== false) {
					$value = str_replace($k, $v, $value);
				}
			}
			$message['postdata'][$arr[0]] = array('value' => $value, 'color' => '#173177');
		}
		$ret = $account->sendTplNotice($partner['openid'], $message['template_id'], $message['postdata'], $message['url'], $message['topcolor']);
		if ($ret !== true) {
			WeUtility::logging("fatal", "[send_partner_manage_tmplmsg] 模板消息发送失败：openid={$partner['openid']}, ret=" . var_export($ret, true) . ", message=" . var_export($message, true));
			return false;
		}
		if (defined('ONLINE_DEVELOPMENT')) {
			WeUtility::logging("trace", "[send_partner_manage_tmplmsg] 模板消息发送成功：template_id={$message['template_id']}, openid={$partner['openid']}, message=" . var_export($message, true));
		}
		return true;
	}
	public function check_user_permission($permission, $exit = true)
	{
		global $_W, $_GPC;
		$has_permission = false;
		if (defined('IN_SUPERMAN_MALL_PLATFORM') && defined('IN_SUPERMAN_MALL_ADMIN')) {
			$has_permission = false;
		} else {
			do {
				if ($_W['isfounder']) {
					$has_permission = true;
					break;
				}
				if ($this->_is_account_admin()) {
					$has_permission = true;
					break;
				}
				if (empty($this->web['user_permission'])) {
					$has_permission = false;
					break;
				}
				if (is_string($this->web['user_permission']) && $this->web['user_permission'] == 'all') {
					$has_permission = true;
					break;
				}
				foreach ($this->web['user_permission'] as $p) {
					if ($permission == $p || strexists($permission, $p) || strexists($p, $permission)) {
						$has_permission = true;
						break;
					}
				}
			} while (0);
		}
		if (!$has_permission) {
			if ($exit) {
				message('没有操作权限！', '', 'error');
			} else {
				return false;
			}
		}
		return true;
	}
	public function checkauth()
	{
		global $_W, $_GPC;
		if (!$_W['member']['uid']) {
			if ($_W['container'] == 'wechat') {
				if (!defined('LOCAL_DEVELOPMENT')) {
					if (defined('ONLINE_DEVELOPMENT')) {
						WeUtility::logging('debug', '[checkauth] _W[fans]=' . var_export($_W['fans'], true));
					}
					if (!empty($_W['fans']['openid'])) {
						$fan = mc_fansinfo($_W['fans']['openid']);
						if (defined('ONLINE_DEVELOPMENT')) {
							WeUtility::logging('debug', '[checkauth] mc_fansinfo fan=' . var_export($fan, true));
						}
						if (empty($fan)) {
							mc_oauth_userinfo();
						}
						if (empty($fan['uid'])) {
							$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
							$salt = random(8);
							$data = array('uniacid' => $_W['uniacid'], 'email' => md5($fan['openid']) . '@we7.cc', 'salt' => $salt, 'groupid' => $default_groupid, 'createtime' => TIMESTAMP, 'password' => md5($fan['openid'] . $salt . $_W['config']['setting']['authkey']), 'nickname' => stripslashes($fan['tag']['nickname']), 'avatar' => $fan['tag']['headimgurl'], 'gender' => $fan['tag']['sex'], 'nationality' => $fan['tag']['country'], 'resideprovince' => $fan['tag']['province'] . '省', 'residecity' => $fan['tag']['city'] . '市');
							pdo_insert('mc_members', $data);
							$fan['uid'] = pdo_insertid();
							if (defined('ONLINE_DEVELOPMENT')) {
								WeUtility::logging('debug', '[checkauth] init mc_members, uid=' . $fan['uid']);
							}
						}
						if (empty($fan['fanid'])) {
							$data = array('openid' => $fan['openid'], 'uid' => $fan['uid'], 'acid' => $_W['acid'], 'uniacid' => $_W['uniacid'], 'salt' => random(8), 'updatetime' => TIMESTAMP, 'nickname' => stripslashes($fan['tag']['nickname']), 'follow' => 0, 'followtime' => 0, 'unfollowtime' => 0, 'tag' => base64_encode(iserializer($fan['tag'])));
							pdo_insert('mc_mapping_fans', $data);
							$fan['fanid'] = pdo_insertid();
							if (defined('ONLINE_DEVELOPMENT')) {
								WeUtility::logging('debug', '[checkauth] init mc_mapping_fans, fanid=' . $fan['fanid']);
							}
						}
						if (!empty($fan['uid']) && _mc_login(array('uid' => $fan['uid']))) {
							if (defined('ONLINE_DEVELOPMENT')) {
								WeUtility::logging('debug', '[checkauth] _mc_login success');
							}
							return true;
						}
					} else {
						if (defined('ONLINE_DEVELOPMENT')) {
							WeUtility::logging('debug', '[checkauth] mc_oauth_userinfo start');
						}
						mc_oauth_userinfo();
					}
				}
			}
			if ($_W['isajax']) {
				$this->json(ERRNO::NOT_LOGIN, '未登录，跳转中...', array('url' => url("auth/login", array("forward" => base64_encode($_SERVER["QUERY_STRING"])))));
			} else {
				$this->message('未登录，跳转中...', url("auth/login", array("forward" => base64_encode($_SERVER["QUERY_STRING"]))), 'info');
			}
		}
		return true;
	}
	public function order_refund($uid, $money, $logs = array())
	{
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
		$credit_type = $setting['creditbehaviors']['currency'];
		$ret = mc_credit_update($uid, $credit_type, $money, $logs);
		if (is_error($ret)) {
			message('退款失败：ret=' . var_export($ret, true), '', 'error');
		}
		return $ret;
	}
	public function check_plugin_permission($plugin_name, $shopid)
	{
		global $_W;
		$skey = SupermanUtil::get_skey(SUPERMAN_SKEY_SHOP_PLUGIN, $shopid);
		$setting = M::t('superman_mall_kv')->fetch_value($skey);
		if (!$setting) {
			return error(-1, '该功能没有操作权限，请联系管理员！');
		}
		if (!$setting[$plugin_name]['open']) {
			return error(-1, '该功能没有操作权限，请联系管理员！');
		}
		if ($setting[$plugin_name]['starttime'] > TIMESTAMP) {
			return error(-1, '该功能操作权限未开始(' . date('Y-m-d H:i:s', $setting[$plugin_name]['starttime']) . ')，请联系管理员！');
		}
		if ($setting[$plugin_name]['endtime'] != -1 && $setting[$plugin_name]['endtime'] < TIMESTAMP) {
			return error(-1, '该功能操作权限已过期(' . date('Y-m-d H:i:s', $setting[$plugin_name]['endtime']) . ')，请联系管理员！');
		}
		return array('start' => date('Y-m-d H:i:s', $setting[$plugin_name]['starttime']), 'end' => $setting[$plugin_name]['endtime'] == -1 ? '永久' : date('Y-m-d H:i:s', $setting[$plugin_name]['endtime']));
	}
	public function check_web_shop()
	{
		global $_W;
		if (empty($this->shop)) {
			message('该操作需要选择商户！', $this->createWebUrl('shop', array('act' => 'display', 'switch' => 'yes', 'referer' => urlencode($_W['siteurl']))), 'warning');
		}
	}
	public function short_url($url)
	{
		global $_W;
		if (defined('LOCAL_DEVELOPMENT')) {
			return $url;
		} else {
			if (in_array($_W['account']['level'], array(3, 4))) {
				$account = $this->_init_account();
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
	public function get_shop_list()
	{
		global $_W;
		$list = M::t('superman_mall_shop')->fetchall(array('uniacid' => $_W['uniacid']), '', 0, -1);
		return $list;
	}
	public function create_shop_web_url($shopid)
	{
		global $_W;
		$setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
		if ($setting && !empty($setting['web_domain'])) {
			$urls = parse_url($_W['siteroot']);
			return ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_W['uniacid'] . '.' . $setting['web_domain'] . $urls['path'];
		} else {
			return $_W['siteroot'] . 'addons/superman_mall/admin/index.php?shopid=' . $shopid;
		}
	}
	public function update_item_total($item, $order_item, $op = '-')
	{
		global $_W, $_GPC;
		if ($op == '+') {
			if ($order_item['skuid'] > 0) {
				M::t('superman_mall_item_sku')->increment(array('total' => $order_item['total']), array('id' => $order_item['skuid']));
			}
			M::t('superman_mall_item')->increment(array('total' => $order_item['total']), array('id' => $order_item['itemid']));
		} else {
			if ($op == '-') {
				if ($order_item['skuid'] > 0) {
					$sku = M::t('superman_mall_item_sku')->fetch($order_item['skuid']);
					if ($sku['total'] <= 0 || $sku['total'] - $order_item['total'] < 0) {
						WeUtility::logging('warning', '[update_item_total], sku total not enough, order_item=' . var_export($order_item, true));
					} else {
						M::t('superman_mall_item_sku')->decrement(array('total' => $order_item['total']), array('id' => $order_item['skuid']));
					}
				}
				if ($item['total'] <= 0 || $item['total'] - $order_item['total'] < 0) {
					WeUtility::logging('warning', '[update_item_total], item total not enough, order_item=' . var_export($order_item, true));
				} else {
					M::t('superman_mall_item')->decrement(array('total' => $order_item['total']), array('id' => $order_item['itemid']));
				}
			}
		}
	}
	public function create_ordersn()
	{
		do {
			$ordersn = date('Ymd') . random(6, 1);
			$row = pdo_fetchcolumn("SELECT id FROM " . tablename('superman_mall_order') . " WHERE ordersn=:ordersn", array(':ordersn' => $ordersn));
			$exist = $row ? true : false;
		} while ($exist);
		return SUPERMAN_ORDERSN_PREFIX . $ordersn;
	}
	public function template($filename)
	{
		global $_W;
		if (defined('IN_SYS')) {
			if (defined('IN_SUPERMAN_MALL_ADMIN')) {
				if (Agent::isMobile()) {
					$source = MODULE_ROOT . '/template/web/shop_admin/mobile/default/' . $filename . '.html';
					$compile = MODULE_ROOT . '/data/tpl/web/shop_admin/mobile/default/' . $filename . 'tpl.php';
				} else {
					$source = MODULE_ROOT . '/template/web/shop_admin/' . $this->web_template_name . '/' . $filename . '.html';
					$compile = MODULE_ROOT . '/data/tpl/web/shop_admin/' . $this->web_template_name . '/' . $filename . 'tpl.php';
				}
				if (!is_file($source)) {
					$source = MODULE_ROOT . '/template/web/shop_admin/default/' . $filename . '.html';
				}
			} else {
				$source = MODULE_ROOT . '/template/web/' . $this->web_template_name . '/' . $filename . '.html';
				$compile = MODULE_ROOT . '/data/tpl/web/' . $this->web_template_name . '/' . $filename . 'tpl.php';
				if (!is_file($source)) {
					$source = MODULE_ROOT . '/template/web/default/' . $filename . '.html';
				}
			}
		} else {
			if (defined('SUPERMAN_MOBILE_ADMIN')) {
				$source = MODULE_ROOT . '/template/mobile/admin/default/' . $filename . '.html';
				$compile = MODULE_ROOT . '/data/tpl/mobile/admin/default/' . $filename . 'tpl.php';
			} else {
				$source = MODULE_ROOT . '/template/mobile/' . $this->mobile_template_name . '/' . $filename . '.html';
				$compile = MODULE_ROOT . '/data/tpl/mobile/' . $this->mobile_template_name . '/' . $filename . 'tpl.php';
			}
			if (!is_file($source)) {
				$source = MODULE_ROOT . '/template/mobile/default/' . $filename . '.html';
			}
		}
		if (!is_file($source)) {
			die("Error: template source '{$filename}' is not exist!");
		}
		$paths = pathinfo($compile);
		$compile = str_replace($paths['filename'], $_W['uniacid'] . '_' . $paths['filename'], $compile);
		if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
			$this->template_compile($source, $compile, true);
		}
		return $compile;
	}
	function template_compile($from, $to, $inmodule = false)
	{
		$path = dirname($to);
		if (!is_dir($path)) {
			mkdirs($path);
		}
		$content = template_parse(file_get_contents($from), $inmodule);
		file_put_contents($to, $content);
	}
	private function _init_account()
	{
		global $_W, $_GPC;
		static $account = null;
		if (!is_null($account)) {
			return $account;
		}
		if (empty($_W['account'])) {
			if (isset($_W['uniacid']) && $_W['uniacid']) {
				$_W['account'] = uni_fetch($_W['uniacid']);
			} else {
				if (isset($_W['acid']) && $_W['acid']) {
					$_W['account'] = account_fetch($_W['acid']);
				} else {
					return error(-1, '初始化失败，缺少acid||uniacid参数');
				}
			}
		}
		if ($_W['account']['level'] < 3) {
			return error(-1, '公众号没有经过认证');
		}
		$account = WeAccount::create();
		if (is_null($account)) {
			return error(-1, '创建公众号操作对象失败');
		}
		return $account;
	}
	private function _init_app()
	{
		global $_W, $_GPC;
		if ($_W['member']['uid']) {
			$_W['member'] = array_merge($_W['member'], mc_fetch($_W['member']['uid'], array('nickname', 'avatar', 'mobile')));
			$data = array();
			if (!empty($_W['fans'])) {
				if (empty($_W['member']['nickname'])) {
					$data['nickname'] = $_W['fans']['tag']['nickname'];
				}
				if (empty($_W['member']['avatar'])) {
					$data['avatar'] = $_W['fans']['tag']['headimgurl'] ? $_W['fans']['tag']['headimgurl'] : $_W['fans']['tag']['avatar'];
				}
			} else {
				$fan = mc_fansinfo($_W['member']['uid']);
				if ($fan) {
					if (empty($_W['member']['nickname'])) {
						$data['nickname'] = $fan['tag']['nickname'];
					}
					if (empty($_W['member']['avatar'])) {
						$data['avatar'] = $fan['tag']['headimgurl'] ? $fan['tag']['headimgurl'] : $fan['tag']['avatar'];
					}
				}
			}
			if (!empty($data)) {
				pdo_update('mc_members', $data, array('uid' => $_W['member']['uid']));
				$_W['member']['nickname'] = $data['nickname'];
				$_W['member']['avatar'] = $data['avatar'];
			}
		} else {
			$_W['member'] = array('nickname' => '微信昵称', 'avatar' => '');
		}
		$platform_share = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHARE);
		if (!empty($platform_share)) {
			$this->share = array('title' => $platform_share['system']['title'], 'link' => $_W['siteurl'], 'imgUrl' => tomedia($platform_share['system']['imgurl']), 'content' => $platform_share['system']['desc']);
		}
		$this->_init_partner_poster();
		$this->_load_footer_nav();
		if ($_W['member']['uid']) {
			$this->cart_count = M::t('superman_mall_cart')->count(array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
			$this->cart_count = intval($this->cart_count);
		}
	}
	private function _load_footer_nav()
	{
		global $_W;
		$this->footer_nav = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_FOOTER_NAV);
		if ($this->footer_nav) {
			usort($this->footer_nav, array(SupermanUtil, "sort_displayorder_desc"));
			foreach ($this->footer_nav as $k => &$nav) {
				if (!$nav['isshow']) {
					unset($this->footer_nav[$k]);
				}
				$nav['active'] = false;
				$url = str_replace('./', '/', $nav['url']);
				$url = str_replace('//', '/', $url);
				if (strexists($_W['siteurl'], $url)) {
					$nav['active'] = true;
				}
			}
			unset($nav);
		}
	}
	private function _init_partner_poster()
	{
		global $_W, $_GPC;
		$partnerid = intval($_GPC['partnerid']) ? intval($_GPC['partnerid']) : intval($_GPC['__partnerid']);
		$posterid = intval($_GPC['posterid']) ? intval($_GPC['posterid']) : intval($_GPC['__posterid']);
		if ($partnerid && $posterid) {
			if (empty($_W['member']['uid'])) {
				if (!isset($_GPC['__partnerid'])) {
					isetcookie('__partnerid', $partnerid, 30 * 365 * 86400);
				}
				if (!isset($_GPC['__posterid'])) {
					isetcookie('__posterid', $posterid, 30 * 365 * 86400);
				}
				$this->checkauth();
			}
			$row = M::t('superman_mall_partner')->fetch(array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
			if (empty($row)) {
				$partner = M::t('superman_mall_partner')->fetch($partnerid);
				$poster = M::t('superman_mall_partner_poster')->fetch($posterid);
				if ($partner && $partner['status'] == 1 && $poster && $poster['partner_downline'] == 1) {
					$partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
					$_data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['fans']['openid'], 'groupid' => isset($partner_setting['partner']['groupid']) ? intval($partner_setting['partner']['groupid']) : 0, 'status' => 1, 'createtime' => TIMESTAMP, 'recommendid' => $partnerid, 'position' => $partner['position'] + 1);
					$new_id = M::t('superman_mall_partner')->insert($_data);
					if ($new_id) {
						$upgradeid = $partner['id'];
						for ($i = 1; $i <= 3; $i++) {
							$arr = array('downline' . $i => 1);
							M::t('superman_mall_partner')->increment($arr, array('id' => $upgradeid));
							$rec = M::t('superman_mall_partner')->fetch($upgradeid);
							$upgradeid = $rec['recommendid'];
							if ($upgradeid <= 0) {
								break;
							}
						}
					}
				} else {
					if (defined('SUPERMAN_DEVELOPMENT')) {
						WeUtility::logging('debug', '[_init_partner_poster] partner=' . var_export($partner, true) . ', poster=' . var_export($poster, true));
					}
				}
			}
		}
	}
	private function _init_web()
	{
		global $_W, $_GPC;
		if (defined('IN_SUPERMAN_MALL_ADMIN')) {
			$this->_init_shop_user_permission();
			$this->shop = $_W['superman_mall']['shop'];
			$this->shop_user = $_W['superman_mall']['shop_user'];
			$this->_init_shop_navigation();
			$this->_init_shop_page_title();
		} else {
			$this->_init_web_user_permission();
			$this->_init_web_shop();
			$this->_init_web_message_mgroupon();
		}
	}
	private function _init_shop_user_permission()
	{
		global $_W;
		$user = $_W['superman_mall']['shop_user'];
		if ($user['groupid'] > 0) {
			$this->web['user_permission'] = $user['group']['permission'];
			if ($user['group']['permission'] == 'all') {
				$this->web['user_permission'] = 'all';
			} else {
				$this->web['user_permission'] = explode('|', $user['group']['permission']);
			}
		} else {
			$this->web['user_permission'] = 'all';
		}
	}
	private function _init_shop_navigation()
	{
		$this->shop_navs = SuermanMallNav::shop($this->web['user_permission']);
		foreach ($this->shop_navs as $nav) {
			if (!empty($nav['active'])) {
				$this->shop_active_nav = $nav;
				foreach ($this->shop_active_nav['items'] as $item) {
					foreach ($item['menus'] as $menu) {
						if (!empty($menu['active'])) {
							$this->shop_active_menu = $menu;
							break;
						}
					}
					if (!empty($this->shop_active_menu)) {
						break;
					}
				}
				break;
			}
		}
	}
	private function _init_shop_page_title()
	{
		if (!empty($this->shop_active_nav['startpage']['active'])) {
			$this->shop_page_title .= $this->shop_active_nav['startpage']['title'];
		}
		if ($this->shop_active_menu) {
			$this->shop_page_title .= $this->shop_active_menu['title'];
		}
		if ($this->shop_active_nav) {
			if (!empty($this->shop_page_title)) {
				$this->shop_page_title .= '_';
			}
			$this->shop_page_title .= $this->shop_active_nav['title'];
		}
		if (!empty($this->shop_page_title)) {
			$this->shop_page_title .= ' - ';
		}
		$this->shop_page_title .= $this->shop['title'];
	}
	private function _init_web_user_permission()
	{
		global $_W;
		if (!$_W['isfounder']) {
			$account_users = M::t('uni_account_users')->fetch(array('uid' => $_W['uid'], 'uniacid' => $_W['uniacid']));
			if ($account_users) {
				$this->web['account_role'] = $account_users['role'];
			}
		}
		$row = M::t('users_permission')->fetch(array('uniacid' => $_W['uniacid'], 'uid' => $_W['uid'], 'type' => SUPERMAN_MODULE_NAME));
		if ($row) {
			if ($row['permission'] == 'all') {
				$this->web['user_permission'] = 'all';
			} else {
				$this->web['user_permission'] = explode('|', $row['permission']);
			}
		} else {
			$row = M::t('users_permission')->fetch(array('uniacid' => $_W['uniacid'], 'uid' => $_W['uid']));
			if (!$row) {
				$this->web['user_permission'] = 'all';
			}
		}
	}
	private function _init_web_shop()
	{
		global $_W, $_GPC;
		if ($_W['isfounder'] || $this->_is_account_admin() || $this->check_user_permission('superman_mall_menu_' . $this->do, false)) {
			$shopid = intval($_GPC['shopid']) ? intval($_GPC['shopid']) : intval($_GPC['__superman_mall_web_shopid:' . $_W['uniacid']]);
			if ($shopid > 0) {
				$this->shop = M::t('superman_mall_shop')->fetch($shopid);
				if ($this->shop) {
					isetcookie('__superman_mall_web_shopid:' . $_W['uniacid'], $shopid, 30 * 86400);
				}
			}
		}
	}
	private function _init_web_message_mgroupon()
	{
		global $_W, $_GPC;
		$filter = array('uniacid' => $_W['uniacid'], 'skey' => SUPERMAN_SKEY_MESSAGE_MGROUPON);
		$row = M::t('superman_mall_kv')->fetch($filter);
		if (!$row) {
			$data = array('uniacid' => $_W['uniacid'], 'skey' => SUPERMAN_SKEY_MESSAGE_MGROUPON, 'svalue' => iserializer(array('start' => array('title' => '发起拼团', 'picurl' => '', 'url' => '{详情页}', 'content' => "报告团长：您已成功发起拼团，赶快邀请小伙伴们来参团，组团成功才能享受优惠哦！~~~"), 'success' => array('title' => '组团成功', 'picurl' => '', 'url' => '{详情页}', 'content' => "恭喜您，您购买的【订单号：{订单号}】订单组团成功，我们会尽快为您安排发货，感谢您的选购！"), 'fail' => array('title' => '组团失败', 'picurl' => '', 'url' => '{详情页}', 'content' => "非常抱歉，您购买的【订单号：{订单号}】订单组团失败，订单款项已退回您的账号余额！"), 'notice' => array('title' => '好友加入拼团', 'picurl' => '', 'url' => '{详情页}', 'content' => "参团提醒，您的好友【{好友昵称}】已参团！【还差{人数}人组团成功】"), 'join' => array('title' => '加入拼团', 'picurl' => '', 'url' => '{详情页}', 'content' => "恭喜您，您已加入拼团！【还差{人数}人组团成功】"))));
			$new_id = M::t('superman_mall_kv')->insert($data);
			if (!$new_id) {
				message('初始化拼团消息失败！', '', 'error');
			}
		}
	}
	private function _check_wechat()
	{
		$acl = array('poster' => array('partner'));
		if (!isset($acl[$this->do]) || !in_array($this->act, $acl[$this->do])) {
			return true;
		}
		return false;
	}
	private function _check_debug()
	{
		global $_W, $_GPC;
		$config = $this->module['config'];
		if ($config['base']['debug']) {
			if (!$_W['member']['uid'] && !$_W['uid']) {
				$this->checkauth();
			}
			if ($_W['member']['uid'] && (!$config['base']['debug_uids'] || !in_array($_W['member']['uid'], $config['base']['debug_uids']))) {
				$message = $config['base']['debug_message'] != '' ? $config['base']['debug_message'] : '系统升级中...';
				$this->message($message, '', 'info');
			}
		}
	}
	public function update_item_sales($item, $order_item, $op = '+')
	{
		global $_W, $_GPC;
		if ($op == '+') {
			if ($order_item['skuid'] > 0) {
				M::t('superman_mall_item_sku')->increment(array('sales' => $order_item['total']), array('id' => $order_item['skuid']));
			}
			M::t('superman_mall_item')->increment(array('sales' => $order_item['total']), array('id' => $order_item['itemid']));
		} else {
			if ($op == '-') {
				if ($order_item['skuid'] > 0) {
					$sku = M::t('superman_mall_item_sku')->fetch($order_item['skuid']);
					if ($sku['sales'] <= 0 || $sku['sales'] - $order_item['total'] < 0) {
						WeUtility::logging('warning', '[update_item_sales], sku sales not enough, order_item=' . var_export($order_item, true));
					} else {
						M::t('superman_mall_item_sku')->decrement(array('sales' => $order_item['total']), array('id' => $order_item['skuid']));
					}
				}
				if ($item['sales'] <= 0 || $item['sales'] - $order_item['total'] < 0) {
					WeUtility::logging('warning', '[update_item_sales], item sales not enough, order_item=' . var_export($order_item, true));
				} else {
					M::t('superman_mall_item')->decrement(array('sales' => $order_item['total']), array('id' => $order_item['itemid']));
				}
			}
		}
	}
	private function _is_account_admin()
	{
		return in_array($this->web['account_role'], array('owner', 'manager'));
	}
	private function _init_trade_status()
	{
		load()->classs('cloudapi');
		$api = new CloudApi();
		$result = $api->get('site', 'module');
		$this->module['trade_status'] = is_array($result) && $result['trade'] == 1 ? 1 : 0;
		if ($this->module['trade_status'] == 0 && !$this->inMobile && !defined('IN_SUPERMAN_MALL_ADMIN')) {
			$siteinfo = M::t('superman_mall_setting')->fetch_value(SUPERMAN_SETTING_SITE);
			$cloud = new SupermanMallCloud($this->module, $siteinfo);
			$result = $cloud->check();
			if ($result['errno'] == 102) {
				message($result['errmsg'], '', 'error');
			}
		}
	}
	private function _background_running()
	{
		global $_W;
		$this->_check_order();
		$this->_check_partner();
		$this->__report();
	}
	private function _check_order()
	{
		global $_W;
		$path = MODULE_ROOT . '/data/' . $_W['uniacid'];
		mkdirs($path);
		$order_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_ORDER);
		$ret = $this->_check_running_interval_time($path . '/order_close.txt', 300);
		if ($ret) {
			if (isset($order_setting['order_auto_close']) && $order_setting['order_auto_close'] > 0) {
				$filter = array('uniacid' => $_W['uniacid'], 'shopid' => '# >0', 'status' => 0, 'createtime' => '# <' . (TIMESTAMP - intval(floatval($order_setting['order_auto_close']) * 60 * 60)));
				$list = M::t('superman_mall_order')->fetchall($filter, '', 0, 100);
				if ($list) {
					foreach ($list as $li) {
						if ($li['pay_credit'] > 0) {
							$log = array($_W['uid'], '订单' . $li['ordersn'] . '支付余额退还', 'superman_mall');
							$this->order_refund($li['uid'], $li['pay_credit'], $log);
						}
						$order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $li['id']), '', 0, -1);
						foreach ($order_items as $order_item) {
							$item = M::t('superman_mall_item')->fetch($order_item['itemid']);
							if ($item && $item['minus_total'] == 2) {
								$this->update_item_total($item, $order_item, '+');
							}
						}
						M::t('superman_mall_order')->update(array('status' => -3, 'updatetime' => TIMESTAMP), array('id' => $li['id']));
					}
				}
			}
		}
		$ret = $this->_check_running_interval_time($path . '/order_confirm.txt', 600);
		if ($ret) {
			if (isset($order_setting['order_auto_receive']) && $order_setting['order_auto_receive'] > 0) {
				$filter = array('uniacid' => $_W['uniacid'], 'shopid' => '# >0', 'status' => 2, 'pay_type' => '# !=3', 'updatetime' => '# <' . (TIMESTAMP - intval(floatval($order_setting['order_auto_receive']) * 24 * 60 * 60)));
				$list = M::t('superman_mall_order')->fetchall($filter, '', 0, -1);
				if ($list) {
					foreach ($list as $li) {
						$data = array('status' => 3, 'updatetime' => TIMESTAMP);
						$condition = array('id' => $li['id']);
						$affected_rows = M::t('superman_mall_order')->update($data, $condition);
						WeUtility::logging('trace', '[_check_order] order_auto_receive, affected_rows=' . $affected_rows);
					}
				}
			}
		}
		$ret = $this->_check_running_interval_time($path . '/order_complete.txt', 300);
		if ($ret) {
			if (isset($order_setting['order_auto_comment']) && $order_setting['order_auto_comment'] > 0) {
				$filter = array('uniacid' => $_W['uniacid'], 'shopid' => '# >0', 'status' => 3, 'updatetime' => '# <' . (TIMESTAMP - intval(floatval($order_setting['order_auto_comment']) * 24 * 60 * 60)));
				$list = M::t('superman_mall_order')->fetchall($filter, '', 0, 50);
				if ($list) {
					foreach ($list as $li) {
						$extend = $li['extend'] ? iunserializer($li['extend']) : array();
						if ($li['reward_credit'] > 0 && !isset($extend['discount_status']['reward_credit'])) {
							$ret = mc_credit_update($li['uid'], $li['credit_type'], $li['reward_credit'], array($li['uid'], '订单完成返积分', 'superman_mall'));
							if (is_error($ret)) {
								WeUtility::logging('fatal', '[comment.inc]订单已完成，但由于未知原因返积分失败，orderid=' . $li['id']);
							}
							$extend['discount_status']['reward_credit'] = 1;
						}
						M::t('superman_mall_order')->update(array('status' => 4, 'extend' => $extend ? iserializer($extend) : '', 'updatetime' => TIMESTAMP), array('id' => $li['id']));
						$order_items = M::t('superman_mall_order_item')->fetchall(array('orderid' => $li['id']), '', 0, -1);
						foreach ($order_items as $order_item) {
							if ($order_item['iscomment'] > 0) {
								continue;
							}
							M::t('superman_mall_order_item')->update(array('iscomment' => 1), array('id' => $order_item['id']));
							M::t('superman_mall_item')->increment(array('comment_count' => 1, 'comment_praise_count' => 1), array('id' => $order_item['itemid']));
							$_data = array('uniacid' => $_W['uniacid'], 'shopid' => $li['shopid'], 'uid' => $li['uid'], 'orderid' => $li['id'], 'itemid' => $order_item['itemid'], 'ordersn' => $li['ordersn'], 'score' => 5, 'message' => '默认好评！', 'img' => '', 'anonymous' => 1, 'status' => -1, 'dateline' => TIMESTAMP);
							M::t('superman_mall_comment')->insert($_data);
							$filter = array('uniacid' => $_W['uniacid'], 'shopid' => $li['shopid'], 'daytime' => date('Ymd', TIMESTAMP));
							$stat = M::t('superman_mall_stat')->fetch($filter);
							if ($stat) {
								M::t('superman_mall_stat')->increment(array('item_comment' => 1), array('id' => $stat['id']));
							} else {
								$_data = array('uniacid' => $_W['uniacid'], 'shopid' => $li['shopid'], 'daytime' => date('Ymd', TIMESTAMP), 'item_comment' => 1);
								M::t('superman_mall_stat')->insert($_data);
							}
						}
						unset($order_item);
					}
					unset($li);
				}
			}
		}
	}
	private function _check_partner()
	{
		global $_W;
		$path = MODULE_ROOT . '/data/' . $_W['uniacid'];
		mkdirs($path);
		if (isset($this->plugin_setting['partner']) && $this->plugin_setting['partner'] == 1) {
			$ret = $this->_check_running_interval_time($path . '/partner_check.txt', 600);
			if ($ret) {
				$partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
				$filter = array('uniacid' => $_W['uniacid'], 'uid' => '# >0', 'status' => '-1');
				$list = M::t('superman_mall_partner')->fetchall($filter, '', 0, -1);
				if ($list) {
					foreach ($list as $li) {
						if ($li['recommendid'] == 0) {
							if (!isset($partner_setting['partner']['join_condition']['type']) || $partner_setting['partner']['join_condition']['type'] == 1) {
								$member = mc_fansinfo($li['uid']);
								if (!$member['follow']) {
									continue;
								}
							} else {
								if ($partner_setting['partner']['join_condition']['type'] == 2) {
								} else {
									$filter = array('uniacid' => $_W['uniacid'], 'uid' => $li['uid'], 'shopid' => '# >0', 'status' => 4);
									if ($partner_setting['partner']['join_condition']['type'] == 3) {
										if (isset($partner_setting['partner']['join_condition']['order_tatal']['status'])) {
											$filter['status'] = intval($partner_setting['partner']['join_condition']['order_tatal']['status']);
										}
										$count = M::t('superman_mall_order')->count($filter);
									} else {
										if ($partner_setting['partner']['join_condition']['type'] == 4) {
											if (isset($partner_setting['partner']['join_condition']['order_money']['status'])) {
												$filter['status'] = intval($partner_setting['partner']['join_condition']['order_money']['status']);
											}
											$count = M::t('superman_mall_order')->sum($filter, 'price');
										}
									}
									if (!isset($partner_setting['partner']['join_condition']['limit']) || $count < floatval($partner_setting['partner']['join_condition']['limit'])) {
										continue;
									}
								}
							}
							M::t('superman_mall_partner')->update(array('status' => isset($partner_setting['partner']['check']) && $partner_setting['partner']['check'] == 1 ? 0 : 1), array('id' => $li['id']));
						} else {
							if (!isset($partner_setting['partner']['downline']) || $partner_setting['partner']['downline'] == 1) {
							} else {
								$filter = array('uniacid' => $_W['uniacid'], 'shopid' => '# >0', 'uid' => $li['uid']);
								if ($partner_setting['partner']['downline'] == 2) {
									$filter['status'] = array(3, 4);
									$count = M::t('superman_mall_order')->count($filter);
								} else {
									$filter['status'] = 4;
									$count = M::t('superman_mall_order')->count($filter);
								}
								if ($count <= 0) {
									continue;
								}
							}
							M::t('superman_mall_partner')->update(array('status' => 1), array('id' => $li['id']));
						}
					}
					unset($li);
				}
			}
			$ret = $this->_check_running_interval_time($path . '/commission_settlement.txt', 300);
			if ($ret) {
				$partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
				if (isset($partner_setting['partner']['commission_settlement']) && $partner_setting['partner']['commission_settlement'] == 1) {
					$filter = array('uniacid' => $_W['uniacid'], 'shopid' => '# >0', 'partner1_id' => '# >0', 'status' => '4', 'commission_status' => '0');
					if (isset($partner_setting['partner']['commission_interval']) && $partner_setting['partner']['commission_interval'] > 0) {
						$updatetime = TIMESTAMP - intval(floatval($partner_setting['partner']['commission_interval']) * 86400);
						$filter['updatetime'] = '# <' . $updatetime;
					}
					$list = M::t('superman_mall_order')->fetchall($filter, '', 0, 50);
					if ($list) {
						foreach ($list as $li) {
							$ret = M::t('superman_mall_order')->update(array('commission_status' => 1), array('id' => $li['id']));
							if ($ret) {
								for ($i = 1; $i <= 3; $i++) {
									$partner_id = $li['partner' . $i . '_id'];
									if (isset($partner_id) && $partner_id > 0) {
										$partner_commission = M::t('superman_mall_order_item')->sum(array('orderid' => $li['id']), 'partner' . $i . '_commission');
										if ($partner_commission > 0) {
											$ret = M::t('superman_mall_partner')->increment(array('commission_total' => $partner_commission, 'commission_balance' => $partner_commission), array('id' => $partner_id));
											$condition = array('uniacid' => $_W['uniacid'], 'partnerid' => $partner_id, 'daytime' => date('Ymd'));
											$stat = M::t('superman_mall_partner_stat')->fetch($condition);
											if ($stat) {
												M::t('superman_mall_partner_stat')->increment(array('commission_total' => $partner_commission), array('id' => $stat['id']));
											} else {
												$condition['type'] = 1;
												$condition['commission_total'] = $partner_commission;
												M::t('superman_mall_partner_stat')->insert($condition);
											}
										}
									}
								}
							} else {
								WeUtility::logging('warning', '[_check_partner] update superman_mall_order.commission_status failed, li=' . var_export($li, true));
							}
						}
					}
				}
			}
			$ret = $this->_check_running_interval_time($path . '/group_upgrade.txt', 3600);
			if ($ret) {
				$partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
				if (isset($partner_setting['partner']['group_upgrade']) && $partner_setting['partner']['group_upgrade'] > 0) {
					$filter = array('uniacid' => $_W['uniacid']);
					$orderby = ' ORDER BY `condition` ASC ';
					$group_list = M::t('superman_mall_partner_group')->fetchall($filter, $orderby, 0, -1, 'id');
					if ($group_list) {
						$filter['uid'] = '# >0';
						$filter['status'] = 1;
						$partner_list = M::t('superman_mall_partner')->fetchall($filter, '', 0, -1);
						if ($partner_list) {
							foreach ($partner_list as $li) {
								$count = 0;
								if (!isset($partner_setting['partner']['group_condition']) || $partner_setting['partner']['group_condition'] == 1) {
									$count = $li['order_total'];
								} else {
									if ($partner_setting['partner']['group_condition'] == 2) {
										$filter = array('uniacid' => $_W['uniacid'], 'partner1_id' => $li['id'], 'status' => 4);
										$count = M::t('superman_mall_order')->sum($filter, 'price');
									} else {
										if ($partner_setting['partner']['group_condition'] == 3) {
											$count = $li['downline1'] + $li['downline2'] + $li['downline3'];
										} else {
											if ($partner_setting['partner']['group_condition'] == 4) {
												$count = $li['downline1'];
											} else {
												if ($partner_setting['partner']['group_condition'] == 5) {
													$count = $li['downline2'];
												} else {
													if ($partner_setting['partner']['group_condition'] == 6) {
														$count = $li['downline3'];
													}
												}
											}
										}
									}
								}
								$groupid = 0;
								foreach ($group_list as $k => $group) {
									if ($group['condition'] < $count) {
										$groupid = $k;
									} else {
										break;
									}
								}
								unset($k, $group);
								if ($partner_setting['partner']['group_upgrade'] == 1) {
									M::t('superman_mall_partner')->update(array('groupid' => $groupid), array('id' => $li['id']));
								} else {
									if ($partner_setting['partner']['group_upgrade'] == 2) {
										if ($li['groupid'] == 0) {
											M::t('superman_mall_partner')->update(array('groupid' => $groupid), array('id' => $li['id']));
										} else {
											if ($groupid == 0) {
											} else {
												if ($group_list[$groupid]['condition'] > $group_list[$li['groupid']]['condition']) {
													M::t('superman_mall_partner')->update(array('groupid' => $groupid), array('id' => $li['id']));
												}
											}
										}
									}
								}
							}
							unset($li);
						}
					}
				}
			}
		}
	}
	private function __report()
	{
		global $_W;
		$path = MODULE_ROOT . '/data/' . $_W['uniacid'];
		mkdirs($path);
		if ($this->_check_running_interval_time($path . '/report.txt', 86400)) {
			$siteinfo = M::t('superman_mall_setting')->fetch_value(SUPERMAN_SETTING_SITE);
			$cloud = new SupermanMallCloud($this->module, $siteinfo);
			$cloud->showMessage = false;
			if (!empty($siteinfo)) {
				$result = $cloud->report();
				if ($result['errno'] == 101) {
					$result = $cloud->register();
					if (!empty($result['data']['siteid'])) {
						$data = array('skey' => SUPERMAN_SETTING_SITE, 'svalue' => iserializer($result['data']));
						if (!empty($siteinfo)) {
							M::t('superman_mall_setting')->update($data, array('skey' => SUPERMAN_SETTING_SITE));
						} else {
							M::t('superman_mall_setting')->insert($data);
						}
					}
				}
			} else {
				$result = $cloud->register();
				if (!empty($result['data']['siteid'])) {
					$data = array('skey' => SUPERMAN_SETTING_SITE, 'svalue' => iserializer($result['data']));
					if (!empty($siteinfo)) {
						M::t('superman_mall_setting')->update($data, array('skey' => SUPERMAN_SETTING_SITE));
					} else {
						M::t('superman_mall_setting')->insert($data);
					}
				}
			}
		}
	}
	private function _check_running_interval_time($filename, $interval = 300)
	{
		$name = substr($filename, strrpos($filename, '/') + 1);
		if (empty($filename)) {
			WeUtility::logging('fatal', "[_check_running_interval_time:{$name}] filename is null");
			return false;
		}
		if (!file_exists($filename)) {
			$interval = 0;
		}
		$fp = fopen($filename, "a");
		if (!$fp) {
			WeUtility::logging('fatal', "[_check_running_interval_time:{$name}] fopen failed, filename={$filename}");
			return false;
		}
		if (!flock($fp, LOCK_EX | LOCK_NB)) {
			fclose($fp);
			return false;
		}
		if ($interval > 0) {
			clearstatcache();
			$lasttime = filemtime($filename);
			$diff = TIMESTAMP - $lasttime;
			if ($diff < $interval) {
				if (defined('LOCAL_DEVELOPMENT')) {
				}
				flock($fp, LOCK_UN);
				fclose($fp);
				return false;
			}
		}
		ftruncate($fp, 0);
		rewind($fp);
		$ret = fwrite($fp, 'success');
		if ($ret <= 0) {
			WeUtility::logging('fatal', "[_check_running_interval_time:{$name}] file_put_contents failed(2), ret={$ret}");
			flock($fp, LOCK_UN);
			fclose($fp);
			return false;
		}
		if (defined('LOCAL_DEVELOPMENT')) {
		}
		flock($fp, LOCK_UN);
		fclose($fp);
		return true;
	}
	public function send_partner_invite_tmplmsg($partner, $joiner, $extra)
	{
		global $_W;
		if (!$_W['uniacid']) {
			$_W['uniacid'] = $partner['uniacid'];
		}
		$account = $this->_init_account();
		if ($_W['account']['level'] != 4) {
			WeUtility::logging('fatal', '[send_partner_invite_tmplmsg] 非认证服务号没有模板消息权限, name=' . $_W['account']['name'] . ', level=' . $_W['account']['level']);
			return false;
		}
		if (!$partner['openid']) {
			WeUtility::logging('fatal', '[send_partner_invite_tmplmsg] 非法参数，openid is null');
			return false;
		}
		$setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_PARTNER);
		if (!$setting['invite'] || $setting['invite']['tmpl_id'] == '') {
			WeUtility::logging('fatal', '[send_partner_invite_tmplmsg] 没有配置模板消息');
			return false;
		}
		$joiner_info = mc_fetch($joiner['uid'], array('nickname'));
		$vars = array('{下线昵称}' => $joiner_info['nickname'] ? $joiner_info['nickname'] : $joiner['uid'], '{加入时间}' => date('Y-m-d H:i:s', TIMESTAMP));
		$message = array('template_id' => $setting['invite']['tmpl_id'], 'postdata' => array(), 'url' => $extra['url'], 'topcolor' => '#008000');
		$tmpl_variable = explode("\n", $setting['invite']['tmpl_variable']);
		foreach ($tmpl_variable as $line) {
			$arr = explode("=", trim($line));
			$arr = array_map('trim', $arr);
			$value = $arr[1];
			foreach ($vars as $k => $v) {
				if (strpos($value, $k) !== false) {
					$value = str_replace($k, $v, $value);
				}
			}
			$message['postdata'][$arr[0]] = array('value' => $value, 'color' => '#173177');
		}
		$ret = $account->sendTplNotice($partner['openid'], $message['template_id'], $message['postdata'], $message['url'], $message['topcolor']);
		if ($ret !== true) {
			WeUtility::logging("fatal", "[send_partner_invite_tmplmsg] 模板消息发送失败：openid={$partner['openid']}, ret=" . var_export($ret, true) . ", message=" . var_export($message, true));
			return false;
		}
		if (defined('ONLINE_DEVELOPMENT')) {
			WeUtility::logging("trace", "[send_partner_invite_tmplmsg] 模板消息发送成功：template_id={$message['template_id']}, openid={$partner['openid']}, message=" . var_export($message, true));
		}
		return true;
	}
}