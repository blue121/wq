<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebMyfetch extends Superman {
    public function __construct() {
        parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_item_myfetch');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
    }
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'setting')) ? $_GPC['act'] : 'display';
        $nav['title'] = '自提门店';
        if ($act == 'display') {
            $nav['subtitle'] = '门店列表';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $list = array();
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            if (isset($_GPC['title']) && $_GPC['title'] != '') {
                $filter['title'] = "# LIKE '%{$_GPC['title']}%'";
            }
            if (isset($_GPC['area'])) {
                if ($_GPC['area']['province'] != '') {
                    $filter['province'] = $_GPC['area']['province'];
                }
                if ($_GPC['area']['city'] != '') {
                    $filter['city'] = $_GPC['area']['city'];
                }
                if ($_GPC['area']['district'] != '') {
                    $filter['district'] = $_GPC['area']['district'];
                }
            }
            $total = M::t('superman_mall_myfetch')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_myfetch')->fetchall($filter, 'ORDER BY displayorder DESC', $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
            }
            /*if ($_GPC['displayorder']) {
                foreach ($_GPC['displayorder'] as $id=>$val) {
                    M::t('superman_mall_myfetch')->update(array('displayorder' => $val), array('id' => $id));
                }
                message('操作成功！', referer(), 'success');
            }*/
            if ($_GPC['_method'] == 'switch') {
                $id = $_GPC['id'];
                $value = $_GPC['value'];
                $field = $_GPC['field'];
                $data = array(
                    $field => $value,
                );
                $condition = array(
                    'id' => $id,
                );
                M::t('superman_mall_myfetch')->update($data, $condition);
                echo 'success';
                exit;
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            if ($id) {
                $item = M::t('superman_mall_myfetch')->fetch($id);
                if (!$item) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
            } else {
                $this->check_web_shop();
            }
            if (checksubmit()) {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'title' => $_GPC['title'],
                );
                $row = M::t('superman_mall_myfetch')->fetch($filter);
                if (!$id) { //insert
                    if ($row) {
                        message('门店名称已存在，请重新填写！', referer(), 'error');
                    }
                } else { //update
                    if ($row && $row['id'] != $id) {
                        message('门店名称已存在，请重新填写！', referer(), 'error');
                    }
                }
                $data = array(
                    'displayorder' => $_GPC['displayorder'],
                    'title' => $_GPC['title'],
                    'username' => $_GPC['username'],
                    'mobile' => $_GPC['mobile'],
                    'address' => $_GPC['address'],
                    'isshow' => $_GPC['isshow'],
                    'province' => $_GPC['area']['province'],
                    'city' => $_GPC['area']['city'],
                    'district' => $_GPC['area']['district'],
                );
                if ($id) {
                    M::t('superman_mall_myfetch')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['shopid'] = $this->shop['id'];
                    $new_id = M::t('superman_mall_myfetch')->insert($data);
                    if (!$new_id) {
                        message('数据库操作失败，insert "superman_mall_myfetch" failed！', referer(), 'error');
                    }
                }
                message('操作成功！', $this->createWebUrl('myfetch'), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            M::t('superman_mall_myfetch')->delete(array('id' => $id));
            message('操作成功！', $this->createWebUrl('myfetch'), 'success');
        } else if ($act == 'setting') {
            $nav['subtitle'] = '参数设置';
            $this->check_web_shop();
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MYFETCH_SETTING, $this->shop['id']);
            if (checksubmit()) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MYFETCH_SETTING, $this->shop['id']);
                if ($setting) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        }
        include $this->template('myfetch');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'setting')) ? $_GPC['act'] : 'display';
        $nav['title'] = '自提门店';
        if ($act == 'display') {
            $nav['subtitle'] = '门店列表';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $list = array();
            $filter = array(
                'shopid' => $this->shop['id'],
            );
            if (isset($_GPC['title']) && $_GPC['title'] != '') {
                $filter['title'] = "# LIKE '%{$_GPC['title']}%'";
            }
            if (isset($_GPC['area'])) {
                if ($_GPC['area']['province'] != '') {
                    $filter['province'] = $_GPC['area']['province'];
                }
                if ($_GPC['area']['city'] != '') {
                    $filter['city'] = $_GPC['area']['city'];
                }
                if ($_GPC['area']['district'] != '') {
                    $filter['district'] = $_GPC['area']['district'];
                }
            }
            $total = M::t('superman_mall_myfetch')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_myfetch')->fetchall($filter, 'ORDER BY displayorder DESC', $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
            }
            if ($_GPC['displayorder']) {
                foreach ($_GPC['displayorder'] as $id=>$val) {
                    M::t('superman_mall_myfetch')->update(array('displayorder' => $val), array('id' => $id));
                }
                message('操作成功！', referer(), 'success');
            }
            if ($_GPC['_method'] == 'switch') {
                $id = $_GPC['id'];
                $value = $_GPC['value'];
                $field = $_GPC['field'];
                $data = array(
                    $field => $value,
                );
                $condition = array(
                    'id' => $id,
                );
                M::t('superman_mall_myfetch')->update($data, $condition);
                echo 'success';
                exit;
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            if ($id) {
                $item = M::t('superman_mall_myfetch')->fetch($id);
                if (!$item) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
            }
            if (checksubmit()) {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'title' => $_GPC['title'],
                );
                $row = M::t('superman_mall_myfetch')->fetch($filter);
                if (!$id) { //insert
                    if ($row) {
                        message('门店名称已存在，请重新填写！', referer(), 'error');
                    }
                } else { //update
                    if ($row && $row['id'] != $id) {
                        message('门店名称已存在，请重新填写！', referer(), 'error');
                    }
                }
                $data = array(
                    'displayorder' => $_GPC['displayorder'],
                    'title' => $_GPC['title'],
                    'username' => $_GPC['username'],
                    'mobile' => $_GPC['mobile'],
                    'address' => $_GPC['address'],
                    'isshow' => $_GPC['isshow'],
                    'province' => $_GPC['area']['province'],
                    'city' => $_GPC['area']['city'],
                    'district' => $_GPC['area']['district'],
                );
                if ($id) {
                    M::t('superman_mall_myfetch')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['shopid'] = $this->shop['id'];
                    $new_id = M::t('superman_mall_myfetch')->insert($data);
                    if (!$new_id) {
                        message('数据库操作失败，insert "superman_mall_myfetch" failed！', referer(), 'error');
                    }
                }
                message('操作成功！', $this->createWebUrl('myfetch'), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            M::t('superman_mall_myfetch')->delete(array('id' => $id));
            message('操作成功！', $this->createWebUrl('myfetch'), 'success');
        } else if ($act == 'setting') {
            $nav['subtitle'] = '参数设置';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MYFETCH_SETTING, $this->shop['id']);
            if (checksubmit()) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MYFETCH_SETTING, $this->shop['id']);
                if ($setting) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        }
        include $this->template('myfetch/index');
    }
}

$obj = new Superman_mall_doWebMyfetch;