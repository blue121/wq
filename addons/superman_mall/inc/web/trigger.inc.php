<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebTrigger extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->check_user_permission('superman_mall_menu_trigger');
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        if ($_GPC['isplatform']) {
            $this->_tigger_platform();
        } else if ($_GPC['isshop']) {
            $this->_tigger_shop();
        }
    }
    private function _tigger_platform() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'new'))?$_GPC['act']:'display';
        $nav['title'] = '触发器规则';
        if ($act == 'display') {
            $nav['subtitle'] = '平台触发器';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid']
            );
            if (isset($_GPC['title']) && $_GPC['title'] != '') {
                $filter['title'] = '# LIKE "%'.$_GPC['title'].'%"';
            }
            $total = M::t('superman_mall_trigger')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_trigger')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['action_title'] = SupermanTrigger::$platform_actions[$li['action']]['title'];
                        $li['notices'] = M::t('superman_mall_trigger_notice')->fetchall(array('triggerid' => $li['id']), '', 0, -1);
                        foreach ($li['notices'] as &$v) {
                            if ($v['type'] == 1) {
                                $member = mc_fetch($v['receiver'], array('nickname', 'avatar'));
                                $v['nickname'] = $member['nickname']?$member['nickname']:'该粉丝不存在';
                                $v['avatar'] = $member['avatar'];
                            }
                        }
                        unset($v);
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);

            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
            if ($id > 0) {
                $item = M::t('superman_mall_trigger')->fetch($id);
                if (!$item) {
                    message('该动作不存在或已删除', referer(), 'error');
                }
                $item['notices'] = M::t('superman_mall_trigger_notice')->fetchall(array('triggerid' => $id), '', 0, -1);
                if ($item['notices']) {
                    foreach ($item['notices'] as &$value) {
                        $member = mc_fetch($value['receiver'], array('nickname', 'avatar'));
                        $value['nickname'] = $member['nickname']?$member['nickname']:'该粉丝不存在';
                        $value['avatar'] = $member['avatar'];
                    }
                    unset($value);
                }
            }
            if (checksubmit()) {
                $_data = array(
                    'title' => $_GPC['title'],
                    'action' => $_GPC['action'],
                    'status' => $_GPC['status']?1:0,
                );
                if (isset($item)) { //编辑
                    M::t('superman_mall_trigger')->update($_data, array('id' => $id));
                } else {    //新增
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['dateline'] = TIMESTAMP;
                    $id = M::t('superman_mall_trigger')->insert($_data);
                }
                if ($id > 0) {
                    //更新通知
                    $type = $_GPC['type'];
                    $openid = $_GPC['openid'];
                    $mobile = $_GPC['mobile'];
                    $message = $_GPC['message'];
                    $nid = $_GPC['nid'];
                    if ($type) {
                        foreach ($type as $k => $t) {
                            $notice_data = array(
                                'triggerid' => $id,
                                'type' => $t,
                                'message' => $message[$k]
                            );
                            if ($t == 1) {
                                $notice_data['receiver'] = $openid[$k];
                            } else {
                                $notice_data['receiver'] = $mobile[$k];
                            }
                            if ($nid[$k] > 0) {
                                M::t('superman_mall_trigger_notice')->update($notice_data, array('id' => $nid[$k]));
                            } else {
                                M::t('superman_mall_trigger_notice')->insert($notice_data);
                            }
                        }
                    }
                }
                message('操作成功！', $this->createWebUrl('trigger', array('act' => 'display', 'isplatform' => 1)));
            }
        } else if ($act == 'delete') {
            if ($_W['isajax']) {
                $nid = intval($_GPC['nid']);
                if ($nid > 0) {
                    M::t('superman_mall_trigger_notice')->delete(array('id' => $nid));
                    echo 'success';
                    exit;
                } else {
                    echo '非法请求';
                    exit;
                }
            }
            $id = intval($_GPC['id']);
            if ($id < 0) {
                message('非法请求', referer(), 'error');
            }
            $row = M::t('superman_mall_trigger')->fetch($id);
            if (!$row) {
                message('该动作不存在或已删除');
            }
            pdo_begin();
            $ret1 = M::t('superman_mall_trigger')->delete(array('id' => $id));
            $ret2 = M::t('superman_mall_trigger_notice')->delete(array('triggerid' => $id));
            if ($ret1 !== false && $ret2 !== false) {
                pdo_commit();
                message('删除成功！', referer());
            } else {
                pdo_rollback();
                message('删除失败，请稍后再试', referer(), 'error');
            }
        } else if ($act == 'new') {
            include $this->template('trigger-new');
            exit;
        }
        include $this->template('trigger');
    }
    private function _tigger_shop() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'new'))?$_GPC['act']:'display';
        $nav['title'] = '触发器规则';
        if ($act == 'display') {
            $nav['subtitle'] = '商户触发器';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            if (isset($_GPC['title']) && $_GPC['title'] != '') {
                $filter['title'] = '# LIKE "%'.$_GPC['title'].'%"';
            }
            $total = M::t('superman_mall_shop_trigger')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_trigger')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                        $li['action_title'] = SupermanTrigger::$shop_actions[$li['action']]['title'];
                        $li['notices'] = M::t('superman_mall_shop_trigger_notice')->fetchall(array('triggerid' => $li['id']), '', 0, -1);
                        foreach ($li['notices'] as &$v) {
                            if ($v['type'] == 1) {
                                $member = mc_fetch($v['receiver'], array('nickname', 'avatar'));
                                $v['nickname'] = $member['nickname']?$member['nickname']:'该粉丝不存在';
                                $v['avatar'] = $member['avatar'];
                            }
                        }
                        unset($v);
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $item = array();
            $id = intval($_GPC['id']);
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
            if ($id > 0) {
                $item = M::t('superman_mall_shop_trigger')->fetch($id);
                if (!$item) {
                    message('该动作不存在或已删除', referer(), 'error');
                }
                $item['notices'] = M::t('superman_mall_shop_trigger_notice')->fetchall(array('triggerid' => $id), '', 0, -1);
            } else {
                $this->check_web_shop();
            }
            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => 1
            );
            $user_list = M::t('superman_mall_shop_user')->fetchall($filter, '', 0, -1);
            if (checksubmit()) {
                $_data = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $_GPC['title'],
                    'action' => $_GPC['action'],
                    'status' => $_GPC['status']?1:0,
                );
                if ($item) { //编辑
                    M::t('superman_mall_shop_trigger')->update($_data, array('id' => $id));
                } else {    //新增
                    $_data['shopid'] = $this->shop['id'];
                    $_data['dateline'] = TIMESTAMP;
                    $id = M::t('superman_mall_shop_trigger')->insert($_data);
                }
                if ($id > 0) {
                    //更新通知
                    $type = $_GPC['type'];
                    $openid = $_GPC['openid'];
                    $mobile = $_GPC['mobile'];
                    $message = $_GPC['message'];
                    $nid = $_GPC['nid'];
                    if ($type) {
                        foreach ($type as $k => $t) {
                            $notice_data = array(
                                'triggerid' => $id,
                                'type' => $t,
                                'message' => $message[$k]
                            );
                            if ($t == 1) {
                                $notice_data['receiver'] = $openid[$k];
                            } else {
                                $notice_data['receiver'] = $mobile[$k];
                            }
                            if ($nid[$k] > 0) {
                                M::t('superman_mall_shop_trigger_notice')->update($notice_data, array('id' => $nid[$k]));
                            } else {
                                M::t('superman_mall_shop_trigger_notice')->insert($notice_data);
                            }
                        }
                    }
                }
                message('操作成功！', $this->createWebUrl('trigger', array('act' => 'display', 'isshop' => 1)));
            }
        } else if ($act == 'delete') {
            if ($_W['isajax']) {
                $nid = intval($_GPC['nid']);
                if ($nid > 0) {
                    M::t('superman_mall_shop_trigger_notice')->delete(array('id' => $nid));
                    echo 'success';
                    exit;
                } else {
                    echo '非法请求';
                    exit;
                }
            }
            $id = intval($_GPC['id']);
            if ($id < 0) {
                message('非法请求', referer(), 'error');
            }
            $row = M::t('superman_mall_shop_trigger')->fetch($id);
            if (!$row) {
                message('该动作不存在或已删除');
            }
            pdo_begin();
            $ret1 = M::t('superman_mall_shop_trigger')->delete(array('id' => $id));
            $ret2 = M::t('superman_mall_shop_trigger_notice')->delete(array('triggerid' => $id));
            if ($ret1 !== false && $ret2 !== false) {
                pdo_commit();
                message('删除成功！', referer());
            } else {
                pdo_rollback();
                message('删除失败，请稍后再试', referer(), 'error');
            }
        } else if ($act == 'new') {
            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => 1
            );
            $user_list = M::t('superman_mall_shop_user')->fetchall($filter, '', 0, -1);
            include $this->template('trigger-new');
            exit;
        }
        include $this->template('trigger-shop');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'new'))?$_GPC['act']:'display';
        $nav['title'] = '触发器规则';
        if ($act == 'display') {
            $nav['subtitle'] = '规则列表';
            $this->check_user_permission('superman_mall_menu_trigger_display');
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;

            $filter = array(
                'shopid' => $this->shop['id']
            );
            if (isset($_GPC['title']) && $_GPC['title'] != '') {
                $filter['title'] = '# LIKE "%'.$_GPC['title'].'%"';
            }
            $total = M::t('superman_mall_shop_trigger')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_trigger')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['action_title'] = SupermanTrigger::$shop_actions[$li['action']]['title'];
                        $li['notices'] = M::t('superman_mall_shop_trigger_notice')->fetchall(array('triggerid' => $li['id']), '', 0, -1);
                        foreach ($li['notices'] as &$v) {
                            if ($v['type'] == 1) {
                                $member = mc_fetch($v['receiver'], array('nickname', 'avatar'));
                                $v['nickname'] = $member['nickname']?$member['nickname']:'该粉丝不存在';
                                $v['avatar'] = $member['avatar'];
                            }
                        }
                        unset($v);
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $this->check_user_permission('superman_mall_menu_trigger_post');
            $id = intval($_GPC['id']);
            if ($id > 0) {
                $item = M::t('superman_mall_shop_trigger')->fetch($id);
                if (!$item) {
                    message('该动作不存在或已删除', referer(), 'error');
                }
                $item['notices'] = M::t('superman_mall_shop_trigger_notice')->fetchall(array('triggerid' => $id), '', 0, -1);
            }
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);

            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => 1
            );
            $user_list = M::t('superman_mall_shop_user')->fetchall($filter, '', 0, -1);
            if (checksubmit()) {
                $_data = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $_GPC['title'],
                    'action' => $_GPC['action'],
                    'status' => $_GPC['status']?1:0,
                );
                if (isset($item)) { //编辑
                    M::t('superman_mall_shop_trigger')->update($_data, array('id' => $id));
                } else {    //新增
                    $_data['shopid'] = $this->shop['id'];
                    $_data['dateline'] = TIMESTAMP;
                    $id = M::t('superman_mall_shop_trigger')->insert($_data);
                }
                if ($id > 0) {
                    //更新通知
                    $type = $_GPC['type'];
                    $openid = $_GPC['openid'];
                    $mobile = $_GPC['mobile'];
                    $message = $_GPC['message'];
                    $nid = $_GPC['nid'];
                    if ($type) {
                        foreach ($type as $k => $t) {
                            $notice_data = array(
                                'triggerid' => $id,
                                'type' => $t,
                                'message' => $message[$k]
                            );
                            if ($t == 1) {
                                $notice_data['receiver'] = $openid[$k];
                            } else {
                                $notice_data['receiver'] = $mobile[$k];
                            }
                            if ($nid[$k] > 0) {
                                M::t('superman_mall_shop_trigger_notice')->update($notice_data, array('id' => $nid[$k]));
                            } else {
                                M::t('superman_mall_shop_trigger_notice')->insert($notice_data);
                            }
                        }
                    }
                }
                message('操作成功！', $this->createWebUrl('trigger'));
            }
        } else if ($act == 'delete') {
            $this->check_user_permission('superman_mall_menu_trigger_delete');
            if ($_W['isajax']) {
                $nid = intval($_GPC['nid']);
                if ($nid > 0) {
                    M::t('superman_mall_shop_trigger_notice')->delete(array('id' => $nid));
                    echo 'success';
                    exit;
                } else {
                    echo '非法请求';
                    exit;
                }
            }
            $id = intval($_GPC['id']);
            if ($id < 0) {
                message('非法请求', referer(), 'error');
            }
            $row = M::t('superman_mall_shop_trigger')->fetch($id);
            if (!$row) {
                message('该动作不存在或已删除');
            }
            if ($row['shopid'] != $this->shop['id']) {
                message('非法请求', referer(), 'error');
            }
            pdo_begin();
            $ret1 = M::t('superman_mall_shop_trigger')->delete(array('id' => $id));
            $ret2 = M::t('superman_mall_shop_trigger_notice')->delete(array('triggerid' => $id));
            if ($ret1 !== false && $ret2 !== false) {
                pdo_commit();
                message('删除成功！', referer());
            } else {
                pdo_rollback();
                message('删除失败，请稍后再试', referer(), 'error');
            }
        } else if ($act == 'new') {
            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => 1
            );
            $user_list = M::t('superman_mall_shop_user')->fetchall($filter, '', 0, -1);
            include $this->template('trigger/new');
            exit;
        }
        include $this->template('trigger/index');
    }
}
$obj = new Superman_mall_doWebTrigger();