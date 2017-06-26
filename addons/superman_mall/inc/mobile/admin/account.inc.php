<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
if ($act == 'switch') {
    $title = '选择商户账号';
    $filter = array(
        'uniacid' => $_W['uniacid'],
        'openid' => $_W['openid'],
    );
    $list = M::t('superman_mall_shop_user')->fetchall($filter, '', 0, -1);
    if ($list) {
        foreach ($list as &$li) {
            $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
            if ($li['groupid'] > 0) {
                $li['group'] = M::t('superman_mall_shop_user_group')->fetch($li['groupid']);
            }
        }
        unset($li);
    } else {
        $this->message('该微信未绑定帐号', '', 'warn');
    }

    if (checksubmit()) {
        $key = $_GPC['admin_user_key'];
        if (!isset($list[$key])) {
            $this->message('非法请求', '', 'warn');
        }
        $shop_admin = $list[$key];
        if ($shop_admin['status'] != 1) {
            $this->message('当前账号正在审核或已禁用，请联系管理员！', '', 'warn');
        }
        if ($shop_admin['starttime'] > TIMESTAMP) {
            $this->message('当前账号未启用，请联系管理员！', '', 'warn');
        }
        if ($shop_admin['expiretime'] > 0 && $shop_admin['expiretime'] < TIMESTAMP) {
            $this->message('当前账号已过期，请联系管理员！', '', 'warn');
        }
        if (empty($shop_admin['shopid'])) {
            $this->message('您的账号未申请商户入驻，请申请商户入驻！', '', 'warn');
        }
        if (empty($shop_admin['shop'])) {
            $this->message('商户信息不存在或已删除，请重新申请商户入驻！', '', 'warn');
        }
        if (!empty($_W['siteclose'])) {
            $this->message('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason'], '', 'warn');
        }
        $_SESSION['mobile_admin_userid'] = $shop_admin['id'];
        $_SESSION['mobile_admin_shopid'] = $shop_admin['shopid'];
        //更新登录时间和ip
        $_data = array(
            'lastvisit' => TIMESTAMP,
            'lastip' => CLIENT_IP,
        );
        M::t('superman_mall_shop_user')->update($_data, array('id' => $shop_admin['id']));
        $url = $_GPC['from']?$this->createMobileUrl('admin', array('route' => $_GPC['from'])):$this->createMobileUrl('admin', array('route' => 'dashboard'));
        $this->message('登录成功，跳转中...', $url, 'success');
    }

    include $this->template('account/index');
}