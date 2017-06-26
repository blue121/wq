<?php
defined('IN_IA') or exit('Access Denied');
isetcookie('___shop_session___', '', -10000);
$sql = "SELECT * FROM ".tablename('superman_mall_kv')." WHERE uniacid=:uniacid AND skey=:skey";
$params = array(
    ':uniacid' => $_W['uniacid'],
    ':skey' => SUPERMAN_SKEY_PLATFORM_SHOP
);
$row = pdo_fetch($sql, $params);
$setting = $row['svalue']?iunserializer($row['svalue']):array();
if ($setting && !empty($setting['web_domain'])) {
    $urls = parse_url($_W['siteroot']);
    $url = ($_SERVER['HTTPS'] == 'on'?'https://':'http://').$_W['uniacid'].'.'.$setting['web_domain'].$urls['path'].'index.php';
} else {
    $url = $_W['siteroot'].'/addons/superman_mall/admin/index.php?shopid='.$_GPC['shopid'];
}
@header('Location: '.$url);