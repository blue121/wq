<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileCreditlog extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '收支明细';
        $do = $do?$do:'creditlog';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 15;
            $start = ($pindex - 1) * $pagesize;
            $credittype = in_array($_GPC['credittype'], array('credit1', 'credit2', 'credit3', 'credit4', 'credit5'))?$_GPC['credittype']:'credit1';
            $credit_titles = $this->get_credit_titles();
            $list = array();
            if ($_W['member']['uid']) {
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'credittype' => $credittype,

                );
                $list = M::t('mc_credits_record')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$row) {
                        $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
                    }
                    unset($row);
                }
            }
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($list));
            }
        }
        include $this->template('creditlog');
    }
}
$obj = new Superman_mall_doMobileCreditlog;