<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
//$this->check_user_permission('superman_mall_menu_mobile_admin_dashboard');    TODO
if ($act == 'display') {
    $title = '首页';
    //收入
    $filter = array(
        'shopid' => $this->shop['id'],
        'status' => array(1, 2, 3, 4),
    );
    $income = M::t('superman_mall_order')->sum($filter, 'price');
    if ($income > 0) {
        //收入扣除分销佣金
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
    //pv,uv
    $filter = array(
        'shopid' => $this->shop['id'],
        'daytime' => date('Ymd'),
    );
    $row = M::t('superman_mall_stat')->fetch($filter);
    $data['page_view'] = isset($row['page_view'])?$row['page_view']:0;
    $data['unique_visitor'] = isset($row['unique_visitor'])?$row['unique_visitor']:0;
}
include $this->template('dashboard/index');