<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebPlugin extends Superman {
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
        $this->check_user_permission('superman_mall_menu_plugin');
        $act = in_array($_GPC['act'], array('display')) ? $_GPC['act'] : 'display';
        if ($act == 'display') {
            @header('Location: '.$this->createWebUrl('seckill', array('act' => 'display')));
            exit;
        }
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $this->check_user_permission('superman_mall_menu_plugin');
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $list = array(
                'seckill' => $this->check_plugin_permission('seckill', $this->shop['id']),
                'mgroupon' => $this->check_plugin_permission('mgroupon', $this->shop['id']),
                'discount' => $this->check_plugin_permission('discount', $this->shop['id']),
                'printer' => $this->check_plugin_permission('printer', $this->shop['id']),
                'tbast' => $this->check_plugin_permission('tbast', $this->shop['id']),
            );
            //print_r($list);
        }
        include $this->template('plugin/index');
    }
}
$obj = new Superman_mall_doWebPlugin;