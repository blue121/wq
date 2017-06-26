<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
$this->check_user_permission('superman_mall_menu_mobile_admin_finance_log');
if ($act == 'log') {
    $title = '提现记录';
    $pindex = max(1, intval($_GPC['page']));
    $pagesize = 10;
    $start = ($pindex - 1) * $pagesize;
    $filter = array(
        'shopid' => $this->shop['id'],
    );
    $list = M::t('superman_mall_shop_getcash_log')->fetchall($filter, '', $start, $pagesize);
    if ($list) {
        foreach ($list as &$li) {
            $li['status_title'] = SupermanUtil::get_getcash_status_title($li['status']);
            $li['account_type_title'] = SupermanUtil::get_getcash_account_type_title($li['account_type']);
            $li['create_date'] = date('Y-m-d', $li['createtime']);
            $li['account_money'] = $li['money'] - $li['service_fee'];
        }
        unset($li);
    }
    //加载更多
    if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
        die(json_encode($list));
    }
}
include $this->template('finance/index');