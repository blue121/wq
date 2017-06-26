<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebPartner extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_partner');
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('overview', 'display', 'group',
            'commission', 'getcash', 'setting', 'ranking', 'poster')) ? $_GPC['act'] : 'overview';
        $nav['title'] = '分销管理';
        if ($act == 'overview') {
            $nav['subtitle'] = '分销概况';
            //总人数
            $filter = array(
                'uniacid' => $_W['uniacid']
            );
            $all_count = M::t('superman_mall_partner')->count($filter);
            //一级
            $filter['position'] = 1;
            $downline1_count = M::t('superman_mall_partner')->count($filter);
            //二级
            $filter['position'] = 2;
            $downline2_count = M::t('superman_mall_partner')->count($filter);
            //三级
            $filter['position'] = 3;
            $downline3_count = M::t('superman_mall_partner')->count($filter);
            //其他
            $filter['position'] = '# >3';
            $other_count = M::t('superman_mall_partner')->count($filter);

            //图标模拟数据
            $scroll = intval($_GPC['scroll']);
            $st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime('-30day');
            $et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d 23:59:59'));
            $starttime = min($st, $et);
            $endtime = max($st, $et);
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

                //分销商
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'createtime#1' => '#>='.$t1,
                    'createtime#2' => '#<='.$t2,
                );
                $count1 = M::t('superman_mall_partner')->count($filter);
                $list['datasets']['flow1'][] = $count1;
            }
            if ($_W['isajax']) {
                echo json_encode($list);
                exit;
            }
        } else if ($act == 'display') {
            $nav['subtitle'] = '分销商';
            $op = in_array($_GPC['op'], array('display', 'post', 'delete')) ? $_GPC['op'] : 'display';
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'uniacid' => $_W['uniacid']
                );
                //筛选
                if (isset($_GPC['groupid']) && $_GPC['groupid']) {
                    $filter['groupid'] = intval($_GPC['groupid']);
                }
                if (isset($_GPC['status']) && $_GPC['status']) {
                    $filter['status'] = $_GPC['status'];
                }
                if (isset($_GPC['nickname']) && $_GPC['nickname']) {
                    if (is_numeric($_GPC['nickname'])) {    //分销商id
                        $filter['id'] = $_GPC['nickname'];
                    } else {    //昵称
                        $sql = "SELECT a.id FROM ".tablename('superman_mall_partner')." AS a,".tablename('mc_members')." AS b";
                        $sql .= ' WHERE a.uid=b.uid AND a.uniacid=:uniacid AND b.nickname LIKE "%'.$_GPC['nickname'].'%"';
                        $params = array(
                            ':uniacid' => $_W['uniacid'],
                        );
                        $col = pdo_fetchall($sql, $params, 'id');
                        if ($col) {
                            $filter['id'] = array_keys($col);
                        }
                    }
                }
                if (isset($_GPC['recommend']) && $_GPC['recommend']) {
                    if (is_numeric($_GPC['recommend'])) {    //分销商id
                        $filter['recommendid'] = $_GPC['recommend'];
                    } else {    //昵称
                        $sql = "SELECT a.id FROM ".tablename('superman_mall_partner')." AS a,".tablename('mc_members')." AS b";
                        $sql .= ' WHERE a.uniacid=:uniacid AND a.uid=b.uid AND b.nickname LIKE "%'.$_GPC['recommend'].'%"';
                        $params = array(
                            ':uniacid' => $_W['uniacid'],
                        );
                        $col = pdo_fetchall($sql, $params, 'id');
                        if ($col) {
                            $filter['recommendid'] = array_keys($col);
                        }
                    }
                }
                //分销等级
                $group_list = M::t('superman_mall_partner_group')->fetchall(array('uniacid' => $_W['uniacid']), '', 0, -1, 'id');
                $total = M::t('superman_mall_partner')->count($filter);
                if ($total) {
                    $orderby = ' ORDER BY id DESC';
                    $list = M::t('superman_mall_partner')->fetchall($filter, $orderby, $start, $pagesize);
                    if ($list) {
                        foreach ($list as &$li) {
                            $member = mc_fetch($li['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                            $li['nickname'] = $member['nickname'];
                            $li['realname'] = $member['realname'];
                            $li['avatar'] = $member['avatar'];
                            $li['mobile'] = $member['mobile'];
                            if ($li['recommendid'] > 0) {
                                $recommend = M::t('superman_mall_partner')->fetch($li['recommendid']);
                                $li['recommend'] = mc_fetch($recommend['uid'], array('nickname', 'avatar'));
                            }
                            unset($li, $member, $recommend);
                        }

                    }
                    $pager = pagination($total, $pindex, $pagesize);
                }
            } else if ($op == 'post') {
                $nav['subtitle'] = '编辑';
                $id = intval($_GPC['id']);
                if ($id > 0) {
                    $row = M::t('superman_mall_partner')->fetch($id);
                    $member = mc_fetch($row['uid'], array('nickname', 'avatar', 'realname', 'mobile'));
                    if ($row['recommendid']) {
                        $recommend = M::t('superman_mall_partner')->fetch($row['recommendid']);
                        $recommend_member = mc_fetch($recommend['uid'], array('avatar', 'nickname'));
                    }
                }
                //分销等级
                $group_list = M::t('superman_mall_partner_group')->fetchall(array('uniacid' => $_W['uniacid']), '', 0, -1);
                if (checksubmit()) {
                    //更改状态发送模版消息
                    $_data = array(
                        'groupid' => $_GPC['group'],
                        'status' => $_GPC['status'],
                    );
                    M::t('superman_mall_partner')->update($_data, array('id' => $id));
                    if (isset($_GPC['send_template_message']) && $_GPC['send_template_message'] == 'on') {
                        //状态变更
                        if (isset($row) && $_GPC['status'] != $row['status']) {
                            $this->send_partner_manage_tmplmsg($row, $_GPC['status'], array(
                                'url' => murl('entry', array(
                                    'do' => 'partner',
                                    'm' => SUPERMAN_MODULE_NAME,
                                ), true, true),
                            ));
                        }
                    }
                    $_member_data = array(
                        'realname' => $_GPC['realname'],
                        'mobile' => $_GPC['mobile']
                    );
                    mc_update($row['uid'], $_member_data);
                    message('操作成功！', $this->createWebUrl('partner', array('act' => 'display', 'op' => 'display')), 'success');
                }
            } else if ($op == 'delete') {
                $id = intval($_GPC['id']);
                if ($id < 0) {
                    message('非法请求',referer(), 'error');
                }
                $partner = M::t('superman_mall_partner')->fetch($id);
                $rec_id = $partner['recommendid'];
                for ($i = 1; $i <= 3; $i++) {
                    if ($rec_id == 0) {
                        break;
                    }
                    M::t('superman_mall_partner')->decrement(array('downline'.$i => 1), array('id' => $rec_id));
                    $recommend = M::t('superman_mall_partner')->fetch(array('id' => $rec_id));
                    $rec_id = $recommend['recommendid'];
                }
                M::t('superman_mall_partner')->update(array('recommendid' => 0), array('recommendid' => $id));
                M::t('superman_mall_order')->delete(array('partner1_id' => $id));
                M::t('superman_mall_order')->delete(array('partner2_id' => $id));
                M::t('superman_mall_order')->delete(array('partner3_id' => $id));
                M::t('superman_mall_partner')->delete(array('id' => $id));
                M::t('superman_mall_partner_stat')->delete(array('partnerid' => $id));
                M::t('superman_mall_partner_getcash_log')->delete(array('partnerid' => $id));
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'group') {
            $nav['subtitle'] = '分销等级';
            $op = in_array($_GPC['op'], array('display', 'post', 'delete')) ? $_GPC['op'] : 'display';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
            $group_condition_title = SupermanUtil::get_partner_group_condition_title($setting['partner']['group_condition']?$setting['partner']['group_condition']:1);
            if ($op == 'display') {
                $filter = array(
                    'uniacid' => $_W['uniacid']
                );
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $orderby = ' ORDER BY `id` DESC';
                $total = M::t('superman_mall_partner_group')->count($filter);
                if ($total) {
                    $list = M::t('superman_mall_partner_group')->fetchall($filter, $orderby, $start, $pagesize);
                    if ($list) {
                        $pager = pagination($total, $pindex, $pagesize);
                    }
                }
            } else if ($op == 'post') {
                $nav['subtitle'] = '编辑';
                $id = intval($_GPC['id']);
                if ($id) {
                    $item = M::t('superman_mall_partner_group')->fetch($id);
                }
                if (checksubmit()) {
                    $title = $_GPC['title'];
                    if ($title == '') {
                        message('分销等级名称为空', referer(), 'error');
                    }
                    $data = array(
                        'title' => $title,
                        'rate1' => $_GPC['rate1'],
                        'rate2' => $_GPC['rate2'],
                        'rate3' => $_GPC['rate3'],
                        'condition' => $_GPC['condition'],
                    );
                    if ($id) {
                        M::t('superman_mall_partner_group')->update($data, array('id' => $id));
                    } else {
                        $data['uniacid'] = $_W['uniacid'];
                        $data['createtime'] = TIMESTAMP;
                        M::t('superman_mall_partner_group')->insert($data);
                    }
                    message('操作成功！', $this->createWebUrl('partner', array('act' => 'group', 'op' => 'display')), 'success');
                }
            } else if ($op == 'delete') {
                $id = intval($_GPC['id']);
                if ($id <= 0 ) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
                M::t('superman_mall_partner_group')->delete(array('id' => $id));
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'commission') {
            $nav['subtitle'] = '佣金管理';
            $op = in_array($_GPC['op'], array('display', 'get_item', 'delete')) ? $_GPC['op'] : 'display';
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => '# >0',     //非父订单
                    'status' => 4,
                    'partner1_id' => '# >0',
                );
                //筛选
                $commission_status = in_array($_GPC['commission_status'], array('0', '1'))?$_GPC['commission_status']:'all';
                if ($commission_status != 'all') {
                    $filter['commission_status'] = $commission_status;
                }
                if (isset($_GPC['partnerid']) && $_GPC['partnerid']) {
                    $filter['partner1_id'] = $_GPC['partnerid'];
                }
                if (isset($_GPC['nickname']) && $_GPC['nickname']) {
                    if (is_numeric($_GPC['nickname'])) {    //分销商id
                        $filter['partner1_id'] = $_GPC['nickname'];
                    } else {    //昵称
                        $sql = "SELECT a.id FROM ".tablename('superman_mall_partner')." AS a,".tablename('mc_members')." AS b";
                        $sql .= ' WHERE a.uid=b.uid AND a.uniacid=:uniacid AND b.nickname LIKE "%'.$_GPC['nickname'].'%"';
                        $params = array(
                            ':uniacid' => $_W['uniacid'],
                        );
                        $col = pdo_fetchall($sql, $params, 'id');
                        if ($col) {
                            $col = array_keys($col);
                            $filter['partner1_id'] = $col;
                        } else {
                            //没有匹配的昵称时返回空
                            $filter['partner1_id'] = -1;
                        }
                    }
                }
                if (isset($_GPC['ordersn']) && $_GPC['ordersn']) {
                    $filter['ordersn'] = '# LIKE "%'.$_GPC['ordersn'].'%"';
                }

                $total = M::t('superman_mall_order')->count($filter);
                if ($total) {
                    $orderby = ' ORDER BY id DESC';
                    $list = M::t('superman_mall_order')->fetchall($filter, $orderby, $start, $pagesize, 'id');
                    if ($list) {
                        foreach ($list as &$li) {
                            $li['user'] = mc_fetch($li['uid'], array('nickname', 'avatar'));
                            $partner = M::t('superman_mall_partner')->fetch($li['partner1_id']);
                            $li['partner'] = mc_fetch($partner['uid'], array('nickname', 'avatar'));
                            unset($li);
                        }

                    }
                    $pager = pagination($total, $pindex, $pagesize);
                }
                /*//修改佣金
                if (checksubmit('commission_edit')) {
                    if (is_array($_GPC['partner1_commission'])) {
                        $commission_p1 = $commission_p2 = $commission_p3 = 0;
                        foreach ($_GPC['partner1_commission'] as $oiid => $partner1_commission) {
                            $arr = array(
                                'partner1_commission' => $partner1_commission,
                                'partner2_commission' => isset($_GPC['partner2_commission'][$oiid])?$_GPC['partner2_commission'][$oiid]:0,
                                'partner3_commission' => isset($_GPC['partner3_commission'][$oiid])?$_GPC['partner3_commission'][$oiid]:0,
                            );
                            M::t('superman_mall_order_item')->update($arr, array('id' => $oiid));
                            //计算各级佣金
                            $commission_p1 += $_GPC['partner1_commission'][$oiid];
                            $commission_p2 += $_GPC['partner2_commission'][$oiid];
                            $commission_p3 += $_GPC['partner3_commission'][$oiid];
                        }
                        $_data = array(
                            'partner1_commission' => $commission_p1,
                            'partner2_commission' => $commission_p2,
                            'partner3_commission' => $commission_p3,
                        );
                        M::t('superman_mall_order')->update($_data, array('id' => $_GPC['orderid']));
                        message('操作成功！', referer(), 'success');
                    }
                    message('非法请求！', referer(), 'error');
                }*/
                //批量操作
                if (checksubmit('batch_action')) {
                    $ids = $_GPC['ids'];
                    $action = $_GPC['batch_action'];
                    if ($ids && in_array($action, array('settlement', 'delete'))) {
                        if ($action == 'settlement') {
                            foreach ($ids as $id) {
                                //更改结算状态
                                $ret = M::t('superman_mall_order')->update(array('commission_status' => 1), array('id' => $id));
                                if (isset($list[$id]['partner1_id']) && $list[$id]['partner1_id'] > 0) {
                                    //income calculation
                                    $partner1_commission = M::t('superman_mall_order_item')->sum(array('orderid' => $id), 'partner1_commission');
                                    //update table:partner(commission_total, commission_balance)
                                    if ($partner1_commission > 0) {
                                        $ret1 = M::t('superman_mall_partner')->increment(array('commission_total' => $partner1_commission, 'commission_balance' => $partner1_commission), array('id' => $list[$id]['partner1_id']));
                                        //update table:partner_stat
                                        $condition = array(
                                            'uniacid' => $_W['uniacid'],
                                            'partnerid' => $list[$id]['partner1_id'],
                                            'daytime' => date('Ymd')
                                        );
                                        $stat = M::t('superman_mall_partner_stat')->fetch($condition);
                                        if ($stat) {
                                            M::t('superman_mall_partner_stat')->increment(array('commission_total' => $partner1_commission), array('id' => $stat['id']));
                                        } else {
                                            $condition['type'] = 1;
                                            $condition['commission_total'] = $partner1_commission;
                                            M::t('superman_mall_partner_stat')->insert($condition);
                                        }
                                    }
                                }
                                if (isset($list[$id]['partner2_id']) && $list[$id]['partner2_id'] > 0) {
                                    //income calculation
                                    $partner2_commission = M::t('superman_mall_order_item')->sum(array('orderid' => $id), 'partner2_commission');
                                    //update table:partner(commission_total, commission_balance)
                                    if ($partner2_commission > 0) {
                                        $ret2 = M::t('superman_mall_partner')->increment(array('commission_total' => $partner2_commission, 'commission_balance' => $partner2_commission), array('id' => $list[$id]['partner2_id']));
                                        //update table:partner_stat
                                        $condition = array(
                                            'uniacid' => $_W['uniacid'],
                                            'partnerid' => $list[$id]['partner2_id'],
                                            'daytime' => date('Ymd')
                                        );
                                        $stat = M::t('superman_mall_partner_stat')->fetch($condition);
                                        if ($stat) {
                                            M::t('superman_mall_partner_stat')->increment(array('commission_total' => $partner2_commission), array('id' => $stat['id']));
                                        } else {
                                            $condition['type'] = 1;
                                            $condition['commission_total'] = $partner2_commission;
                                            M::t('superman_mall_partner_stat')->insert($condition);
                                        }
                                    }
                                }
                                if (isset($list[$id]['partner3_id']) && $list[$id]['partner3_id'] > 0) {
                                    //income calculation
                                    $partner3_commission = M::t('superman_mall_order_item')->sum(array('orderid' => $id), 'partner3_commission');
                                    //update table:partner(commission_total, commission_balance)
                                    if ($partner3_commission > 0) {
                                        $ret3 = M::t('superman_mall_partner')->increment(array('commission_total' => $partner3_commission, 'commission_balance' => $partner3_commission), array('id' => $list[$id]['partner3_id']));
                                        //update table:partner_stat
                                        $condition = array(
                                            'uniacid' => $_W['uniacid'],
                                            'partnerid' => $list[$id]['partner3_id'],
                                            'daytime' => date('Ymd')
                                        );
                                        $stat = M::t('superman_mall_partner_stat')->fetch($condition);
                                        if ($stat) {
                                            M::t('superman_mall_partner_stat')->increment(array('commission_total' => $partner3_commission), array('id' => $stat['id']));
                                        } else {
                                            $condition['type'] = 1;
                                            $condition['commission_total'] = $partner3_commission;
                                            M::t('superman_mall_partner_stat')->insert($condition);
                                        }
                                    }
                                }
                            }
                        } else {
                            $condition = array(
                                'id' => $ids
                            );
                            M::t('superman_mall_order')->update(array('partner1_id' => 0), $condition);
                        }
                        message('操作成功！', referer(), 'success');
                    }
                    message('非法请求', referer(), 'error');
                }
            } else if ($op == 'delete') {
                $id = intval($_GPC['id']);
                if ($id <= 0 ) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
                M::t('superman_mall_order')->update(array('partner1_id' => 0), array('id' => $id));
                message('操作成功！', referer(), 'success');
            } else if ($op == 'get_item') {
                $id = intval($_GPC['id']);
                if ($id <= 0) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $order = M::t('superman_mall_order')->fetch($id);
                if (!$order) {
                    $this->json(ERRNO::ORDER_NOT_EXIST);
                }
                //一二三级分销商资料
                if ($order['partner1_id'] > 0) {
                    $partner1 = M::t('superman_mall_partner')->fetch($order['partner1_id']);
                }
                if ($order['partner2_id'] > 0) {
                    $partner2 = M::t('superman_mall_partner')->fetch($order['partner2_id']);
                }
                if ($order['partner3_id'] > 0) {
                    $partner3 = M::t('superman_mall_partner')->fetch($order['partner3_id']);
                }
                $list = M::t('superman_mall_order_item')->fetchall(array('orderid' => $id));
                if ($list) {
                    foreach ($list as &$li) {
                        $li['cover'] = tomedia($li['cover']);
                        if (isset($partner1) && $partner1) {
                            $li['partner1_id'] = $partner1['id'];
                            $li['partner1'] = mc_fetch($partner1['uid'], array('nickname', 'avatar'));
                        }
                        if (isset($partner2) && $partner2) {
                            $li['partner2_id'] = $partner2['id'];
                            $li['partner2'] = mc_fetch($partner2['uid'], array('nickname', 'avatar'));
                        }
                        if (isset($partner3) && $partner3) {
                            $li['partner3_id'] = $partner3['id'];
                            $li['partner3'] = mc_fetch($partner3['uid'], array('nickname', 'avatar'));
                        }
                    }
                }
                if ($_W['isajax']) {
                    die(json_encode($list));
                }
            }
        } else if ($act == 'getcash') {
            $nav['subtitle'] = '提现管理';
            $op = in_array($_GPC['op'], array('display', 'post', 'delete')) ? $_GPC['op'] : 'display';
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = isset($_GPC['export'])?-1:20;
                $start = ($pindex - 1) * $pagesize;
                $orderby = ' ORDER BY createtime DESC';
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                );
                $status = in_array($_GPC['status'], array('-2', '-1', '0', '1', '2'))?$_GPC['status']:'all';
                if ($status != 'all') {
                    $filter['status'] = $status;
                }
                if (isset($_GPC['nickname']) && $_GPC['nickname']) {
                    if (is_numeric($_GPC['nickname'])) {    //分销商id
                        $filter['partnerid'] = $_GPC['nickname'];
                    } else {    //昵称
                        $sql = "SELECT a.id FROM ".tablename('superman_mall_partner')." AS a,".tablename('mc_members')." AS b";
                        $sql .= ' WHERE a.uid=b.uid AND a.uniacid=:uniacid AND b.nickname LIKE "%'.$_GPC['nickname'].'%"';
                        $params = array(
                            ':uniacid' => $_W['uniacid'],
                        );
                        $col = pdo_fetchall($sql, $params, 'id');
                        if ($col) {
                            $col = array_keys($col);
                            $filter['partnerid'] = $col;
                        } else {
                            //没有匹配的昵称时返回空
                            $filter['partnerid'] = -1;
                        }
                    }
                }
                $total = M::t('superman_mall_partner_getcash_log')->count($filter);
                if ($total) {
                    $list = M::t('superman_mall_partner_getcash_log')->fetchall($filter, $orderby, $start, $pagesize, 'id');
                    if ($list) {
                        foreach ($list as $key => &$value) {
                            $value['partner'] = M::t('superman_mall_partner')->fetch(array('id' => $value['partnerid']));
                            $member = mc_fetch($value['partner']['uid'], array('nickname', 'avatar'));
                            $value['nickname'] = $member['nickname'];
                            $value['avatar'] = $member['avatar'];
                            if ($value['account_type'] == 'wechat') {
                                $value['account_title'] = '微信';
                            } else if ($value['account_type'] == 'alipay') {
                                $value['account_title'] = '支付宝';
                            } else if ($value['account_type'] == 'bank') {
                                $value['account_title'] = '银行';
                            }
                            if ($value['status'] == 0) {
                                $value['status_title'] = '待审核';
                            } else if ($value['status'] == -3) {
                                $value['status_title'] = '已归还';
                            } else if ($value['status'] == -2) {
                                $value['status_title'] = '付款失败';
                            } else if ($value['status'] == -1) {
                                $value['status_title'] = '审核失败';
                            } else if ($value['status'] == 1) {
                                $value['status_title'] = '审核通过';
                            } else if ($value['status'] == 2) {
                                $value['status_title'] = '已付款';
                            }
                        }
                        unset($value);
                    }
                    $pager = pagination($total, $pindex, $pagesize);
                }
            } else if ($op == 'post') {
                $nav['subtitle'] = '编辑';
                $id = intval($_GPC['id']);
                if ($id <= 0 ) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
                $row = M::t('superman_mall_partner_getcash_log')->fetch(array('id' => $id));
                if ($row) {
                    $row['partner'] = M::t('superman_mall_partner')->fetch(array('id' => $row['partnerid']));
                    $member = mc_fetch($row['partner']['uid'], array('nickname', 'avatar'));
                    $row['nickname'] = $member['nickname'];
                    $row['avatar'] = $member['avatar'];
                }
                //更改状态
                if (checksubmit('submit')) {
                    $data = array(
                        'status' => $_GPC['status'],
                    );
                    M::t('superman_mall_partner_getcash_log')->update($data, array(
                        'id' => $id,
                    ));
                    message('操作成功！', $this->createWebUrl('partner', array('act' => 'getcash', 'op' => 'display')), 'success');
                }

                //归还提现金额
                if (checksubmit('btn_return')) {
                    if ($row['status'] < 2 && $row['status'] > -3) {
                        M::t('superman_mall_partner')->increment(array('commission_balance' => $row['money']), array('id' => $row['partnerid']));
                        M::t('superman_mall_partner')->decrement(array('commission_received' => $row['money']), array('id' => $row['partnerid']));
                        M::t('superman_mall_partner_getcash_log')->update(array(
                            'status' => -3,
                            'operator' => $_W['user']['username']
                        ), array('id' => $row['id']));
                    }
                    message('操作成功！', referer(), 'success');
                }

                //微信支付
                if (checksubmit('btn_wxpay')) { //微信支付
                    if ($_GPC['status'] != 1 && $row['status'] != 0) {
                        message('该记录没有审核通过，无法支付', referer(), 'error');
                    }
                    if ($row['account_type'] != 'wechat') {
                        message('非法请求', referer(), 'error');
                    }
                    $row['account'] = iunserializer($row['account']);
                    $appid = $_W['account']['key'];
                    $setting = uni_setting($_W['uniacid'], array('payment'));
                    $pay = $setting['payment'];
                    if (empty($pay)) {
                        message('请配置和开启微信支付', url('profile/payment'), 'error');
                    }
                    $mchid = $pay['wechat']['mchid'];
                    if (empty($mchid)) {
                        message('微信支付商户号(MchId)参数未设置', url('profile/module/setting', array('m' => 'superman_mall')), 'error');
                    }
                    $paycert = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                    if (empty($paycert) || empty($paycert['apiclient_cert']) || empty($paycert['apiclient_key']) || empty($paycert['rootca'])) {
                        message('微信支付证书未设置', $this->createWebUrl('platform', array('act' => 'paycert')), 'error');
                    }
                    $orderno = $row['orderno'];
                    $openid = $row['account']['openid'];
                    $money = $row['money'];
                    $params = array(
                        'mch_appid' => $appid,
                        'mchid' => $mchid,
                        'nonce_str' => random(32),
                        'partner_trade_no' => $orderno,
                        'openid' => $openid,
                        'check_name' => 'NO_CHECK',
                        're_user_name' => '',
                        'amount' => $money,
                        'desc' => '分销提现'.date('Ymd'),
                        'spbill_create_ip' => CLIENT_IP,
                    );
                    $extra = array();
                    $extra['sign_key'] = $pay['wechat']['signkey'];
                    $path = SupermanUtil::attachment_path();
                    $extra['apiclient_cert'] = $path.$paycert['apiclient_cert'];
                    $extra['apiclient_key'] = $path.$paycert['apiclient_key'];
                    $extra['rootca'] = $path.$paycert['rootca'];
                    $ret = WxpayAPI::pay($params, $extra);
                    //平台触发器
                    $params = array(
                        'action' => 'partner_getcash_pay',
                        'uniacid' => $_W['uniacid'],
                    );
                    Trigger::init('platform')->send($params);
                    if (is_array($ret) && isset($ret['success'])) {
                        $data = array(
                            'status' => 2,
                            'wxpay_result' => is_array($ret)?implode("\n", $ret):$ret,
                            'wxpay_paymentno' => $ret['payment_no'],
                            'operator' => $_W['user']['username'],
                            'remark' => $_GPC['remark'],
                            'paytime' => strtotime($ret['payment_time']),
                            'updatetime' => TIMESTAMP,
                        );
                        M::t('superman_mall_partner_getcash_log')->update($data, array('id' => $id));
                        message('付款成功', referer(), 'success');
                    } else {
                        $data = array(
                            'status' => -2, //付款失败
                            'wxpay_result' => is_array($ret)?implode("\n", $ret):$ret,
                            'operator' => $_W['user']['username'],
                            'remark' => $_GPC['remark'],
                            'updatetime' => TIMESTAMP,
                        );
                        M::t('superman_mall_partner_getcash_log')->update($data, array('id' => $id));
                        message('付款失败，请查看微信付款结果信息', referer(), 'error');
                    }
                }
            } else if ($op == 'delete') {
                $id = intval($_GPC['id']);
                if ($id <= 0 ) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
                $stat = M::t('superman_mall_partner_getcash_log')->fetch($id);
                if (isset($stat['status']) && $stat['status'] < 2) {    //未打款前删除都返还
                    M::t('superman_mall_partner')->increment(array('commission_balance' => $stat['money']), array('id' => $stat['partnerid']));
                    M::t('superman_mall_partner')->decrement(array('commission_received' => $stat['money']), array('id' => $stat['partnerid']));
                }
                M::t('superman_mall_partner_getcash_log')->delete(array('id' => $id));
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'setting') {
            $nav['subtitle'] = '参数设置';
            $group_list = M::t('superman_mall_partner_group')->fetchall(array('uniacid' => $_W['uniacid']), '', 0, -1, 'id');
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
            $group_condition_titles = SupermanUtil::get_partner_group_condition_title();
            if (checksubmit('submit')) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PARTNER_SETTING);
                if ($setting) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'ranking') {
            $nav['subtitle'] = '佣金排行';
            $op = in_array($_GPC['op'], array('display_partner', 'post_partner', 'delete_partner',
                'display_commission', 'post_commission', 'delete_commission')) ? $_GPC['op'] : 'display_partner';
            if ($op == 'display_partner') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 20;
                $start = ($pindex - 1) * $pagesize;
                $type = in_array($_GPC['type'], array('week', 'month')) ? $_GPC['type'] : 'week';
                if ($type == 'week') {
                    $starttime = date('Ymd', strtotime('-1 week'));
                    $endtime = date('Ymd');
                }
                if ($type == 'month') {
                    $starttime = date('Ymd', strtotime('-1 months'));
                    $endtime = date('Ymd');
                }
                $params = array(
                    ':uniacid' => $_W['uniacid'],
                    ':starttime' => $starttime,
                    ':endtime' => $endtime,
                );
                $sql = "SELECT COUNT(DISTINCT partnerid,type) FROM ".tablename('superman_mall_partner_stat');
                $sql .= " WHERE uniacid=:uniacid AND daytime>=:starttime AND daytime<=:endtime";
                $total = pdo_fetchcolumn($sql, $params);
                if ($total) {
                    $where = " WHERE uniacid=:uniacid AND daytime>=:starttime AND daytime<=:endtime GROUP BY partnerid,type";
                    $sql = "SELECT partnerid,type,SUM(commission_total) AS commission FROM ".tablename('superman_mall_partner_stat')." {$where}";
                    $sql .= " ORDER BY commission DESC LIMIT $start, $pagesize";
                    $list = pdo_fetchall($sql, $params);
                    if ($list) {
                        foreach ($list as $key => &$value) {
                            $value['ranking'] = $key + 1;
                            if ($value['type'] == 1) {
                                $partner_info = M::t('superman_mall_partner')->fetch($value['partnerid']);
                                $value['uid'] = $partner_info['uid'];
                                $member = mc_fetch($value['uid'], array('nickname', 'avatar'));
                                $value['nickname'] = $member['nickname'];
                                $value['avatar'] = $member['avatar'];
                            } else {
                                $value['partner_info'] = M::t('superman_mall_partner_virtual')->fetch($value['partnerid']);
                                $value['nickname'] = $value['partner_info']['nickname'];
                                $value['avatar'] = $value['partner_info']['avatar'];
                            }
                        }
                        unset($value);
                    }
                    $pager = pagination($total, $pindex, $pagesize);
                }
            } else if ($op == 'post_partner') {
                $nav['subtitle'] = '编辑';
                if ($_GPC['id'] > 0) {
                    $item = M::t('superman_mall_partner_virtual')->fetch(array('id' => $_GPC['id']));
                }
                if (checksubmit('submit')) {
                    $nickname = $_GPC['nickname'];
                    if ($nickname == '') {
                        message('分销商昵称为空，请重新填写！', referer(), 'error');
                    }
                    $data = array(
                        'nickname' => $nickname,
                        'avatar' => $_GPC['avatar'],
                    );
                    if (empty($_GPC['id'])) {
                        $new_id = M::t('superman_mall_partner_virtual')->insert($data);
                        $_data = array(
                            'uniacid' => $_W['uniacid'],
                            'partnerid' => $new_id,
                            'type' => 0,
                            'daytime' => date('Ymd'),
                        );
                        M::t('superman_mall_partner_stat')->insert($_data);
                    } else {
                        if($_GPC['id'] > 0) {
                            M::t('superman_mall_partner_virtual')->update($data, array('id' => $_GPC['id']));
                        } else {
                            message('数据不存在或已删除！', referer(), 'error');
                        }
                    }
                    message('操作成功！', $this->createWebUrl('partner', array('act' => 'ranking','op' => 'display_partner')));
                }
            } else if ($op == 'delete_partner') {
                $id = intval($_GPC['id']);
                if ($id <= 0 ) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
                M::t('superman_mall_partner_virtual')->delete(array('id' => $id));
                M::t('superman_mall_partner_stat')->delete(array('partnerid' => $id, 'type' => 0));
                message('操作成功！', referer(), 'success');
            } else if ($op == 'display_commission') {
                $params = array(
                    ':uniacid' => $_W['uniacid'],
                    ':partnerid' => $_GPC['partnerid'],

                );
                $sql = "SELECT SUM(commission_total) AS commission FROM ".tablename('superman_mall_partner_stat');
                $sql .= " WHERE uniacid=:uniacid AND partnerid=:partnerid AND type=0 AND daytime>=:starttime AND daytime<=:endtime ";
                $params[':starttime'] = date('Ymd', strtotime('-1 week'));
                $params[':endtime'] = date('Ymd');
                $week_commission = pdo_fetch($sql, $params);
                $params[':starttime'] = date('Ymd', strtotime('-1 months'));
                $params[':endtime'] = date('Ymd');
                $month_commission = pdo_fetch($sql, $params);
                $id = $_GPC['partnerid'];
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'partnerid' => $id,
                    'type' => 0
                );
                $total = M::t('superman_mall_partner_stat')->count($filter);
                if ($total) {
                    $list = M::t('superman_mall_partner_stat')->fetchall($filter, 'ORDER BY daytime DESC', $start, $pagesize);
                    if ($list) {
                        foreach ($list as $key => &$value) {
                            $value['partner_info'] = M::t('superman_mall_partner_virtual')->fetch($value['partnerid']);
                            $value['nickname'] = $value['partner_info']['nickname'];
                            $value['avatar'] = $value['partner_info']['avatar'];
                            $value['daytime'] = SupermanUtil::format_date($value['daytime']);
                        }
                        unset($value);
                    }
                    $pager = pagination($total, $pindex, $pagesize);
                }
            } else if ($op == 'post_commission') {
                $nav['subtitle'] = '编辑';
                $id = intval($_GPC['id']);
                if ($id) {
                    $item = M::t('superman_mall_partner_stat')->fetch($id);
                    $item['daytime'] = SupermanUtil::format_date($item['daytime']);
                }
                if (checksubmit()) {
                    $data = array(
                        'daytime' => str_replace("-", "", $_GPC['daytime']),
                        'commission_total' => $_GPC['commission_total'],
                    );
                    if ($id) {
                        M::t('superman_mall_partner_stat')->update($data, array('id' => $id));
                    } else {
                        $data['uniacid'] = $_W['uniacid'];
                        $data['partnerid'] = $_GPC['partnerid'];
                        $data['type'] = 0;
                        $row = M::t('superman_mall_partner_stat')->fetch(array('partnerid' => $_GPC['partnerid'], 'type' => '0', 'daytime' => $data['daytime']));
                        if ($row) {
                            message('佣金已存在，不能重复添加！', referer(), 'error');
                        } else {
                            M::t('superman_mall_partner_stat')->insert($data);
                        }
                    }
                    message('操作成功！', $this->createWebUrl('partner', array('act' => 'ranking','op' => 'display_commission', 'partnerid' => $_GPC['partnerid'])));
                }
            } else if ($op == 'delete_commission') {
                $id = intval($_GPC['id']);
                if ($id <= 0 ) {
                    message('数据不存在或已删除！', referer(), 'error');
                }
                M::t('superman_mall_partner_stat')->delete(array('id' => $id));
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'poster') {
            $nav['subtitle'] = '分销海报';
            $op = in_array($_GPC['op'], array('display', 'post', 'delete', 'refresh')) ? $_GPC['op'] : 'display';
            if ($op == 'display') {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 20;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                );
                $total = M::t('superman_mall_partner_poster')->count($filter);
                if ($total) {
                    $list = M::t('superman_mall_partner_poster')->fetchall($filter, '', $start, $pagesize);
                    $pager = pagination($total, $pindex, $pagesize);
                }
            } else if ($op == 'post') {
                $system_bgimgs = array(
                    array(
                        'filename' => '1.png',
                    ),
                    array(
                        'filename' => '2.png',
                    ),
                    array(
                        'filename' => '3.png',
                    ),
                    array(
                        'filename' => '4.png',
                    ),
                    array(
                        'filename' => '5.png',
                    ),
                    array(
                        'filename' => '6.png',
                    ),
                );
                $id = intval($_GPC['id']);
                $item = M::t('superman_mall_partner_poster')->fetch($id);
                if ($item) {
                    $item['rule'] = M::t('rule')->fetch(array(
                        'uniacid' => $_W['uniacid'],
                        'name' => 'partner_poster:'.$id,
                        'module' => SUPERMAN_MODULE_NAME,
                    ));
                    $item['rule_keyword'] = array(
                        'content' => '',
                    );
                    if ($item['rule']) {
                        $item['rule_keyword'] = M::t('rule_keyword')->fetch(array(
                            'rid' => $item['rule']['id'],
                        ));
                    }
                    $item['widgets'] = $item['widgets']?iunserializer($item['widgets']):array();
                }
                if (checksubmit()) {
                    $name = $_GPC['name'];
                    $isdefault = $_GPC['isdefault']?1:0;
                    $partner_downline = $_GPC['partner_downline']?1:0;
                    $bgimg = $_GPC['bgimg'];
                    $widgets = array();
                    $widget_types = array('qrcode', 'image', 'mobile', 'nickname', 'avatar', 'address');

                    foreach ($widget_types as $type) {
                        if (isset($_GPC[$type]) && $_GPC[$type]) {
                            foreach ($_GPC[$type] as $v) {
                                $widgets[] = array(
                                    'type' => $type,
                                    'top' => $v['top'],
                                    'left' => $v['left'],
                                    'width' => $v['width'],
                                    'height' => $v['height'],
                                    'color' => $v['color'],
                                    'fontsize' => $v['fontsize'],
                                    'imgpath' => $v['imgpath'],
                                );
                            }
                        }
                    }
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'name' => $name,
                        'isdefault' => $isdefault,
                        'partner_downline' => $partner_downline,
                        'bgimg' => $bgimg,
                        'widgets' => iserializer($widgets),
                    );
                    if ($item) {
                        //清除默认
                        if ($isdefault) {
                            $sql = "UPDATE " . tablename('superman_mall_partner_poster') . " SET isdefault=0 WHERE uniacid=:uniacid AND id!=:id";
                            pdo_query($sql, array(
                                ':uniacid' => $_W['uniacid'],
                                ':id' => $id,
                            ));
                        }
                        M::t('superman_mall_partner_poster')->update($data, array('id' => $id));
                    } else {
                        //清除默认
                        if ($isdefault) {
                            M::t('superman_mall_partner_poster')->update(array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
                        }
                        $data['dateline'] = TIMESTAMP;
                        $id = M::t('superman_mall_partner_poster')->insert($data);
                        if (!$id) {
                            message('数据库操作失败！(insert superman_mall_partner_poster failed)', '', 'error');
                        }
                    }

                    //触发关键字
                    $keyword = $_GPC['keyword'];
                    if (!empty($keyword)) {
                        if ($item['rule']) {
                            if ($keyword != $item['rule_keyword']['content']) {
                                M::t('rule_keyword')->update(array(
                                    'content' => $keyword,
                                    'type' => 1,
                                    'displayorder' => 0,
                                    'status' => 1,
                                ), array(
                                    'id' => $item['rule_keyword']['id'],
                                ));
                            }
                        } else {
                            $data = array(
                                'uniacid' => $_W['uniacid'],
                                'module' => SUPERMAN_MODULE_NAME,
                                'name' => 'partner_poster:'.$id,
                                'displayorder' => 0,
                                'status' => 1,
                            );
                            $rid = M::t('rule')->insert($data);
                            if (!$rid) {
                                message('创建回复规则失败！(insert rule failed)', '', 'error');
                            }
                            $data = array(
                                'rid' => $rid,
                                'uniacid' => $_W['uniacid'],
                                'module' => SUPERMAN_MODULE_NAME,
                                'content' => $keyword,
                                'type' => 1,
                                'displayorder' => 0,
                                'status' => 1,
                            );
                            $new_id = M::t('rule_keyword')->insert($data);
                            if (!$new_id) {
                                message('创建回复规则关键字失败！(insert rule_keyword failed)', '', 'error');
                            }
                        }
                    }

                    if (isset($_GPC['refresh']) && $_GPC['refresh']) {
                        $total = $this->_clear_poster_cache($id);
                        message('操作成功（清理'.$total.'张海报缓存）！', $this->createWebUrl('partner', array('act' => 'poster', 'op' => 'display')), 'success');
                    } else {
                        message('操作成功！', $this->createWebUrl('partner', array('act' => 'poster', 'op' => 'display')), 'success');
                    }
                }
            } else if ($op == 'delete') {
                $id = intval($_GPC['id']);
                $item = M::t('superman_mall_partner_poster')->fetch($id);
                if (empty($item)) {
                    message('海报不存在或已删除！', referer(), 'error');
                }
                M::t('superman_mall_partner_poster')->delete(array('id' => $id));
                message('操作成功！', $this->createWebUrl('partner', array('act' => 'poster', 'op' => 'display')), 'success');
            } else if ($op == 'refresh') {
                $id = intval($_GPC['id']);
                $item = M::t('superman_mall_partner_poster')->fetch($id);
                if (empty($item)) {
                    message('海报不存在或已删除！', referer(), 'error');
                }
                $total = $this->_clear_poster_cache($id);
                message('操作成功（清理'.$total.'张海报缓存）！', $this->createWebUrl('partner', array('act' => 'poster', 'op' => 'display')), 'success');
            }
        }
		include $this->template('partner/index');
    }
    private function _clear_poster_cache($id) {
        global $_W;
        $path = MODULE_ROOT.'/data/'.$_W['uniacid'].'/poster/';
        $list = file_lists($path, 0, 'png');
        $total = 0;
        if ($list) {
            foreach ($list as $li) {
                if (strexists($li, "-{$id}.png")) {
                    $total += 1;
                    @unlink(IA_ROOT.'/'.$li);
                }
            }
        }
        M::t('superman_mall_member_poster')->delete(array('tid' => $id));
        return $total;
    }
}
$obj = new Superman_mall_doWebPartner;