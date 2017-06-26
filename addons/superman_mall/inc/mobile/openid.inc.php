<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileOpenid extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $act = in_array($_GPC['act'], array('checkout_user', 'shop_user', 'login_qrcode'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $this->message('非法请求！', '', 'error');
        } else if ($act == 'checkout_user') {
            $this->checkauth();
            if (!$_W['fans']['follow']) {
                $this->message('请先关注公众号！', $_W['account']['subscribeurl'], 'error');
            } else {
                $params = array(
                    'act' => $_GPC['act'],
                    'shopid' => $_GPC['shopid'],
                    't' => $_GPC['t'],
                );
                $sign = SupermanUtil::get_sign($params, $_W['config']['setting']['authkey']);
                if ($_GPC['sign'] != $sign) {
                    $this->message('非法参数！', '', 'error');
                }
                $shopid = intval($_GPC['shopid']);
                if (!$shopid) {
                    $this->message('非法请求！', '', 'error');
                }
                $timestamp = $_GPC['t'];
                if (TIMESTAMP - $timestamp > 300) { //5分钟有效期
                    $this->message('二维码已过期，请刷新后重新扫描！', '', 'info');
                }
                $shop = M::t('superman_mall_shop')->fetch($shopid);
                if (!$shop) {
                    $this->message('参数错误，请重新扫描！', '', 'error');
                }
                $user = M::t('superman_mall_checkout_user')->fetch(array(
                    'shopid' => $shopid,
                    'uid' => $_W['member']['uid'],
                ));
                if ($user) {
                    $this->message('您已绑定该商户核销员！', $this->createMobileUrl('home'), 'info');
                }
                if (checksubmit()) {
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $_GPC['shopid'],
                        'uid' => $_W['member']['uid'],
                        'openid' => $_W['fans']['openid'],
                    );
                    M::t('superman_mall_checkout_user')->insert($data);
                    $this->message('绑定成功，请联系管理员确认！', '', 'success');
                }
            }
        } else if ($act == 'shop_user') {
            $this->checkauth();
            if (!$_W['fans']['follow']) {
                $this->message('请先关注公众号！', $_W['account']['subscribeurl'], 'error');
            } else {
                $params = array(
                    'act' => $_GPC['act'],
                    'id' => $_GPC['id'],
                    't' => $_GPC['t'],
                );
                $sign = SupermanUtil::get_sign($params, $_W['config']['setting']['authkey']);
                if ($_GPC['sign'] != $sign) {
                    $this->message('非法参数！', '', 'error');
                }
                $id = intval($_GPC['id']);
                if (!$id) {
                    $this->message('非法请求！', '', 'error');
                }
                $timestamp = $_GPC['t'];
                if (TIMESTAMP - $timestamp > 300) { //5分钟有效期
                    $this->message('二维码已过期，请刷新后重新扫描！', '', 'info');
                }
                $user = M::t('superman_mall_shop_user')->fetch($id);
                if (!$user) {
                    $this->message('参数错误，请重新扫描！', '', 'error');
                }
                $shop = M::t('superman_mall_shop')->fetch($user['shopid']);
                if (!$shop) {
                    $this->message('商户不存在或已删除！', '', 'error');
                }
                //检查是否绑定了其它账号
//                $isexist = M::t('superman_mall_shop_user')->fetch(array(
//                    'openid' => $_W['fans']['openid'],
//                    'shopid' => '#!='.$user['shopid'],
//                ));
//                if ($isexist) {
//                    M::t('superman_mall_shop_user')->update(array('binding_flag' => -1), array('id' => $id));
//                    $this->message('绑定失败，该微信号已绑定其它商户账号！', '', 'error');
//                }
                if (checksubmit()) {
                    $data = array(
                        'openid' => $_W['fans']['openid'],
                        'binding_flag' => 1,
                    );
                    M::t('superman_mall_shop_user')->update($data, array('id' => $id));
                    $this->message('绑定成功！', '', 'success');
                } else {
                    //初始化绑定状态
                    M::t('superman_mall_shop_user')->update(array('binding_flag' => 0), array('id' => $id));
                }
            }
        } else if ($act == 'login_qrcode') {
            $this->checkauth();
            if (!$_W['fans']['follow']) {
                $this->message('请先关注公众号！', $_W['account']['subscribeurl'], 'error');
            } else {
                $params = array(
                    'act' => $_GPC['act'],
                    'id' => $_GPC['id'],
                    't' => $_GPC['t'],
                );
                $sign = SupermanUtil::get_sign($params, $_W['config']['setting']['authkey']);
                if ($_GPC['sign'] != $sign) {
                    $this->message('非法参数！', '', 'error');
                }
                $id = intval($_GPC['id']);
                if (!$id/* || !$shopid*/) {
                    $this->message('非法请求！', '', 'error');
                }
                $row = M::t('superman_mall_openid')->fetch($id);
                if (!$row) {
                    $this->message('非法请求！', '', 'error');
                }
//                $shop = M::t('superman_mall_shop')->fetch($shopid);
//                if (!$shop) {
//                    $this->message('该帐号所在商户不存在或已删除！', '', 'error');
//                }
                $timestamp = $_GPC['t'];
                if (TIMESTAMP - $timestamp > 300) { //5分钟有效期
                    $this->message('二维码已过期，请刷新后重新扫描！', '', 'info');
                }
                $user = M::t('superman_mall_shop_user')->fetchall(array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $_W['fans']['openid'],
                ));
                if (!$user) {
                    $this->message('该微信未绑定帐号，请使用帐号登录检查！', '', 'error');
                }
                if (checksubmit()) {
                    $data = array(
                        'openid' => $_W['fans']['openid'],
                        'status' => 1,
                    );
                    M::t('superman_mall_openid')->update($data, array('id' => $id));
                    $this->message('登录成功！', '', 'success');
                } else {
                    //初始化绑定状态
                    M::t('superman_mall_shop_user')->update(array('binding_flag' => 0), array('id' => $id));
                }
            }
        }
        include $this->template('openid');
    }
}
$obj = new Superman_mall_doMobileOpenid;