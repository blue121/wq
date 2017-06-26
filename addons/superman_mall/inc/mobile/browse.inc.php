<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileBrowse extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '浏览记录';
        $do = $do?$do:'browse';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        $this->checkauth();
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $list = array();
            if ($_W['member']['uid']) {
                $filter = array(
                    'uid' => $_W['member']['uid'],
                );
                $list = M::t('superman_mall_item_browse')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['item'] = M::t('superman_mall_item')->fetch($li['itemid']);
                    }
                    unset($li);
                }
            }
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($list));
            }
            //print_r($list);die;
        }
		include $this->template('browse');
    }
}
$obj = new Superman_mall_doMobileBrowse;