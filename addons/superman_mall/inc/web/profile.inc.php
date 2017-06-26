<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebProfile extends Superman {
    public function __construct() {
        parent::__construct();
        parent::init();
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        }
    }

    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'qrcode', 'unbinding', 'check_binding')) ? $_GPC['act'] : 'display';
        $op = $_GPC['op'];
        $nav['title'] = '我的账号';
        if ($act == 'display') {
            define('SUPERMAN_WEB_FULL_SCREEN', true);
            if ($this->shop_user['groupid'] > 0) {
                $user_group = M::t('superman_mall_shop_user_group')->fetch($this->shop_user['groupid']);
            } else {
                $user_group = array(
                    'title' => '管理员',
                );
            }
            if ($this->shop_user['openid']) {
                $user_info = mc_fetch($this->shop_user['openid'], array('nickname', 'avatar'));
            }
            if (checksubmit()) {
                $oldpassword = $_GPC['oldpassword'];
                if ($oldpassword == '') {
                    message('原密码为空，请输入！', referer(), 'error');
                }
                $hash = user_hash($oldpassword, $this->shop_user['salt']);
                if ($hash != $this->shop_user['password']) {
                    message('原密码不正确，请重新输入！', referer(), 'error');
                }
                $newpassword = $_GPC['newpassword'];
                if ($newpassword == '' || !preg_match(SUPERMAN_REGULAR_PASSWORD, $newpassword)) {
                    message('新密码格式不正确，请重新输入！', referer(), 'error');
                }
                $newpassword2 = $_GPC['newpassword2'];
                if ($newpassword == '' || $newpassword != $newpassword2) {
                    message('新密码两次输入不一致，请重新输入！', referer(), 'error');
                }
                $newsalt = random(10);
                $newhash = user_hash($newpassword, $newsalt);
                $data = array(
                    'password' => $newhash,
                    'salt' => $newsalt,
                );
                $condition = array(
                    'id' => $this->shop_user['id'],
                );
                $ret = M::t('superman_mall_shop_user')->update($data, $condition);
                if ($ret) {
                    message('更新密码成功，请重新登录！', wurl('user/logout'), 'success');
                }
                message('更新密码失败，请重试！', '', 'error');
            }
        } else if ($act == 'qrcode') {
            M::t('superman_mall_shop_user')->update(array('binding_flag' => 0), array('id' => $this->shop_user['id']));
            $params = array(
                'act' => 'shop_user',
                'id' => $this->shop_user['id'],
                't' => TIMESTAMP,
            );
            $params['sign'] = SupermanUtil::get_sign($params, $_W['config']['setting']['authkey']);
            $binding_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('openid', $params);
            $qrcode_url = $this->createWebUrl('qrcode', array('content' => $binding_url));
            echo $qrcode_url;
            exit;
        } else if ($act == 'unbinding') {
            M::t('superman_mall_shop_user')->update(array('openid' => ''), array('id' => $this->shop_user['id']));
            message('操作成功！', referer(), 'success');
        } else if ($act == 'check_binding') {
            $row = M::t('superman_mall_shop_user')->fetch($this->shop_user['id']);
            if ($row) {
                if ($row['binding_flag'] == -1) {
                    die('repeated');
                } else if ($row['binding_flag'] == 1) {
                    die('succeed');
                } else {
                    die('waiting');
                }
            }
            die('invalid request');
        }
        include $this->template('profile/index');
    }
}
$obj = new Superman_mall_doWebProfile;