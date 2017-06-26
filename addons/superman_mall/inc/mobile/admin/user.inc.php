<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
$this->check_user_permission('superman_mall_menu_mobile_admin_user');
if ($act == 'user') {
    $title = '账号列表';
    $filter = array(
        'uniacid' => $_W['uniacid'],
        'shopid' => $this->shop['id']
    );
    $list = M::t('superman_mall_shop_user')->fetchall($filter, '', 0, -1);
    if ($list) {
        foreach ($list as &$li) {
            $li['status_title'] = _get_shop_user_status_title($li['status']);
            if ($li['openid'] != '') {
                $member = mc_fetch($li['openid'], array('avatar', 'nickname'));
                $li['avatar'] = $member['avatar'];
                $li['nickname'] = $member['nickname'];
            }
            if ($li['groupid'] > 0) {
                $group = M::t('superman_mall_shop_user_group')->fetch($li['groupid']);
                $li['group_title'] = $group['title'];
            } else {
                $li['group_title'] = '管理员';
            }
        }
        unset($li);
    }
} else if ($act == 'post') {
    $title = '账号管理';
    $id = intval($_GPC['id']);
    if ($id <= 0) {
        $this->message('id非法', '', 'warn');
    }
    $item = M::t('superman_mall_shop_user')->fetch($id);
    if ($item['shopid'] != $this->shop['id']) {
        $this->message('非法请求', '', 'warn');
    }
    if ($item['openid'] != '') {
        $member = mc_fetch($item['openid'], array('avatar', 'nickname'));
    }
    $filter = array(
        'shopid' => $this->shop['id']
    );
    $groups = M::t('superman_mall_shop_user_group')->fetchall($filter, '', 0, -1);
    if (checksubmit('submit1')) {
        $_data = array(
            'status' => $_GPC['status'],
            'remark' => $_GPC['remark']
        );
        if ($item['groupid'] > 0 && $_GPC['groupid'] > 0) {
            $_data['groupid'] = $_GPC['groupid'];
            $_data['status'] = $_GPC['status'];
        }
        M::t('superman_mall_shop_user')->update($_data, array('id' => $id));
        $this->message('操作成功，跳转中...', $this->createMobileUrl('admin', array('route' => 'user.user')), 'success');
    }
}
include $this->template('user/index');

function _get_shop_user_status_title($status) {
    switch ($status) {
        case 0:
            return '待审核';break;
        case 1:
            return '正常';break;
        case 2:
            return '禁用';break;
        default:
            return 'unknown';break;
    }
}