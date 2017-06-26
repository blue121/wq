<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileCategory extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '分类';
        $do = $do?$do:'category';
        $act = in_array($_GPC['act'], array('display', 'load'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'isshow' => 1,
            );
            $list = M::t('superman_mall_item_category')->fetchall_recurse($filter, '', 0, -1);
            //print_r($list);die;
        }
        include $this->template('category');
    }
}
$obj = new Superman_mall_doMobileCategory;