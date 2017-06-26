<?php
define('IN_SYS', true);
define('IN_SUPERMAN_MALL_ADMIN', true);
require '../../../framework/bootstrap.inc.php';
__superman_fix_siteroot();
require IA_ROOT.'/addons/superman_mall/const.php';
require MODULE_ROOT.'/admin/common/bootstrap.sys.inc.php';
require MODULE_ROOT.'/admin/common/common.func.php';
require MODULE_ROOT.'/admin/common/template.func.php';
require MODULE_ROOT.'/admin/common/tpl.func.php';
$acl = array(
    'user' => array(
        'default' => 'login',
        'direct' => array(
            'login',
            'register',
            'logout',
            'auth',
        ),
    ),
);
if (($_W['setting']['copyright']['status'] == 1) && empty($_W['isfounder']) && $controller != 'cloud' && $controller != 'utility') {
    $_W['siteclose'] = true;
    if ($controller == 'user' && $action == 'login') {
        if (checksubmit()) {
            require _forward($controller, $action);
        }
        template('user/login');
        exit;
    }
    isetcookie('___shop_session___', '', -10000);
    message('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason'], '', 'info');
}
$controllers = array();
$handle = opendir(MODULE_ROOT . '/admin/source/');
if(!empty($handle)) {
    while($dir = readdir($handle)) {
        if($dir != '.' && $dir != '..') {
            $controllers[] = $dir;
        }
    }
}
if(!in_array($controller, $controllers)) {
    $controller = 'user';
}
$init = MODULE_ROOT . "/admin/source/{$controller}/__init.php";
if(is_file($init)) {
    require $init;
}
$actions = array();
$handle = opendir(MODULE_ROOT . '/admin/source/' . $controller);
if(!empty($handle)) {
    while($dir = readdir($handle)) {
        if($dir != '.' && $dir != '..' && strexists($dir, '.ctrl.php')) {
            $dir = str_replace('.ctrl.php', '', $dir);
            $actions[] = $dir;
        }
    }
}
/*if(empty($actions)) {
    header('location: ?refresh');
}*/
if(!in_array($action, $actions)) {
    $action = $acl[$controller]['default'];
}
if(!in_array($action, $actions)) {
    $action = $actions[0];
}
if ($controller == 'user' && $action == 'login') {
//    if (empty($_W['__shopid'])) {
//        message('非法参数！', '', 'error');
//    }
}
if(is_array($acl[$controller]['direct']) && in_array($action, $acl[$controller]['direct'])) {
    require _forward($controller, $action);
    exit;
}
if(is_array($acl[$controller]['founder']) && in_array($action, $acl[$controller]['founder'])) {
    if(!$_W['isfounder']) {
        message('没有访问权限！', '', 'error');
    }
}
if ($_GPC['do'] != 'qrcode') {
    checklogin();
}
if (empty($_W['uniacid'])) {
    message('非法参数！', '', 'error');
}
require _forward($controller, $action);

function _forward($c, $a) {
    $file = MODULE_ROOT . '/admin/source/' . $c . '/' . $a . '.ctrl.php';
    return $file;
}

function __superman_fix_siteroot() {
    global $_W, $controller, $action;
    $urls = parse_url($_W['siteroot']);
    $arr = explode('/', $urls['path']);
    do {
        $val = array_pop($arr);
    } while ($val != 'addons');
    $path = implode('/', $arr);
    if(substr($path, -1) != '/') {
        $path .= '/';
    }
    $urls['path'] = $path;
    $_W['siteroot'] = $urls['scheme'].'://'.$urls['host'].((!empty($urls['port']) && $urls['port']!='80') ? ':'.$urls['port'] : '').$urls['path'];
    $_W['siteurl'] = $urls['scheme'].'://'.$urls['host'].((!empty($urls['port']) && $urls['port']!='80') ? ':'.$urls['port'] : '') . $_W['script_name'] . (empty($_SERVER['QUERY_STRING'])?'':'?') . $_SERVER['QUERY_STRING'];
}