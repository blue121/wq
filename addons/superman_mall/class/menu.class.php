<?php

//decode by  http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
class SuermanMallMenu
{
	public static function coreMenus()
	{
		$menus = array();
		return $menus;
	}
	public static function businessMenus()
	{
		$menus = array(array('title' => '平台设置', 'icon' => 'fa fa-gear', 'url' => wurl('site/entry/platform', array('act' => 'base', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform', 'displayorder' => 0), array('title' => '商户管理', 'icon' => 'fa fa-certificate', 'url' => wurl('site/entry/shop', array('act' => 'display', 'status' => 'all', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'shop', 'displayorder' => 2), array('title' => '财务管理', 'icon' => 'fa fa-yen', 'url' => wurl('site/entry/finance', array('act' => 'apply', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance', 'displayorder' => 3), array('title' => '分销管理', 'icon' => 'fa fa-share-alt', 'url' => wurl('site/entry/partner', array('act' => 'overview', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner', 'displayorder' => 4));
		usort($menus, array(SupermanUtil, 'sort_displayorder_asc'));
		return $menus;
	}
	public static function customMenus()
	{
		$menus = array(array('title' => '功能模块', 'icon' => 'fa fa-puzzle-piece', 'url' => wurl('site/entry/plugin', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'plugin', 'displayorder' => 1), array('title' => '账号管理', 'icon' => 'fa fa-users', 'url' => wurl('site/entry/user', array('act' => 'user', 'op' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'user', 'displayorder' => 2), array('title' => '商品管理', 'icon' => 'fa fa-gift', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'upshelf', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item', 'displayorder' => 10), array('title' => '订单管理', 'icon' => 'fa fa-bars', 'url' => wurl('site/entry/order', array('act' => 'overview', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order', 'displayorder' => 20), array('title' => '评价管理', 'icon' => 'fa fa-comments-o', 'url' => wurl('site/entry/comment', array('act' => 'display', 'status' => -1, 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'comment', 'displayorder' => 30), array('title' => '线下核销', 'icon' => 'fa fa-check-square-o', 'url' => wurl('site/entry/checkout', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'checkout', 'displayorder' => 42), array('title' => '数据统计', 'icon' => 'fa fa-bar-chart-o', 'url' => wurl('site/entry/stat', array('type' => 'item', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'stat', 'displayorder' => 50), array('title' => '触发器规则', 'icon' => 'fa fa-bell', 'url' => wurl('site/entry/trigger', array('act' => 'display', 'isshop' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'trigger', 'displayorder' => 60));
		usort($menus, array(SupermanUtil, 'sort_displayorder_asc'));
		$status = uni_user_permission_exist();
		if (is_error($status)) {
			$permission = uni_user_permission(SUPERMAN_MODULE_NAME);
			if ($permission[0] != 'all') {
				foreach ($menus as $k => $m) {
					if (!in_array(SUPERMAN_MODULE_NAME . '_menu_' . $m['do'], $permission)) {
						unset($menus[$k]);
					}
				}
			}
		}
		return $menus;
	}
	public static function webNavs($user_permission = array())
	{
		global $_W, $_GPC;
		$navs = array(array('title' => '平台设置', 'icon' => 'fa fa-cog', 'active' => '', 'permission' => 'superman_mall_menu_platform', 'items' => array(array('title' => '基本参数', 'url' => wurl('site/entry/platform', array('act' => 'base', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => 'SEO设置', 'url' => wurl('site/entry/platform', array('act' => 'seo', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '分享设置', 'url' => wurl('site/entry/platform', array('act' => 'share', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '联系客服', 'url' => wurl('site/entry/platform', array('act' => 'service', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '幻灯片', 'url' => wurl('site/entry/platform', array('act' => 'slide', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '公告', 'url' => wurl('site/entry/platform', array('act' => 'notice', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '首页导航', 'url' => wurl('site/entry/platform', array('act' => 'nav', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '底部导航', 'url' => wurl('site/entry/platform', array('act' => 'footer_nav', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '短信设置', 'url' => wurl('site/entry/platform', array('act' => 'sms', 'op' => 'setting', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('op'), 'do' => 'platform'), array('title' => '商户设置', 'url' => wurl('site/entry/platform', array('act' => 'shop', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '订单设置', 'url' => wurl('site/entry/platform', array('act' => 'order', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '支付设置', 'url' => wurl('site/entry/platform', array('act' => 'payments', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '首页广告图', 'url' => wurl('site/entry/platform', array('act' => 'homead', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '引导关注', 'url' => wurl('site/entry/platform', array('act' => 'subscribe', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '商品分类', 'url' => wurl('site/entry/category', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/category', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加分类', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'category'), array('title' => '功能管理', 'url' => wurl('site/entry/platform', array('act' => 'plugin', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '消息中心', 'url' => wurl('site/entry/message', array('act' => 'template', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'message'), array('title' => '触发器规则', 'url' => wurl('site/entry/trigger', array('act' => 'display', 'isplatform' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'extra' => array('url' => wurl('site/entry/trigger', array('act' => 'post', 'isplatform' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加规则', 'icon' => 'fa fa-plus'), 'do' => 'trigger'), array('title' => '积分设置', 'url' => wurl('site/entry/platform', array('act' => 'discount', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '快递公司', 'url' => wurl('site/entry/platform', array('act' => 'express', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => '样式设置', 'url' => wurl('site/entry/platform', array('act' => 'style', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'platform'), array('title' => 'diy', 'url' => wurl('site/entry/page', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'page'))), array('title' => '商户管理', 'icon' => 'fa fa-certificate', 'active' => '', 'permission' => 'superman_mall_menu_shop', 'items' => array(array('title' => '全部', 'url' => wurl('site/entry/shop', array('act' => 'display', 'status' => 'all', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/shop', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加商户', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'shop'), array('title' => '待审核', 'url' => wurl('site/entry/shop', array('act' => 'display', 'status' => '0', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'shop'), array('title' => '已审核', 'url' => wurl('site/entry/shop', array('act' => 'display', 'status' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'shop'))), array('title' => '财务管理', 'icon' => 'fa fa-yen', 'active' => '', 'permission' => 'superman_mall_menu_finance', 'items' => array(array('title' => '提现管理', 'url' => wurl('site/entry/finance', array('act' => 'apply', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance'), array('title' => '商户结算', 'url' => wurl('site/entry/finance', array('act' => 'stat', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance'), array('title' => '商户钱包', 'url' => wurl('site/entry/finance', array('act' => 'balance', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance'), array('title' => '对账单下载', 'url' => wurl('site/entry/finance', array('act' => 'statement', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance'))), array('title' => '分销管理', 'icon' => 'fa fa-share-alt', 'active' => '', 'permission' => 'superman_mall_menu_partner', 'items' => array(array('title' => '分销概况', 'url' => wurl('site/entry/partner', array('act' => 'overview', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner'), array('title' => '分销商', 'url' => wurl('site/entry/partner', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner'), array('title' => '分销等级', 'url' => wurl('site/entry/partner', array('act' => 'group', 'op' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/partner', array('act' => 'group', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加分销等级', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'partner'), array('title' => '佣金管理', 'url' => wurl('site/entry/partner', array('act' => 'commission', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner'), array('title' => '提现管理', 'url' => wurl('site/entry/partner', array('act' => 'getcash', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner'), array('title' => '参数设置', 'url' => wurl('site/entry/partner', array('act' => 'setting', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner'), array('title' => '佣金排行', 'url' => wurl('site/entry/partner', array('act' => 'ranking', 'op' => 'display_partner', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner'), array('title' => '分销海报', 'url' => wurl('site/entry/partner', array('act' => 'poster', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'partner'))), array('title' => '功能模块', 'icon' => 'fa fa-cog', 'active' => '', 'permission' => 'superman_mall_menu_plugin', 'items' => array(array('title' => '秒杀', 'url' => wurl('site/entry/seckill', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/seckill', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加秒杀', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'seckill', 'do_prefix' => 'plugin'), array('title' => '拼团', 'url' => wurl('site/entry/mgroupon', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/mgroupon', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加拼团', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'mgroupon', 'do_prefix' => 'plugin'), array('title' => '营销', 'url' => wurl('site/entry/discount', array('act' => 'index', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'discount'), array('title' => '打印机', 'url' => wurl('site/entry/printer', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'printer'), array('title' => '淘宝助手', 'url' => wurl('site/entry/tbast', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'tbast'))), array('title' => '账号管理', 'icon' => 'fa fa-user', 'active' => '', 'permission' => 'superman_mall_menu_user', 'items' => array(array('title' => '账号', 'url' => wurl('site/entry/user', array('act' => 'user', 'op' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/user', array('act' => 'user', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加账号', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'user', 'do_suffix' => 'user'), array('title' => '身份', 'url' => wurl('site/entry/user', array('act' => 'group', 'op' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/user', array('act' => 'group', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加身份', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'user', 'do_suffix' => 'group'))), array('title' => '商品管理', 'icon' => 'fa fa-gift', 'active' => '', 'permission' => 'superman_mall_menu_item', 'items' => array(array('title' => '发布商品', 'url' => wurl('site/entry/item', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '出售中商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'upshelf', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '已售罄商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'stockout', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '仓库中商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'offshelf', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '禁售商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'forbid', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '商品规格', 'url' => wurl('site/entry/spec', array('m' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/spec', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加规格', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'spec', 'do_prefix' => 'item'), array('title' => '邮费模板', 'url' => wurl('site/entry/postage', array('m' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/postage', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加模板', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'postage', 'do_prefix' => 'item'), array('title' => '自提门店', 'url' => wurl('site/entry/myfetch', array('m' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/myfetch', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加自提门店', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'myfetch', 'do_prefix' => 'item'))), array('title' => '订单管理', 'icon' => 'fa fa-bars', 'active' => '', 'permission' => 'superman_mall_menu_order', 'items' => array(array('title' => '订单概况', 'url' => wurl('site/entry/order', array('act' => 'overview', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '全部订单', 'url' => wurl('site/entry/order', array('act' => 'display', 'status' => 'all', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '待支付', 'url' => wurl('site/entry/order', array('act' => 'display', 'status' => '0', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '待发货', 'url' => wurl('site/entry/order', array('act' => 'display', 'status' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '货到付款', 'url' => wurl('site/entry/order', array('act' => 'display', 'pay_type' => '3', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '自提订单', 'url' => wurl('site/entry/order', array('act' => 'display', 'dispatch_type' => '2', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '拼团订单', 'url' => wurl('site/entry/order', array('act' => 'display', 'type' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '售后管理', 'url' => wurl('site/entry/order', array('act' => 'refund', 'service_type' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '快递公司', 'url' => wurl('site/entry/order', array('act' => 'express', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '自定义配送', 'url' => wurl('site/entry/delivery', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'delivery'), array('title' => '批量发货', 'url' => wurl('site/entry/order', array('act' => 'batch', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'))), array('title' => '评价管理', 'icon' => 'fa fa-comments-o', 'active' => '', 'permission' => 'superman_mall_menu_comment', 'items' => array(array('title' => '全部评价', 'url' => wurl('site/entry/comment', array('act' => 'display', 'status' => '-1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'comment'), array('title' => '未审核评价', 'url' => wurl('site/entry/comment', array('act' => 'display', 'status' => '0', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'comment'), array('title' => '已审核评价', 'url' => wurl('site/entry/comment', array('act' => 'display', 'status' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'comment'))), array('title' => '线下核销', 'icon' => 'fa fa-check-square-o', 'active' => '', 'permission' => 'superman_mall_menu_checkout', 'items' => array(array('title' => '核销记录', 'url' => wurl('site/entry/checkout', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'checkout', 'do_suffix' => 'display'), array('title' => '扫码核销', 'url' => wurl('site/entry/checkout', array('act' => 'qrcode', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/checkout', array('act' => 'qrcode', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加核销员', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'checkout', 'do_suffix' => 'qrcode'), array('title' => '自助核销', 'url' => wurl('site/entry/checkout', array('act' => 'oneself', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/checkout', array('act' => 'oneself', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加验证码', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'checkout', 'do_suffix' => 'oneself'))), array('title' => '数据统计', 'icon' => 'fa fa-bar-chart-o', 'active' => '', 'permission' => 'superman_mall_menu_stat', 'items' => array(array('title' => '商品数据', 'url' => wurl('site/entry/stat', array('type' => 'item', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'stat'))), array('title' => '触发器规则', 'icon' => 'fa fa-bell', 'active' => '', 'permission' => 'superman_mall_menu_trigger', 'items' => array(array('title' => '规则列表', 'url' => wurl('site/entry/trigger', array('act' => 'display', 'isshop' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/trigger', array('act' => 'post', 'isshop' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加规则', 'icon' => 'fa fa-plus'), 'do' => 'trigger'))));
		foreach ($navs as $k => &$nav) {
			if (is_array($user_permission)) {
				$no_permission = true;
				foreach ($user_permission as $p) {
					if ($nav['permission'] == $p || strexists($p, $nav['permission'])) {
						$no_permission = false;
						break;
					}
				}
				if ($no_permission) {
					unset($navs[$k]);
					continue;
				}
			}
			foreach ($nav['items'] as $n => &$item) {
				if (is_array($user_permission)) {
					$item_permission = 'superman_mall_menu_';
					if (isset($item['do_prefix'])) {
						$item_permission .= $item['do_prefix'] . '_';
					}
					$item_permission .= $item['do'];
					if (isset($item['do_suffix'])) {
						$item_permission .= '_' . $item['do_suffix'];
					}
					$has_permission = false;
					foreach ($user_permission as $p) {
						if ($item_permission == $p || strexists($item_permission, $p)) {
							$has_permission = true;
							break;
						}
					}
					if (!$has_permission) {
						unset($nav['items'][$n]);
						continue;
					}
				}
				if (!defined('LOCAL_DEVELOPMENT')) {
					if ($item['title'] == 'diy') {
						unset($nav['items'][$n]);
						continue;
					}
				}
			}
			$nav['dos'] = array();
			foreach ($nav['items'] as $v) {
				if (!in_array($v['do'], $nav['dos'])) {
					$nav['dos'][] = $v['do'];
				}
			}
			sort($nav['dos']);
			$nav['xdo'] = md5(implode(',', $nav['dos']));
			foreach ($nav['items'] as &$item) {
				$query = parse_url($item['url'], PHP_URL_QUERY);
				parse_str($query, $urls);
				$query = parse_url($_W['siteurl'], PHP_URL_QUERY);
				parse_str($query, $get);
				if (isset($item['excude_params']) && $item['excude_params']) {
					foreach ($item['excude_params'] as $p) {
						if (isset($urls[$p])) {
							unset($urls[$p]);
						}
						if (isset($get[$p])) {
							unset($get[$p]);
						}
					}
				}
				$diff = array_diff_assoc($urls, $get);
				if (isset($get['do']) && in_array($get['do'], $nav['dos']) || isset($_GPC['__nav_' . $nav['xdo']]) && $_GPC['__nav_' . $nav['xdo']]) {
					$nav['active'] = 'in';
				}
				if (empty($diff)) {
					$item['active'] = 'active';
					break;
				}
			}
			if (isset($_GPC['superman']) && $_GPC['superman'] == 'yes' && $nav['permission'] == 'superman_mall_menu_platform') {
				$nav['items'][] = array('title' => '工具', 'url' => wurl('site/entry/tools', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'tools');
			}
		}
		return $navs;
	}
	public static function webShopNavs($user_permission = array())
	{
		global $_W, $_GPC;
		if (!defined('IN_SUPERMAN_MALL_ADMIN')) {
			return array();
		}
		$navs = array(array('title' => '基本设置', 'icon' => 'fa fa-cog', 'active' => 'in', 'permission' => 'superman_mall_menu_setting', 'items' => array(array('title' => '联系客服', 'url' => wurl('site/entry/setting', array('act' => 'service', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'setting'), array('title' => '幻灯片', 'url' => wurl('site/entry/setting', array('act' => 'slide', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'setting'))), array('title' => '功能模块', 'icon' => 'fa fa-cog', 'active' => '', 'permission' => 'superman_mall_menu_plugin', 'items' => array(array('title' => '秒杀', 'url' => wurl('site/entry/seckill', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/seckill', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加秒杀', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'seckill', 'do_prefix' => 'plugin'), array('title' => '拼团', 'url' => wurl('site/entry/mgroupon', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/mgroupon', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加拼团', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'mgroupon', 'do_prefix' => 'plugin'), array('title' => '营销', 'url' => wurl('site/entry/discount', array('act' => 'index', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'discount'), array('title' => '打印机', 'url' => wurl('site/entry/printer', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'printer'), array('title' => '淘宝助手', 'url' => wurl('site/entry/tbast', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act'), 'do' => 'tbast'))), array('title' => '账号管理', 'icon' => 'fa fa-user', 'active' => '', 'permission' => 'superman_mall_menu_user', 'items' => array(array('title' => '账号', 'url' => wurl('site/entry/user', array('act' => 'user', 'op' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/user', array('act' => 'user', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加账号', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'user', 'do_suffix' => 'user'), array('title' => '身份', 'url' => wurl('site/entry/user', array('act' => 'group', 'op' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/user', array('act' => 'group', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加身份', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'user', 'do_suffix' => 'group'))), array('title' => '商品管理', 'icon' => 'fa fa-gift', 'active' => '', 'permission' => 'superman_mall_menu_item', 'items' => array(array('title' => '发布商品', 'url' => wurl('site/entry/item', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '出售中商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'upshelf', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '已售罄商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'stockout', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '仓库中商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'offshelf', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '禁售商品', 'url' => wurl('site/entry/item', array('act' => 'display', 'status' => 'forbid', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'item'), array('title' => '商品规格', 'url' => wurl('site/entry/spec', array('m' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/spec', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加规格', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'spec', 'do_prefix' => 'item'), array('title' => '邮费模板', 'url' => wurl('site/entry/postage', array('m' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/postage', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加模板', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'postage', 'do_prefix' => 'item'), array('title' => '自提门店', 'url' => wurl('site/entry/myfetch', array('m' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/myfetch', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加自提门店', 'icon' => 'fa fa-plus'), 'excude_params' => array('act'), 'do' => 'myfetch', 'do_prefix' => 'item'))), array('title' => '订单管理', 'icon' => 'fa fa-bars', 'active' => '', 'permission' => 'superman_mall_menu_order', 'items' => array(array('title' => '订单概况', 'url' => wurl('site/entry/order', array('act' => 'overview', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '全部订单', 'url' => wurl('site/entry/order', array('act' => 'display', 'status' => 'all', 'm' => SUPERMAN_MODULE_NAME)), 'excude_params' => array('act', 'dispatch_type'), 'do' => 'order'), array('title' => '待支付', 'url' => wurl('site/entry/order', array('act' => 'display', 'status' => '0', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '待发货', 'url' => wurl('site/entry/order', array('act' => 'display', 'status' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '货到付款', 'url' => wurl('site/entry/order', array('act' => 'display', 'pay_type' => '3', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '自提订单', 'url' => wurl('site/entry/order', array('act' => 'display', 'dispatch_type' => '2', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '拼团订单', 'url' => wurl('site/entry/order', array('act' => 'display', 'type' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '售后管理', 'url' => wurl('site/entry/order', array('act' => 'refund', 'service_type' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '快递公司', 'url' => wurl('site/entry/order', array('act' => 'express', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'), array('title' => '自定义配送', 'url' => wurl('site/entry/delivery', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'delivery'), array('title' => '批量发货', 'url' => wurl('site/entry/order', array('act' => 'batch', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'order'))), array('title' => '评价管理', 'icon' => 'fa fa-comments-o', 'active' => '', 'permission' => 'superman_mall_menu_comment', 'items' => array(array('title' => '全部评价', 'url' => wurl('site/entry/comment', array('act' => 'display', 'status' => '-1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'comment'), array('title' => '未审核评价', 'url' => wurl('site/entry/comment', array('act' => 'display', 'status' => '0', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'comment'), array('title' => '已审核评价', 'url' => wurl('site/entry/comment', array('act' => 'display', 'status' => '1', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'comment'))), array('title' => '线下核销', 'icon' => 'fa fa-check-square-o', 'active' => '', 'permission' => 'superman_mall_menu_checkout', 'items' => array(array('title' => '核销记录', 'url' => wurl('site/entry/checkout', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'checkout', 'do_suffix' => 'display'), array('title' => '扫码核销', 'url' => wurl('site/entry/checkout', array('act' => 'qrcode', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/checkout', array('act' => 'qrcode', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加核销员', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'checkout', 'do_suffix' => 'qrcode'), array('title' => '自助核销', 'url' => wurl('site/entry/checkout', array('act' => 'oneself', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/checkout', array('act' => 'oneself', 'op' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加验证码', 'icon' => 'fa fa-plus'), 'excude_params' => array('op'), 'do' => 'checkout', 'do_suffix' => 'oneself'))), array('title' => '数据统计', 'icon' => 'fa fa-bar-chart-o', 'active' => '', 'permission' => 'superman_mall_menu_stat', 'items' => array(array('title' => '商品数据', 'url' => wurl('site/entry/stat', array('type' => 'item', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'stat'))), array('title' => '财务管理', 'icon' => 'fa fa-yen', 'active' => '', 'permission' => 'superman_mall_menu_finance', 'items' => array(array('title' => '申请提现', 'url' => wurl('site/entry/finance', array('act' => 'apply', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance'), array('title' => '提现记录', 'url' => wurl('site/entry/finance', array('act' => 'log', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance'), array('title' => '提现账户', 'url' => wurl('site/entry/finance', array('act' => 'user', 'm' => SUPERMAN_MODULE_NAME)), 'do' => 'finance'))), array('title' => '触发器规则', 'icon' => 'fa fa-bell', 'active' => '', 'permission' => 'superman_mall_menu_trigger', 'items' => array(array('title' => '规则列表', 'url' => wurl('site/entry/trigger', array('act' => 'display', 'm' => SUPERMAN_MODULE_NAME)), 'extra' => array('url' => wurl('site/entry/trigger', array('act' => 'post', 'm' => SUPERMAN_MODULE_NAME)), 'title' => '添加规则', 'icon' => 'fa fa-plus'), 'do' => 'trigger'))));
		foreach ($navs as $k => &$nav) {
			if (is_array($user_permission)) {
				$no_permission = true;
				foreach ($user_permission as $p) {
					if ($nav['permission'] == $p || strexists($p, $nav['permission'])) {
						$no_permission = false;
						break;
					}
				}
				if ($no_permission) {
					unset($navs[$k]);
					continue;
				}
			}
			foreach ($nav['items'] as $n => &$item) {
				if (is_array($user_permission)) {
					$item_permission = 'superman_mall_menu_';
					if (isset($item['do_prefix'])) {
						$item_permission .= $item['do_prefix'] . '_';
					}
					$item_permission .= $item['do'];
					if (isset($item['do_suffix'])) {
						$item_permission .= '_' . $item['do_suffix'];
					}
					$has_permission = false;
					foreach ($user_permission as $p) {
						if ($item_permission == $p || strexists($p, $item_permission)) {
							$has_permission = true;
							break;
						}
					}
					if (!$has_permission) {
						unset($nav['items'][$n]);
						continue;
					}
				}
			}
			$nav['dos'] = array();
			foreach ($nav['items'] as $v) {
				if (!in_array($v['do'], $nav['dos'])) {
					$nav['dos'][] = $v['do'];
				}
			}
			sort($nav['dos']);
			$nav['xdo'] = md5(implode(',', $nav['dos']));
			foreach ($nav['items'] as &$item) {
				$query = parse_url($item['url'], PHP_URL_QUERY);
				parse_str($query, $urls);
				$query = parse_url($_W['siteurl'], PHP_URL_QUERY);
				parse_str($query, $get);
				if (isset($item['excude_params']) && $item['excude_params']) {
					foreach ($item['excude_params'] as $p) {
						if (isset($urls[$p])) {
							unset($urls[$p]);
						}
						if (isset($get[$p])) {
							unset($get[$p]);
						}
					}
				}
				$diff = array_diff_assoc($urls, $get);
				if (isset($get['do']) && in_array($get['do'], $nav['dos']) || isset($_GPC['__nav_' . $nav['xdo']]) && $_GPC['__nav_' . $nav['xdo']]) {
					$nav['active'] = 'in';
				}
				if (empty($diff)) {
					$item['active'] = 'active';
					break;
				}
			}
		}
		return $navs;
	}
}