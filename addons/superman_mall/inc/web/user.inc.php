<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebUser extends Superman {
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
        $act = in_array($_GPC['act'], array('user', 'group')) ? $_GPC['act'] : 'display';
        $nav['title'] = '账号管理';
        $op = $_GPC['op'];
        if ($act == 'user') {
            $nav['subtitle'] = '账号';
            $this->check_user_permission('superman_mall_menu_user_user');
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                );
                if ($this->shop) {
                    $filter['shopid'] = $this->shop['id'];
                }
                $total = M::t('superman_mall_shop_user')->count($filter);
                $list = M::t('superman_mall_shop_user')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                        if ($li['groupid'] > 0) {
                            $li['shop_user_group'] = M::t('superman_mall_shop_user_group')->fetch($li['groupid']);
                        } else {
                            $li['shop_user_group'] = array(
                                'title' => '管理员',
                            );
                        }
                        $li['member'] = $li['openid']?mc_fetch($li['openid'], array('nickname', 'avatar')):array();
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
                //print_r($list);
            } else if ($op == 'post') {
                $nav['subtitle'] = '编辑';
                $this->check_web_shop();
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $user_groups = M::t('superman_mall_shop_user_group')->fetchall($filter, '', 0, -1);
                $shop_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
                if ($shop_setting && $shop_setting['international']) {
                    $countrys = M::t('superman_mall_country')->fetchall(array('isshow' => 1), '', 0, -1);
                }
                $id = intval($_GPC['id']);
                if ($id) {
                    $user = M::t('superman_mall_shop_user')->fetch($id);
                    if ($user) {
                        $usetime = array(
                            'start' => date('Y-m-d H:i:s', $user['starttime']),
                            'end' => date('Y-m-d H:i:s', $user['expiretime'] > 0?$user['expiretime']:strtotime('+1 years')),
                        );
                    }
                } else {
                    $usetime = array(
                        'start' => date('Y-m-d H:i:s'),
                        'end' => date('Y-m-d H:i:s', strtotime('+1 years')),
                    );
                }
                if (checksubmit()) {
                    $username = trim($_GPC['username']);
                    if ($username == '') {
                        message('账号名为空，请重新输入！', '', 'error');
                    }
                    if (!preg_match(SUPERMAN_REGULAR_USERNAME, $username)) {
                        message('账号名不合法，请重新输入！', '', 'error');
                    }
                    //查重
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'username' => $username,
                    );
                    if ($id) {
                        $filter['id'] = '# !='.$id;
                    }
                    $check_repeat = M::t('superman_mall_shop_user')->count($filter);
                    if ($check_repeat > 0) {
                        message('该用户名不允许使用，请重新输入！', referer(), 'error');
                    }
                    $data = array(
                        'countryid' => $_GPC['countryid'],
                        'username' => $username,
                        'groupid' => $_GPC['groupid'],
                        'mobile' => $_GPC['mobile'],
                        'status' => $_GPC['status'],
                        'starttime' => strtotime($_GPC['usetime']['start']),
                        'expiretime' => $_GPC['usetime_limit'] == 'on'?0:strtotime($_GPC['usetime']['end']),
                        'remark' => $_GPC['remark'],
                    );
                    $password = $_GPC['password'];
                    $repassword = $_GPC['repassword'];
                    if ($password != '') {
                        if (!preg_match(SUPERMAN_REGULAR_PASSWORD, $password)) {
                            message('密码格式不合法，请重新输入！', '', 'error');
                        }
                        if ($password != $repassword) {
                            message('两次输入的密码不一致，请重新输入！', '', 'error');
                        }
                        $salt = random(10);
                        $newpassword = user_hash($password, $salt);
                        $data['password'] = $newpassword;
                        $data['salt'] = $salt;
                    }
                    if ($id) {
                        /*if ($user['groupid'] == 0) {
                            //管理员无需修改
                            unset($data['groupid']);
                            unset($data['status']);
                            unset($data['starttime']);
                            unset($data['expiretime']);
                        }*/
                        M::t('superman_mall_shop_user')->update($data, array('id' => $id));
                    } else {
                        $data['uniacid'] = $_W['uniacid'];
                        $data['shopid'] = $this->shop['id'];
                        $data['dateline'] = TIMESTAMP;
                        M::t('superman_mall_shop_user')->insert($data);
                    }
                    message('操作成功！', $this->createWebUrl('user', array('act' => 'user', 'op' => 'display')), 'success');
                }
            } else if ($op == 'delete') {        //删除
                $id = intval($_GPC['id']);
                $sql = "DELETE FROM ".tablename('superman_mall_shop_user')." WHERE id=:id";
                pdo_query($sql, array(':id' => $id));
                //M::t('superman_mall_shop_user')->delete(array('id' => $id));
                message('操作成功！', $this->createWebUrl('user', array('act' => 'user', 'op' => 'display')), 'success');
            }
        } else if ($act == 'group') {
            $nav['subtitle'] = '身份';
            $this->check_user_permission('superman_mall_menu_user_group');
            $this->check_web_shop();
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $total = M::t('superman_mall_shop_user_group')->count($filter);
                $list = M::t('superman_mall_shop_user_group')->fetchall($filter, '', $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
                //print_r($list);
            } else if ($op == 'post') {
                $nav['subtitle'] = '编辑';
                $id = intval($_GPC['id']);
                if ($id) {
                    $user_group = M::t('superman_mall_shop_user_group')->fetch($id);
                    if ($user_group) {
                        if ($user_group['permission'] != 'all') {
                            $user_group['permission'] = explode('|', $user_group['permission']);
                        }
                    }
                    //print_r($user_group);
                }
                if (checksubmit()) {
                    $all = $_GPC['all'];
                    if ($all == 1) {
                        $data = array(
                            'id' => $_GPC['id'],
                            'shopid' => $this->shop['id'],
                            'title' => $_GPC['title'],
                            'permission' => 'all',
                        );
                    } else {
                        $data = array(
                            'id' => $_GPC['id'],
                            'shopid' => $this->shop['id'],
                            'title' => $_GPC['title'],
                            'permission' => $_GPC['permission']?implode('|', $_GPC['permission']):'',
                        );
                    }
                    if ($id) {
                        M::t('superman_mall_shop_user_group')->update($data, array('id' => $id));
                    } else {
                        M::t('superman_mall_shop_user_group')->insert($data);
                    }
                    message('操作成功！', $this->createWebUrl('user', array('act' => 'group', 'op' => 'display')), 'success');
                }
            } else if ($op == 'delete') {
                $id = intval($_GPC['id']);
                M::t('superman_mall_shop_user_group')->delete(array('id' => $id));
                message('操作成功！', referer(), 'success');
            }
        }
        include $this->template('user');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('user', 'group')) ? $_GPC['act'] : 'display';
        $nav['title'] = '账号管理';
        $op = $_GPC['op'];
        if ($act == 'user') {
            $nav['subtitle'] = '账号';
            $this->check_user_permission('superman_mall_menu_user_user');
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array();
                $filter['shopid'] = $this->shop['id'];
                $total = M::t('superman_mall_shop_user')->count($filter);
                $list = M::t('superman_mall_shop_user')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                        if ($li['groupid'] > 0) {
                            $li['shop_user_group'] = M::t('superman_mall_shop_user_group')->fetch($li['groupid']);
                        } else {
                            $li['shop_user_group'] = array(
                                'title' => '管理员',
                            );
                        }
                        $li['member'] = $li['openid']?mc_fetch($li['openid'], array('nickname', 'avatar')):array();
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
                //print_r($list);
            } else if ($op == 'post') {
                $nav['subtitle'] = '编辑';
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $user_groups = M::t('superman_mall_shop_user_group')->fetchall($filter, '', 0, -1);
                $id = intval($_GPC['id']);
                if ($id) {
                    $user = M::t('superman_mall_shop_user')->fetch($id);
                    if ($user) {
                        $usetime = array(
                            'start' => date('Y-m-d H:i:s', $user['starttime']),
                            'end' => date('Y-m-d H:i:s', $user['expiretime'] > 0?$user['expiretime']:strtotime('+1 years')),
                        );
                    }
                } else {
                    $usetime = array(
                        'start' => date('Y-m-d H:i:s'),
                        'end' => date('Y-m-d H:i:s', strtotime('+1 years')),
                    );
                }
                if (checksubmit()) {
                    $username = trim($_GPC['username']);
                    if ($username == '') {
                        message('账号名为空，请重新输入！', '', 'error');
                    }
                    if (!preg_match(SUPERMAN_REGULAR_USERNAME, $username)) {
                        message('账号名不合法，请重新输入！', '', 'error');
                    }
                    //查重
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'username' => $username,
                    );
                    if ($id) {
                        $filter['id'] = '# !='.$id;
                    }
                    $check_repeat = M::t('superman_mall_shop_user')->count($filter);
                    if ($check_repeat > 0) {
                        message('该用户名不允许使用，请重新输入！', referer(), 'error');
                    }
                    $data = array(
                        'countryid' => $_GPC['countryid'],
                        'username' => $username,
                        'groupid' => $_GPC['groupid'],
                        'status' => $_GPC['status'],
                        'starttime' => strtotime($_GPC['usetime']['start']),
                        'expiretime' => $_GPC['usetime_limit'] == 'on'?0:strtotime($_GPC['usetime']['end']),
                        'remark' => $_GPC['remark'],
                    );
                    $mobile = trim($_GPC['mobile']);
                    if ($mobile != '') {
                        if (!preg_match(SUPERMAN_REGULAR_MOBILE, $mobile)) {
                            message('手机号不合法，请重新输入！', '', 'error');
                        }
                        if ($this->shop_user['groupid'] == 0) {
                            $data['mobile'] = $_GPC['mobile']; //管理员可直接修改手机号
                        } else {
                            //TODO: 手机号验证码
                        }
                    }
                    $password = $_GPC['password'];
                    $repassword = $_GPC['repassword'];
                    if ($password != '') {
                        if (!preg_match(SUPERMAN_REGULAR_PASSWORD, $password)) {
                            message('密码格式不合法，请重新输入！', '', 'error');
                        }
                        if ($password != $repassword) {
                            message('两次输入的密码不一致，请重新输入！', '', 'error');
                        }
                        $salt = random(10);
                        $newpassword = user_hash($password, $salt);
                        $data['password'] = $newpassword;
                        $data['salt'] = $salt;
                    }
                    if ($id) {
                        if ($user['groupid'] == 0) {
                            //管理员无需修改
                            unset($data['groupid']);
                            unset($data['status']);
                            unset($data['starttime']);
                            unset($data['expiretime']);
                        }
                        M::t('superman_mall_shop_user')->update($data, array('id' => $id));
                    } else {
                        $data['uniacid'] = $_W['uniacid'];
                        $data['shopid'] = $this->shop['id'];
                        $data['dateline'] = TIMESTAMP;
                        M::t('superman_mall_shop_user')->insert($data);
                    }
                    message('操作成功！', $this->createWebUrl('user', array('act' => 'user', 'op' => 'display')), 'success');
                }
            } else if ($op == 'delete') {        //删除
                $id = intval($_GPC['id']);
                $sql = "DELETE FROM ".tablename('superman_mall_shop_user')." WHERE id=:id AND groupid!=0";
                pdo_query($sql, array(':id' => $id));
                //M::t('superman_mall_shop_user')->delete(array('id' => $id));
                message('操作成功！', $this->createWebUrl('user', array('act' => 'user', 'op' => 'display')), 'success');
            }
        } else if ($act == 'group') {
            $nav['subtitle'] = '身份';
            $this->check_user_permission('superman_mall_menu_user_group');
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $total = M::t('superman_mall_shop_user_group')->count($filter);
                $list = M::t('superman_mall_shop_user_group')->fetchall($filter, '', $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
                //print_r($list);
            } else if ($op == 'post') {
                $nav['subtitle'] = '编辑';
                $id = intval($_GPC['id']);
                if ($id) {
                    $user_group = M::t('superman_mall_shop_user_group')->fetch($id);
                    if ($user_group) {
                        if ($user_group['permission'] != 'all') {
                            $user_group['permission'] = explode('|', $user_group['permission']);
                        }
                    }
                    //print_r($user_group);
                }
                if (checksubmit()) {
                    $all = $_GPC['all'];
                    if ($all == 1) {
                        $data = array(
                            'id' => $_GPC['id'],
                            'shopid' => $this->shop['id'],
                            'title' => $_GPC['title'],
                            'permission' => 'all',
                        );
                    } else {
                        $data = array(
                            'id' => $_GPC['id'],
                            'shopid' => $this->shop['id'],
                            'title' => $_GPC['title'],
                            'permission' => $_GPC['permission']?implode('|', $_GPC['permission']):'',
                        );
                    }
                    if ($id) {
                        M::t('superman_mall_shop_user_group')->update($data, array('id' => $id));
                    } else {
                        M::t('superman_mall_shop_user_group')->insert($data);
                    }
                    message('操作成功！', $this->createWebUrl('user', array('act' => 'group', 'op' => 'display')), 'success');
                }
            } else if ($op == 'delete') {
                $id = intval($_GPC['id']);
                M::t('superman_mall_shop_user_group')->delete(array('id' => $id));
                message('操作成功！', referer(), 'success');
            }
        }
        include $this->template('user/index');
    }
}
$obj = new Superman_mall_doWebUser;