<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
global $_W;
defined('IN_IA') or exit('Access Denied');
define('SUPERMAN_MODULE_NAME', 'superman_mall');
if (!defined('MODULE_ROOT')) {
    define('MODULE_ROOT', dirname(__FILE__));
}
if (!defined('MODULE_URL')) {
    define('MODULE_URL', $_W['siteroot'].'addons/'.SUPERMAN_MODULE_NAME.'/');
}
if (defined('LOCAL_DEVELOPMENT') || defined('ONLINE_DEVELOPMENT')) {
    define('SUPERMAN_DEVELOPMENT', true);
}
define('SUPERMAN_CREATED_ORDER_PRINT', 1); //下单打印
define('SUPERMAN_PAYED_ORDER_PRINT', 2); //支付打印
define('SUPERMAN_ORDERSN_PREFIX', 'S'); //订单号前缀
define('SUPERMAN_FONT_MSYH', MODULE_ROOT.'/data/font/msyh.ttf');
if (is_dir(IA_ROOT.'/addons/bm_payu')) {
    define('SUPERMAN_CONNECT_BMPAYU', true);
}
//superman_mall_kv: skey
define('SUPERMAN_SKEY_PLATFORM_SHARE', 'platform_share');
define('SUPERMAN_SKEY_HOME_APPS', 'home_apps');
define('SUPERMAN_SKEY_FOOTER_NAV', 'footer_nav');
define('SUPERMAN_SKEY_HOME_SLIDE', 'home_slide');
define('SUPERMAN_SKEY_HOME_TOP', 'home_top');
define('SUPERMAN_SKEY_HOME_AD', 'home_ad');
define('SUPERMAN_SKEY_PLATFORM_SUBSCRIBE', 'platform_subscribe');
define('SUPERMAN_SKEY_PLATFORM_PLUGIN', 'platform_plugin');
define('SUPERMAN_SKEY_PLATFORM_GETCASH', 'platform_getcash');
define('SUPERMAN_SKEY_PLATFORM_PAYCERT', 'platform_paycert');
define('SUPERMAN_SKEY_PLATFORM_PAYMENTS', 'platform_payments');
define('SUPERMAN_SKEY_PLATFORM_SHOP', 'platform_shop');
define('SUPERMAN_SKEY_PLATFORM_SMS', 'platform_sms');
define('SUPERMAN_SKEY_PLATFORM_ORDER', 'platform_order');
define('SUPERMAN_SKEY_PLATFORM_DISCOUNT', 'platform_discount');
define('SUPERMAN_SKEY_PLATFORM_STYLE', 'platform_style');
define('SUPERMAN_SKEY_SMS_SETTING', 'sms_setting');
define('SUPERMAN_SKEY_SMS_ACCOUNT', 'sms_account');
define('SUPERMAN_SKEY_MESSAGE_ORDER', 'message_order');
define('SUPERMAN_SKEY_MESSAGE_SHOP', 'message_shop');
define('SUPERMAN_SKEY_MESSAGE_PARTNER', 'message_partner');
define('SUPERMAN_SKEY_MESSAGE_TRIGGER', 'message_trigger');
define('SUPERMAN_SKEY_MESSAGE_MGROUPON', 'message_mgroupon');
define('SUPERMAN_SKEY_PARTNER_SETTING', 'partner_setting');
define('SUPERMAN_SKEY_MGROUPON_SETTING', 'mgroupon_setting:%s'); //mgroupon_setting:<shopid>
define('SUPERMAN_SKEY_MYFETCH_SETTING', 'myfetch_setting:%s'); //myfetch_setting:<shopid>
define('SUPERMAN_SKEY_SHOP_PLUGIN', 'shop_plugin:%s'); //shop_plugin:<shopid>
define('SUPERMAN_SKEY_SHOP_DISCOUNT', 'shop_discount:%s');//shop_discount:<shopid>
define('SUPERMAN_SKEY_SHOP_SERVICE', 'shop_service:%s');//shop_service:<shopid>
define('SUPERMAN_SKEY_SHOP_SLIDE', 'shop_slide:%s');//shop_slide:<shopid>
define('SUPERMAN_SETTING_SITE', 'site');

//regular
define('SUPERMAN_REGULAR_EMAIL', '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i');
define('SUPERMAN_REGULAR_MOBILE', '/1\d{10}/');
define('SUPERMAN_REGULAR_USERNAME', '/^[a-z\d_]{4,16}$/i');
define('SUPERMAN_REGULAR_PASSWORD', '/^\w{6,16}$/i');

//temp config
define('SUPERMAN_CONFIG_SECKILL_HISTORY', 0); //是否显示已结束的秒杀数据
define('SUPERMAN_CONFIG_SCROLL_MEMORY', 1); //是否开启浏览滚动条位置记忆