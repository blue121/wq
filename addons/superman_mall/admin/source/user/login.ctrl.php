<?php
defined('IN_IA') or exit('Access Denied');
define('IN_GW', true);
if ($_GPC['do'] == 'qrcode') {
    //清空所有过期数据
    _delete_stale_data();
    //初始化数据
    _init_qrcode();
}
if ($_GPC['do'] == 'check_qrcode') {
    //验证id是否合法
    $id = intval($_GPC['id']);
    $_t = intval($_GPC['_t']);
    if ($id <= 0 || $_t < TIMESTAMP-300) {
        echo json_encode(array(
            'errno' => 1,
            'errmsg' => '非法请求'
        ));
        exit;
    }
    require IA_ROOT.'/addons/superman_mall/class/util.class.php';
    $sign = SupermanUtil::get_sign(array('id' => $id, '_t' => $_t), $_W['config']['setting']['authkey']);
    if ($sign != $_GPC['sign']) {
        echo json_encode(array(
            'errno' => 2,
            'errmsg' => '非法请求'
        ));
        exit;
    }
    $sql = "SELECT * FROM ".tablename('superman_mall_openid')." WHERE id=:id AND status=:status AND dateline>:dateline";
    $params = array(
        ':id' => $id,
        ':status' => 1,
        ':dateline' => TIMESTAMP-300
    );
    $row = pdo_fetch($sql, $params);
    if (!$row) {
        echo json_encode(array(
            'errno' => 3,
            'errmsg' => '未被扫描'
        ));
        exit;
    }

    $sql = "SELECT * FROM ".tablename('superman_mall_shop_user')." WHERE openid=:openid";
    $params = array(
        ':openid' => $row['openid'],
    );
    $list = pdo_fetchall($sql, $params);
    if (!$list) {
        echo json_encode(array(
            'errno' => 4,
            'errmsg' => '该微信未绑定帐号或帐号已禁用'
        ));
    } else {
        $params = array(
            'id' => $id,
            '_t' => TIMESTAMP,
        );
        $params['sign'] = SupermanUtil::get_sign($params, $_W['config']['setting']['authkey']);
        $params['shopid'] = $_GPC['shopid'];
        echo json_encode(array(
            'errno' => 0,
            'errmsg' => 'OK',
            'url' => wurl('user/auth/select_account', $params)
        ));
    }
    exit;
}
//已登录时进入登录页自动登录
if ($_W['superman_mall'] && $_W['uniacid'] == $_W['superman_mall']['shop_user']['uniacid']) {
    message("欢迎回来，{$_W['superman_mall']['shop_user']['username']}！", url('site/entry/dashboard', array('m' => 'superman_mall')), 'success');
}
if (checksubmit()) {
	_login($_GPC['referer']);
}
template('user/login');

function _login($forward = '') {
	global $_GPC, $_W;
	$username = trim($_GPC['username']);
	$password = trim($_GPC['password']);
    $verify = trim($_GPC['verify']);
    if(empty($username)) {
        message('请输入账户名！', '', 'error');
    }
    if(empty($password)) {
        message('请输入密码！', '', 'error');
    }
    if(empty($verify)) {
        message('请输入验证码！', '', 'error');
    }
    $result = checkcaptcha($verify);
    if (empty($result)) {
        message('验证码错误，请重新输入！', '', 'error');
    }
    $record = __user_single($username, $password);
	if (!empty($record)) {
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
		//message("欢迎回来，{$record['username']}！", '', 'success');
	} else {
		message('登录失败！', '', 'error');
	}
}

function __user_single($username, $password) {
    global $_W;
    $sql = "SELECT * FROM ".tablename('superman_mall_shop_user')." WHERE uniacid=:uniacid AND username=:username";
    $params = array(
        ':uniacid' => $_W['uniacid'],
//        ':shopid' => $_W['__shopid'],
        ':username' => $username,
    );
    $record = pdo_fetch($sql, $params);
    if (empty($record)) {
        return false;
    }
    $password = user_hash($password, $record['salt']);
    if ($password != $record['password']) {
        return false;
    }
    return $record;
}

function _delete_stale_data() {
    $sql = "DELETE FROM ".tablename('superman_mall_openid')." WHERE `dateline`<:dateline";
    pdo_query($sql, array(
        ':dateline' => TIMESTAMP-600
    ));
}

function _init_qrcode() {
    global $_W, $_GPC;
    pdo_insert('superman_mall_openid', array(
        'dateline' => TIMESTAMP,
        'status' => 0
    ));
    $id = pdo_insertid();
    $params = array(
        'act' => 'login_qrcode',
        'id' => $id,
        't' => TIMESTAMP,
    );
    require IA_ROOT.'/addons/superman_mall/class/util.class.php';
    $params['sign'] = SupermanUtil::get_sign($params, $_W['config']['setting']['authkey']);
    $params['m'] = 'superman_mall';

    $siteroot = _get_siteroot();
    //手机访问地址
    $login_url = $siteroot.'app/'.murl('entry//openid', $params);
    //将访问地址生成二维码(添加时间戳防止图片不刷新)
    $qrcode_url = wurl('site/entry', array('m' => 'superman_mall', 'do' => 'qrcode', 'direct' => 1, 'content' => $login_url, '_t' => TIMESTAMP, 'shopid' => $_GPC['shopid']));
    $arr = array(
        'qrcode' => $qrcode_url,
        'id' => $id,
        '_t' => TIMESTAMP,
        'sign' => SupermanUtil::get_sign(array(
            'id' => $id,
            '_t' => TIMESTAMP
        ), $_W['config']['setting']['authkey']),
    );
    echo json_encode($arr);
    exit();
}
function _get_siteroot() {
    global $_GPC, $_W;
    $siteroot = $_W['siteroot'];
    $filename = IA_ROOT . '/addons/superman_mall/data/siteroot.txt';
    if (file_exists($filename)) {
        $siteroot = file_get_contents($filename);
    }
    return $siteroot;
}