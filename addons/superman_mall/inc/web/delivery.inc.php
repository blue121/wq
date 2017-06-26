<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebDelivery extends Superman {
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
        $act = in_array($_GPC['act'], array('display', 'post', 'delete'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid']
            );
            if ($this->shop['id']) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_shop_delivery')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_delivery')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    //do nothing
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $id = intval($_GPC['id']);
            if ($id) {
                $row = M::t('superman_mall_shop_delivery')->fetch($id);
            } else {
                $this->check_web_shop();
            }
            if (checksubmit()) {
                $_data = array(
                    'content' => $_GPC['content'],
                );
                if (isset($row) && $row) {  //更新
                    M::t('superman_mall_shop_delivery')->update($_data, array('id' => $row['id']));
                } else {    //添加
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    M::t('superman_mall_shop_delivery')->insert($_data);
                }
                message('更新成功！', $this->createWebUrl('delivery', array('act' => 'display')), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法请求', referer(), 'error');
            }
            M::t('superman_mall_shop_delivery')->delete(array(
                'uniacid' => $_W['uniacid'],
                'id' => $id,
            ));
            message('操作成功！', referer(), 'success');
        }
        include $this->template('delivery');
    }

    public function do_shop_admin() {
        global $_W, $_GPC;
        $plugin_permission = $this->check_plugin_permission('printer', $this->shop['id']);
        $act = in_array($_GPC['act'], array('display', 'post', 'delete'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id'],
            );
            $total = M::t('superman_mall_shop_delivery')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_delivery')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    //do nothing
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $id = intval($_GPC['id']);
            if ($id) {
                $row = M::t('superman_mall_shop_delivery')->fetch($id);
                if ($row) {
                    if ($row['shopid'] != $this->shop['id']) {
                        message('非法请求', referer(), 'error');
                    }
                }
            }
            if (checksubmit()) {
                $_data = array(
                    'content' => $_GPC['content'],
                );
                if (isset($row) && $row) {  //更新
                    M::t('superman_mall_shop_delivery')->update($_data, array('id' => $row['id']));
                } else {    //添加
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    M::t('superman_mall_shop_delivery')->insert($_data);
                }
                message('更新成功！', $this->createWebUrl('delivery', array('act' => 'display')), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法请求', referer(), 'error');
            }
            M::t('superman_mall_shop_delivery')->delete(array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id'],
                'id' => $id,
            ));
            message('操作成功！', referer(), 'success');

        }
        include $this->template('delivery/index');
    }
}
$obj = new Superman_mall_doWebDelivery();