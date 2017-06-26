<?php
if (isset($_GPC['__shopid'])) {
    $_W['__shopid'] = intval($_GPC['__shopid']);
    $shop = pdo_fetch("SELECT * FROM ".tablename('superman_mall_shop')." WHERE id=:id", array(':id' => $_W['__shopid']));
    if ($shop) {
        $_W['uniacid'] = $shop['uniacid'];
    }
    unset($shop);
}
__superman_fix_attachurl();
load()->model('user');
load()->func('tpl');
$_W['token'] = token();
$session = json_decode(base64_decode($_GPC['___shop_session___']), true);
if (is_array($session)) {
    $sql = "SELECT * FROM ".tablename('superman_mall_shop_user')." WHERE id=:id";
    $params = array(
        ':id' => $session['superman_mall']['user_id'],
    );
    $shop_user = pdo_fetch($sql, $params);
    if (is_array($shop_user) && $session['superman_mall']['hash'] == md5($shop_user['password'] . $shop_user['salt'])) {
        $_W['superman_mall']['user_id'] = $shop_user['id'];
        $_W['superman_mall']['username'] = $shop_user['username'];
        $shop_user['currentvisit'] = $shop_user['lastvisit'];
        $shop_user['currentip'] = $shop_user['lastip'];
        $shop_user['lastvisit'] = $session['superman_mall']['lastvisit'];
        $shop_user['lastip'] = $session['superman_mall']['lastip'];
        $_W['superman_mall']['shop_user'] = $shop_user;

        //初始化商户账号组
        if ($shop_user['groupid'] > 0) {
            $sql = "SELECT * FROM ".tablename('superman_mall_shop_user_group')." WHERE id=:id";
            $params = array(
                ':id' => $shop_user['groupid'],
            );
            $_W['superman_mall']['shop_user']['group'] = pdo_fetch($sql, $params);
        }
        //初始化商户
        if ($shop_user['shopid'] > 0) {
            $sql = "SELECT * FROM ".tablename('superman_mall_shop')." WHERE id=:id";
            $params = array(
                ':id' => $shop_user['shopid'],
            );
            $_W['superman_mall']['shop'] = pdo_fetch($sql, $params);
        }
        $_W['__shopid'] = $shop_user['shopid'];
        $_W['uniacid'] = $shop_user['uniacid'];
    } else {
        isetcookie('___shop_session___', false, -100);
    }
    unset($shop_user);
}
unset($session);

if (isset($_GPC['shopid'])) {
    $shopid = intval($_GPC['shopid']);
    $shop = pdo_fetch("SELECT * FROM ".tablename('superman_mall_shop')." WHERE id=:id", array(':id' => $shopid));
    if ($shop) {
        $_W['uniacid'] = $shop['uniacid'];
    }
    unset($shop, $shopid);
} else {
    $url = str_replace("http://", '', $_W['siteroot']);
    $url = str_replace("https://", '', $url);
    $arr = explode(".", $url);
    if (intval($arr[0]) > 0) {
        $uniacid = intval($arr[0]);
        $count = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('uni_account')." WHERE uniacid=:uniacid", array(':uniacid' => $uniacid));
        if ($count > 0) {
            $_W['uniacid'] = $uniacid;
        }
    }
    unset($url, $arr, $count, $uniacid);
}
//$_W['uniacid'] = intval($_GPC['i'])?intval($_GPC['i']):intval($_GPC['__uniacid']);
if (!empty($_W['uniacid'])) {
    $_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
    $_W['acid'] = $_W['account']['acid'];
    $_W['weid'] = $_W['uniacid'];
    isetcookie('__uniacid', $_W['uniacid'], 7 * 86400);
} else {
    exit('非法请求');
}
$_W['template'] = 'default';
load()->func('compat.biz');

function __superman_fix_attachurl() {
    global $_W;
    if (strpos($_W['attachurl'], 'addons/superman_mall/admin') === false) {
        return;
    }
    $_W['attachurl'] = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
}