<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebPage extends Superman {
    public function __construct() {
        parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_platform');
        $this->exec();
    }

    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete')) ? $_GPC['act'] : 'display';
        if ($act == 'display') {
            //TODO
        } else if ($act == 'post') {
            //TODO
        } else if ($act == 'delete') {
            //TODO
        }
        include $this->template('web/page');
    }
}
$obj = new Superman_mall_doWebPage;