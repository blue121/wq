<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebChat extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        }
	}
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $this->check_user_permission('superman_mall_menu_chat_display');
            //TODO
        } else if ($act == 'post') {
            $this->check_user_permission('superman_mall_menu_chat_post');
            //TODO
        }
        include $this->template('chat/index');
    }
}
$obj = new Superman_mall_doWebChat;