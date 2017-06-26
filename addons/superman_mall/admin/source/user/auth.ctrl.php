<?php
defined('IN_IA') or exit('Access Denied');
require IA_ROOT.'/addons/superman_mall/class/util.class.php';
if ($_GPC['do'] == 'select_account') {
    $arr = array(
        'id' => $_GPC['id'],
        '_t' => $_GPC['_t'],
    );
    if (!$arr['id']) {
        message('非法请求', '', 'error');
    }
    $sign = SupermanUtil::get_sign($arr, $_W['config']['setting']['authkey']);
    if ($sign != $_GPC['sign']) {
        message('非法请求', referer(), 'error');
    }

    $row = pdo_get('superman_mall_openid', array('id' => $arr['id'], 'status' => 1));
    if (!$row || $row['dateline'] < TIMESTAMP - 600) {  //10分钟内可用
        $url = _superman_get_shop_url();
        message('登录超时', $url, 'error');
    }
    $sql = "SELECT * FROM ".tablename('superman_mall_shop_user')." WHERE uniacid=:uniacid AND openid=:openid";
    $params = array(
        ':uniacid' => $_W['uniacid'],
        ':openid' => $row['openid'],
    );
    $list = pdo_fetchall($sql, $params);
    if ($list) {
        foreach ($list as &$li) {
            $li['shop'] = pdo_get('superman_mall_shop', array('id' => $li['shopid']));
        }
        unset($li);
    }
    if (checksubmit()) {
        _superman_do_auth($list);
    }
} else if ($_GPC['do'] == 'check') {
    $arr = array(
        'c' => $_GPC['c'],
        'a' => $_GPC['a'],
        'do' => $_GPC['do'],
        'id' => $_GPC['id'],
    );
    if (!$arr['id']) {
        message('非法请求', '', 'error');
    }
    $sign = SupermanUtil::get_sign($arr, $_W['config']['setting']['authkey']);
    if ($sign != $_GPC['sign']) {
        message('非法请求', referer(), 'error');
    }
    $sql = "SELECT * FROM ".tablename('superman_mall_shop_user')." WHERE uniacid=:uniacid AND openid=:openid";
    $params = array(
        ':uniacid' => $_W['uniacid'],
        ':openid' => $_GPC['id'],
    );
    $list = pdo_fetchall($sql, $params);
    if ($list) {
        foreach ($list as &$li) {
            $li['shop'] = pdo_get('superman_mall_shop', array('id' => $li['shopid']));
        }
        unset($li);
    }
    if (checksubmit()) {
        _superman_do_auth($list);
    }
}
function _superman_do_auth($list) {
    global $_W, $_GPC;
    $key = $_GPC['key'];
    if (!isset($list[$key])) {
        message('非法请求', '', 'error');
    }
    $record = $list[$key];
    if ($record['status'] != 1) {
        message('当前账号正在审核或已禁用，请联系管理员！', '', 'error');
    }
    if ($record['starttime'] > TIMESTAMP) {
        message('当前账号未启用，请联系管理员！', '', 'error');
    }
    if ($record['expiretime'] > 0 && $record['expiretime'] < TIMESTAMP) {
        message('当前账号已过期，请联系管理员！', '', 'error');
    }
    if (empty($record['shopid'])) {
        message('您的账号未申请商户入驻，请申请商户入驻！', '', 'info');
    }
    $sql = "SELECT * FROM ".tablename('superman_mall_shop')." WHERE id=:id";
    $shop = pdo_fetch($sql, array('id' => $record['shopid']));
    if (empty($shop)) {
        message('商户信息不存在或已删除，请重新申请商户入驻！', '', 'error');
    }
    if (!empty($_W['siteclose'])) {
        message('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason'], '', 'info');
    }
    $cookie = array();
    $cookie['superman_mall']['user_id'] = $record['id'];
    $cookie['superman_mall']['lastvisit'] = $record['logindate'];
    $cookie['superman_mall']['lastip'] = $record['lastip'];
    $cookie['superman_mall']['hash'] = md5($record['password'] . $record['salt']);
    $session = base64_encode(json_encode($cookie));
    isetcookie('___shop_session___', $session, !empty($_GPC['rember']) ? 7 * 86400 : 0, true);
    isetcookie('__uniacid', $_W['uniacid'], 7 * 86400);
    isetcookie('__shopid', $record['shopid'], 7 * 86400);
    $data = array(
        'lastvisit' => TIMESTAMP,
        'lastip' => CLIENT_IP,
    );
    pdo_update('superman_mall_shop_user', $data, array('id' => $record['id']));
    message("欢迎回来，{$record['username']}！", url('site/entry/dashboard', array('m' => 'superman_mall')), 'success');
}
function _superman_get_shop_url (){
    global $_GPC, $_W;
    $sql = "SELECT * FROM " . tablename('superman_mall_kv') . " WHERE uniacid=:uniacid AND skey=:skey";
    $params = array(
        ':uniacid' => $_W['uniacid'],
        ':skey' => SUPERMAN_SKEY_PLATFORM_SHOP
    );
    $row = pdo_fetch($sql, $params);
    $setting = $row['svalue'] ? iunserializer($row['svalue']) : array();
    if ($setting && !empty($setting['web_domain'])) {
        $urls = parse_url($_W['siteroot']);
        $url = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_W['uniacid'] . '.' . $setting['web_domain'] . $urls['path'];
    } else {
        $url = $_W['siteroot'] . 'addons/superman_mall/admin/index.php?shopid=' . $_GPC['shopid'];
    }
    return $url;
}
template('user/auth');