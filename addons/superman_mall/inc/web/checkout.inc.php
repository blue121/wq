<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebCheckout extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'qrcode', 'oneself'))?$_GPC['act']:'display';
        $nav['title'] = '线下核销';
        if ($act == 'display') {
            $nav['subtitle'] = '核销记录';
            $this->check_user_permission('superman_mall_menu_checkout_display');
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            if (isset($_GPC['ordersn']) && $_GPC['ordersn']) {
                $filter['ordersn'] = '# LIKE "%'.intval($_GPC['ordersn']).'%"';
            }
            $orderby = ' ORDER BY dateline DESC';
            $list = M::t('superman_mall_checkout_log')->fetchall($filter, $orderby, $start, $pagesize);
            if ($list) {
                foreach ($list as &$li) {
                    $li['member'] = mc_fetch($li['uid'], array('avatar', 'nickname'));
                    $li['dateline'] = date('Y-m-d H:i:s', $li['dateline']);
                    if ($li['type'] == 1) {
                        $li['type'] = '扫码核销';
                        $checkout = M::t('superman_mall_checkout_user')->fetch($li['checkout_id']);
                        $li['user'] = mc_fetch($checkout['uid'], array('nickname', 'avatar'));
                    } else {
                        $li['type'] = '自助核销';
                        $checkout = M::t('superman_mall_checkout_code')->fetch($li['checkout_id']);
                        $li['code'] = $checkout['code'];
                    }
                    unset($li);
                }
            }
            $total = M::t('superman_mall_checkout_log')->count($filter);
            $pager = pagination($total, $pindex, $pagesize);
        } else if ($act == 'qrcode') {
            $nav['subtitle'] = '扫码核销';
            $this->check_user_permission('superman_mall_menu_checkout_qrcode');
            if ($_GPC['op'] == '') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }
                $list = M::t('superman_mall_checkout_user')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $member = mc_fetch($li['uid'], array('nickname', 'avatar'));
                        $li['nickname'] = $member['nickname'];
                        $li['avatar'] = $member['avatar'];
                        unset($li, $member);
                    }
                }
                $total = M::t('superman_mall_checkout_user')->count($filter);
                $pager = pagination($total, $pindex, $pagesize);
            } else if ($_GPC['op'] == 'post') {    //添加核销员
                $nav['subtitle'] = '编辑';
                $this->check_web_shop();
                if (checksubmit('submit')) {
                    $openid = $_GPC['openid'];
                    $remark = $_GPC['remark'];
                    //查重
                    $filter = array(
                        'shopid' => $this->shop['id'],
                        'openid' => $openid,
                    );
                    $count = M::t('superman_mall_checkout_user')->count($filter);
                    if ($count > 0) {
                        message('该用户已添加为核销员，请勿重复添加', referer(), 'info');
                    }
                    if ($openid) {
                        $member = mc_fansinfo($openid);
                    }
                    if (isset($member['follow']) && $member['follow'] == 1) {
                        $data = array(
                            'uniacid' => $_W['uniacid'],
                            'shopid' => $this->shop['id'],
                            'uid' => $member['uid'],
                            'openid' => $openid,
                            'status' => 1,
                            'remark' => $remark,
                            'dateline' => TIMESTAMP,
                        );
                        $new_id = M::t('superman_mall_checkout_user')->insert($data);
                        if ($new_id) {
                            message('操作成功！', $this->createWebUrl('checkout', array('act' => 'qrcode')), 'success');
                        } else {
                            message('数据库出错，请稍后重试', referer(), 'error');
                        }
                    } else {
                        message('粉丝编号输入有误，请重新输入', referer(), 'error');
                    }
                }
            } else if ($_GPC['op'] == 'delete') {   //删除核销员
                $id = intval($_GPC['id']);
                if ($id > 0) {
                    $ret = M::t('superman_mall_checkout_user')->delete(array('id' => $id));
                    if ($ret !== false) {
                        message('删除成功！', referer(), 'success');
                    } else {
                        message('删除失败！请返回重试', referer(), 'error');
                    }
                } else {
                    message('该核销员不存在或已删除');
                }
            }
        } else if ($act == 'oneself') {
            $nav['subtitle'] = '自助核销';
            $this->check_user_permission('superman_mall_menu_checkout_oneself');
            if ($_GPC['op'] == '') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }
                $list = M::t('superman_mall_checkout_code')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['dateline'] = date('Y-m-d H:i:s', $li['dateline']);
                        unset($li);
                    }
                }
                $total = M::t('superman_mall_checkout_code')->count($filter);
                $pager = pagination($total, $pindex, $pagesize);
            } else if ($_GPC['op'] == 'post') {
                $id = intval($_GPC['id']);
                if ($id > 0) {
                    $row = M::t('superman_mall_checkout_code')->fetch($id);
                    if (!$row) {
                        $id = 0;
                    }
                } else {
                    $this->check_web_shop();
                }
                if (checksubmit('submit')) {
                    $title = trim($_GPC['title']);
                    $code = trim($_GPC['code']);
                    $remark = $_GPC['remark'];
                    if ($title != '' && $code != '') {
                        $data = array(
                            'title' => $title,
                            'code' => $code,
                            'remark' => $remark,
                        );
                        if ($id > 0) {
                            $ret = M::t('superman_mall_checkout_code')->update($data, array('id' => $id));
                        } else {
                            $data['uniacid'] = $_W['uniacid'];
                            $data['shopid'] = $this->shop['id'];
                            $data['dateline'] = TIMESTAMP;
                            $new_id = M::t('superman_mall_checkout_code')->insert($data);
                            if ($new_id) {
                                $ret = true;
                            } else {
                                $ret = false;
                            }
                        }
                        if ($ret !== false) {
                            message('操作成功！', $this->createWebUrl('checkout', array('act' => 'oneself')), 'success');
                        } else {
                            message('数据库出错，请稍后重试', referer(), 'error');
                        }
                    } else {
                        message('标题或验证码不能为空或0', referer(), 'error');
                    }
                }
            } else if ($_GPC['op'] == 'delete') {
                $id = intval($_GPC['id']);
                if ($id > 0) {
                    $ret = M::t('superman_mall_checkout_code')->delete(array('id' => $id));
                    if ($ret !== false) {
                        message('删除成功！', referer(), 'success');
                    } else {
                        message('删除失败！请返回重试', referer(), 'error');
                    }
                } else {
                    message('该验证码不存在或已删除');
                }
            }
        }
        include $this->template('checkout');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'qrcode', 'oneself', 'check'))?$_GPC['act']:'display';
        $nav['title'] = '线下核销';
        if ($act == 'display') {
            $nav['subtitle'] = '核销记录';
            $this->check_user_permission('superman_mall_menu_checkout_display');
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $this->shop['id'],
            );
            if (isset($_GPC['ordersn']) && $_GPC['ordersn']) {
                $filter['ordersn'] = '# LIKE "%'.intval($_GPC['ordersn']).'%"';
            }
            $orderby = ' ORDER BY dateline DESC';
            $list = M::t('superman_mall_checkout_log')->fetchall($filter, $orderby, $start, $pagesize);
            if ($list) {
                foreach ($list as &$li) {
                    $li['member'] = mc_fetch($li['uid'], array('avatar', 'nickname'));
                    $li['dateline'] = date('Y-m-d H:i:s', $li['dateline']);
                    if ($li['type'] == 1) {
                        $li['type'] = '扫码核销';
                        $checkout = M::t('superman_mall_checkout_user')->fetch($li['checkout_id']);
                        $li['user'] = mc_fetch($checkout['uid'], array('nickname', 'avatar'));
                    } else {
                        $li['type'] = '自助核销';
                        $checkout = M::t('superman_mall_checkout_code')->fetch($li['checkout_id']);
                        $li['code'] = $checkout['code'];
                    }
                    unset($li);
                }
            }
            $total = M::t('superman_mall_checkout_log')->count($filter);
            $pager = pagination($total, $pindex, $pagesize);
        } else if ($act == 'qrcode') {
            $nav['subtitle'] = '扫码核销';
            $this->check_user_permission('superman_mall_menu_checkout_qrcode');
            if ($_GPC['op'] == '') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'shopid' => $this->shop['id']
                );
                $list = M::t('superman_mall_checkout_user')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $member = mc_fetch($li['uid'], array('nickname', 'avatar'));
                        $li['nickname'] = $member['nickname'];
                        $li['avatar'] = $member['avatar'];
                        unset($li, $member);
                    }
                }
                $total = M::t('superman_mall_checkout_user')->count($filter);
                $pager = pagination($total, $pindex, $pagesize);
            } else if ($_GPC['op'] == 'post') {    //添加核销员
                $user = M::t('superman_mall_checkout_user')->fetch(array(
                    'shopid' => $this->shop['id'],
                    'status' => 0,
                ));
                if ($user) {
                    $user['member'] = mc_fetch($user['uid'], array('avatar', 'nickname'));
                }
                if (checksubmit('submit')) {
                    $id = $_GPC['id'];
                    $remark = $_GPC['remark'];
                    if ($id) {
                        $data = array(
                            'status' => 1,
                            'remark' => $remark,
                            'dateline' => TIMESTAMP,
                        );
                        M::t('superman_mall_checkout_user')->update($data, array('id' => $id));
                        message('操作成功！', $this->createWebUrl('checkout', array('act' => 'qrcode')), 'success');
                    }
                }
            } else if ($_GPC['op'] == 'delete') {   //删除核销员
                $id = intval($_GPC['id']);
                if ($id > 0) {
                    $ret = M::t('superman_mall_checkout_user')->delete(array('id' => $id));
                    if ($ret !== false) {
                        message('删除成功！', referer(), 'success');
                    } else {
                        message('删除失败！请返回重试', referer(), 'error');
                    }
                } else {
                    message('该核销员不存在或已删除');
                }
            } else if ($_GPC['op'] == 'check') {
                $user = M::t('superman_mall_checkout_user')->fetch(array(
                    'shopid' => $this->shop['id'],
                    'status' => 0,
                ));
                if ($user) {
                    exit('yes');
                } else {
                    exit('no');
                }
            }
        } else if ($act == 'oneself') {
            $nav['subtitle'] = '自助核销';
            $this->check_user_permission('superman_mall_menu_checkout_oneself');
            if ($_GPC['op'] == '') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'shopid' => $this->shop['id']
                );
                $list = M::t('superman_mall_checkout_code')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['dateline'] = date('Y-m-d H:i:s', $li['dateline']);
                        unset($li);
                    }
                }
                $total = M::t('superman_mall_checkout_code')->count($filter);
                $pager = pagination($total, $pindex, $pagesize);
            } else if ($_GPC['op'] == 'post') {
                $nav['subtitle'] = '编辑';
                $id = intval($_GPC['id']);
                if ($id > 0) {
                    $row = M::t('superman_mall_checkout_code')->fetch($id);
                    if (!$row) {
                        $id = 0;
                    }
                }
                if (checksubmit('submit')) {
                    $title = trim($_GPC['title']);
                    $code = trim($_GPC['code']);
                    $remark = $_GPC['remark'];
                    if ($title != '' && $code != '') {
                        $data = array(
                            'title' => $title,
                            'code' => $code,
                            'remark' => $remark,
                        );
                        if ($id > 0) {
                            $ret = M::t('superman_mall_checkout_code')->update($data, array('id' => $id));
                        } else {
                            $data['uniacid'] = $_W['uniacid'];
                            $data['shopid'] = $this->shop['id'];
                            $data['dateline'] = TIMESTAMP;
                            $new_id = M::t('superman_mall_checkout_code')->insert($data);
                            if ($new_id) {
                                $ret = true;
                            } else {
                                $ret = false;
                            }
                        }
                        if ($ret !== false) {
                            message('操作成功！', $this->createWebUrl('checkout', array('act' => 'oneself')), 'success');
                        } else {
                            message('数据库出错，请稍后重试', referer(), 'error');
                        }
                    } else {
                        message('标题或验证码不能为空或0', referer(), 'error');
                    }
                }
            } else if ($_GPC['op'] == 'delete') {
                $id = intval($_GPC['id']);
                if ($id > 0) {
                    $ret = M::t('superman_mall_checkout_code')->delete(array('id' => $id));
                    if ($ret !== false) {
                        message('删除成功！', referer(), 'success');
                    } else {
                        message('删除失败！请返回重试', referer(), 'error');
                    }
                } else {
                    message('该验证码不存在或已删除');
                }
            }
        }
        include $this->template('checkout/index');
    }
}
$obj = new Superman_mall_doWebCheckout;