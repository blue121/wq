<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileFollow extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '关注的商品';
        $do = $do?$do:'follow';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $list = array();
            if ($_W['member']['uid']) {
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'status' => 1,
                );
                $follows = M::t('superman_mall_item_follow')->fetchall($filter, '', $start, $pagesize);
                if ($follows) {
                    $itemids = array();
                    foreach ($follows as $follow) {
                        $itemids[] = $follow['itemid'];
                    }
                    $list = M::t('superman_mall_item')->fetchall(array('id' => $itemids));
                }
            }
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($list));
            }
            //print_r($list);
        }
		include $this->template('follow');
    }
}
$obj = new Superman_mall_doMobileFollow;