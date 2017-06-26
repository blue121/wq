<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebDashboard extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_dashboard');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
	public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        $nav['title'] = '首页';
        if ($act == 'display') {
            //TODO
        }
        //include $this->template('dashboard/index');
    }

    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        $nav['title'] = '首页';
        if ($act == 'display') {
            $data = array(
                'income' => '0.00',
                'order' => '0',
                'page_view' => '0',
                'unique_visitor' => '0',
            );
            //收入
            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => array(1, 2, 3, 4),
            );
            $income = M::t('superman_mall_order')->sum($filter, 'price');
            if ($income > 0) {
                //分销佣金
                $commission1 = M::t('superman_mall_order')->sum($filter, 'partner1_commission');
                $commission2 = M::t('superman_mall_order')->sum($filter, 'partner2_commission');
                $commission3 = M::t('superman_mall_order')->sum($filter, 'partner3_commission');
                $com = floatval($commission1) + floatval($commission2) + floatval($commission3);
                $data['income'] = SupermanUtil::float_format($income-$com);
            }
            //订单
            $month_start = strtotime(date('Y-m-1 0:0:0'));
            $month_end = strtotime(date('Y-m-t 23:59:59'));
            $filter = array(
                'shopid' => $this->shop['id'],
                'status' => array(1, 2, 3, 4),
                'createtime#1' => '#>='.$month_start,
                'createtime#2' => '#<='.$month_end,
            );
            $data['order'] = M::t('superman_mall_order')->count($filter);
            //浏览量&访客
            $filter = array(
                'shopid' => $this->shop['id'],
                'daytime' => date('Ymd'),
            );
            $row = M::t('superman_mall_stat')->fetch($filter);
            if ($row) {
                $data['page_view'] = $row['page_view'];
                $data['unique_visitor'] = $row['unique_visitor'];
            }
            //订单图表
            $scroll = intval($_GPC['scroll']);
            $st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime(date('Y-m-1 0:0:0'));
            $et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d 23:59:59'));
            $starttime = min($st, $et);
            $endtime = max($st, $et);
            if ($_W['isajax']) {
                $list = $this->order_list($starttime, $endtime);
                echo json_encode($list);
                exit;
            }
            $getcash_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_GETCASH);
        }
		//include $this->template('dashboard');
		include $this->template('dashboard/index');
    }
    private function order_list($starttime, $endtime) {
        global $_W, $_GPC;
        $list = array();
        for ($i = $starttime; $i <= $endtime; $i += (24*3600)) {
            if ($i == $starttime) {          //每日开始时间戳
                $t1 = $i;
            } else {
                $t1 = strtotime(date('Y-m-d 0:0:0', $i));
            }
            $t2 = strtotime(date('Y-m-d 23:59:59', $i));
            //日期
            $list['label'][] = date('m-d', $t1);
            $filter = array(
                'shopid' => $this->shop['id'],
                'start_time' => $t1,
                'end_time' => $t2,
            );
            //待支付
            $count1 = M::t('superman_mall_order')->count(array(
                'shopid' => $this->shop['id'],
                'status' => 0,
                'createtime#1' => '#>='.$t1,
                'createtime#2' => '#<='.$t2,
            ));
            $list['datasets']['flow1'][] = $count1;
            //待发货
            $count2 = M::t('superman_mall_order')->count(array(
                'shopid' => $this->shop['id'],
                'status' => 1,
                'createtime#1' => '#>='.$t1,
                'createtime#2' => '#<='.$t2,
            ));
            $list['datasets']['flow2'][] = $count2;
            //已发货
            $count3 = M::t('superman_mall_order')->count(array(
                'shopid' => $this->shop['id'],
                'status' => 2,
                'createtime#1' => '#>='.$t1,
                'createtime#2' => '#<='.$t2,
            ));
            $list['datasets']['flow3'][] = $count3;
            //已收货
            $count4 = M::t('superman_mall_order')->count(array(
                'shopid' => $this->shop['id'],
                'status' => 3,
                'createtime#1' => '#>='.$t1,
                'createtime#2' => '#<='.$t2,
            ));
            $list['datasets']['flow4'][] = $count4;
        }
        return $list;
    }
}
$obj = new Superman_mall_doWebDashboard;