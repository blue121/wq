<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebPrinter extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_plugin');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        if (isset($this->shop['id']) && $this->shop['id']) {
            $plugin_permission = $this->check_plugin_permission('printer', $this->shop['id']);
        }
        $act = in_array($_GPC['act'], array('display', 'post', 'log', 'delete'))?$_GPC['act']:'display';
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
            $total = M::t('superman_mall_shop_printer')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_printer')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    //do nothing
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $id = intval($_GPC['id']);
            if ($id) {
                $row = M::t('superman_mall_shop_printer')->fetch($id);
            } else {
                $this->check_web_shop();
            }
            if (checksubmit()) {
                $_data = array(
                    'title' => $_GPC['title'],
                    'provider' => in_array($_GPC['provider'], array('feie', 365))?$_GPC['provider']:'feie',
                    'sn' => $_GPC['sn'],
                    'key' => $_GPC['key'],
                    'print_type' => in_array($_GPC['print_type'], array(1, 2))?$_GPC['print_type']:1,
                    'times' => $_GPC['times'],
                    'qrcode_url' => $_GPC['qrcode_url'],
                    'status' => $_GPC['status']?1:0,
                    'head' => $_GPC['head'],
                    'foot' => $_GPC['foot'],
                );
                if (isset($row) && $row) {  //更新
                    M::t('superman_mall_shop_printer')->update($_data, array('id' => $row['id']));
                } else {    //添加
                    $_data['dateline'] = TIMESTAMP;
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    M::t('superman_mall_shop_printer')->insert($_data);
                }
                message('更新成功！', $this->createWebUrl('printer', array('act' => 'display')), 'success');
            }
        } else if ($act == 'log') {
            if (isset($_GPC['delete']) && $_GPC['delete']) {
                $id = intval($_GPC['delete']);
                M::t('superman_mall_order_print')->delete(array(
                    'uniacid' => $_W['uniacid'],
                    'id' => $id
                ));
                //商户编辑删除打印机和日志需要检查权限
                message('操作成功！', referer(), 'success');
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid']
            );
            if ($this->shop['id']) {
                $filter['shopid'] = $this->shop['id'];
            }
            if ($_GPC['sn']) {
                $filter['printersn'] = $_GPC['sn'];
            }
            $total = M::t('superman_mall_order_print')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_order_print')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $order = M::t('superman_mall_order')->fetch(array('ordersn' => $li['ordersn']));
                        if ($order) {
                            $li['orderid'] = $order['id'];
                        }
                    }
                    unset($li);
                    //do nothing
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
            if ($_GPC['refresh'] == 1) {
                if (isset($list)) {
                    foreach ($list as $li) {
                        if ($li['status'] != 1) {
                            $filter = array(
                                'uniacid' => $li['uniacid'],
                                'shopid' => $li['shopid'],
                                'sn' => $li['printersn'],
                            );
                            $printer = M::t('superman_mall_shop_printer')->fetch($filter);
                            $account = array(
                                'sn' => $printer['sn'],
                                'key' => $printer['key'],
                            );
                            $result = Printer::init($printer)->check_print($li['searchid']);
                            if (isset($result['errno']) && $result['errno'] == 0) {
                                M::t('superman_mall_order_print')->update(array('status' => 1), array('id' => $li['id']));
                            }
                        }
                    }
                }
                message('更新成功！', referer(), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法请求', referer(), 'error');
            }
            M::t('superman_mall_shop_printer')->delete(array(
                'uniacid' => $_W['uniacid'],
                'id' => $id,
            ));
            message('操作成功！', referer(), 'success');
        }
        include $this->template('printer');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $plugin_permission = $this->check_plugin_permission('printer', $this->shop['id']);
        $act = in_array($_GPC['act'], array('display', 'post', 'log', 'delete'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id'],
            );
            $total = M::t('superman_mall_shop_printer')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_printer')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    //do nothing
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $id = intval($_GPC['id']);
            if ($id) {
                $row = M::t('superman_mall_shop_printer')->fetch($id);
                if ($row) {
                    if ($row['shopid'] != $this->shop['id']) {
                        message('非法请求', referer(), 'error');
                    }
                }
            }
            if (checksubmit()) {
                $_data = array(
                    'title' => $_GPC['title'],
                    'provider' => in_array($_GPC['provider'], array('feie', 365))?$_GPC['provider']:'feie',
                    'sn' => $_GPC['sn'],
                    'key' => $_GPC['key'],
                    'print_type' => in_array($_GPC['print_type'], array(1, 2))?$_GPC['print_type']:1,
                    'times' => $_GPC['times'],
                    'qrcode_url' => $_GPC['qrcode_url'],
                    'status' => $_GPC['status']?1:0,
                    'head' => $_GPC['head'],
                    'foot' => $_GPC['foot'],
                );
                if (isset($row) && $row) {  //更新
                    M::t('superman_mall_shop_printer')->update($_data, array('id' => $row['id']));
                } else {    //添加
                    $_data['dateline'] = TIMESTAMP;
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    M::t('superman_mall_shop_printer')->insert($_data);
                }
                message('更新成功！', $this->createWebUrl('printer', array('act' => 'display')), 'success');
            }
        } else if ($act == 'log') {
            if (isset($_GPC['delete']) && $_GPC['delete']) {
                $id = intval($_GPC['delete']);
                M::t('superman_mall_order_print')->delete(array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'id' => $id
                ));
                //商户编辑删除打印机和日志需要检查权限
                message('操作成功！', referer(), 'success');
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id'],
            );
            if ($_GPC['sn']) {
                $filter['printersn'] = $_GPC['sn'];
            }
            $total = M::t('superman_mall_order_print')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_order_print')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $order = M::t('superman_mall_order')->fetch(array('ordersn' => $li['ordersn']));
                        if ($order) {
                            $li['orderid'] = $order['id'];
                        }
                    }
                    unset($li);
                    //do nothing
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
            if ($_GPC['refresh'] == 1) {
                if (isset($list)) {
                    foreach ($list as $li) {
                        if ($li['status'] != 1) {
                            $filter = array(
                                'uniacid' => $li['uniacid'],
                                'shopid' => $li['shopid'],
                                'sn' => $li['printersn'],
                            );
                            $printer = M::t('superman_mall_shop_printer')->fetch($filter);
                            $account = array(
                                'sn' => $printer['sn'],
                                'key' => $printer['key'],
                            );
                            $result = Printer::init($printer)->check_print($li['searchid']);
                            if (isset($result['errno']) && $result['errno'] == 0) {
                                M::t('superman_mall_order_print')->update(array('status' => 1), array('id' => $li['id']));
                            }
                        }
                    }
                }
                message('更新成功！', referer(), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            if ($id <= 0) {
                message('非法请求', referer(), 'error');
            }
            M::t('superman_mall_shop_printer')->delete(array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id'],
                'id' => $id,
            ));
            message('操作成功！', referer(), 'success');
        }
        include $this->template('printer/index');
    }
}
$obj = new Superman_mall_doWebPrinter();