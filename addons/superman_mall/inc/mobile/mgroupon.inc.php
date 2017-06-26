<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileMgroupon extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $do = $do ? $do : 'comment';
        $act = in_array($_GPC['act'], array('display', 'invite', 'list')) ? $_GPC['act'] : 'list';
        if ($act == 'display') {
            $title = '我的拼团';
            $list = array();
            $inviter = array();
            if ($_W['member']['uid']) {
                $pindex = max(1, intval($_GPC['page']));
                $pagesize = 10;
                $start = ($pindex - 1) * $pagesize;
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'pay_status' => 1,
                );
                $orderby = '';
                $rows = M::t('superman_mall_merge_groupon')->fetchall($filter, $orderby, $start, $pagesize, 'id');
                if ($rows) {
                    foreach ($rows as &$row) {
                        if ($row['mgid'] == 0 && !isset($list[$row['mgid']])) { //团长
                            $list[$row['id']] = $this->format_data($row);
                        } else { //团员
                            if (!isset($list[$row['mgid']])) {
                                $item = M::t('superman_mall_merge_groupon')->fetch($row['mgid']);
                                if ($item) {
                                    $list[$row['mgid']] = $this->format_data($item);
                                }
                            }
                        }
                    }
                    unset($row);
                }
                //加载更多
                if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                    die(json_encode($list));
                }
                //print_r($list);
            }
        } else if ($act == 'list') {
            $title = '拼团';
            $list = array();
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            if (isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'status' => 1,
                    'special' => 2,
                );
                if (isset($_GPC['shopid']) && $_GPC['shopid']) {
                    $filter['shopid'] = intval($_GPC['shopid']);
//                    $mgroupon_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, intval($_GPC['shopid']));
                }
                $orderby = '';
                $sort = in_array($_GPC['sort'], array('multiple', 'sale', 'comment', 'priceup', 'pricedown'))?$_GPC['sort']:'multiple';
                $sort_price = 'priceup';
                switch ($sort) {
                    case 'multiple':
                        $orderby = 'ORDER BY displayorder DESC, comment_count DESC, sales DESC, view_count DESC';
                        break;
                    case 'sale':
                        $orderby = 'ORDER BY sales DESC, id ASC';
                        break;
                    case 'comment':
                        $orderby = 'ORDER BY comment_count DESC, id ASC';
                        break;
                    case 'priceup':
                        $sort_price = 'pricedown';
                        $orderby = 'ORDER BY price DESC, id ASC';
                        break;
                    case 'pricedown':
                        $sort_price = 'priceup';
                        $orderby = 'ORDER BY price ASC, id ASC';
                        break;
                }
                $list = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
                //$list = M::t('superman_mall_item')->fetchall($filter, $orderby, 0, -1);
                if ($list) {
                    foreach ($list as &$li) {
                        /*if (!isset($_GPC['shopid']) || $_GPC['shopid'] <= 0) {
                            $mgroupon_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, $li['shopid']);
                        }
                        $li['mgroupon_limit'] = $mgroupon_setting['limit'];*/
                        $li['extend'] = $li['extend']?iunserializer($li['extend']):array();
                    }
                    unset($li);
                }
                //加载更多
                if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                    die(json_encode($list));
                }
                //print_r($list);
            }
        } else if ($act == 'invite') {
            $back_url = $this->createMobileUrl('mgroupon', array('act' => 'display'));
            $my_mgroupon = array();
            $id = intval($_GPC['id']);
            $shopid = intval($_GPC['shopid']);
            $mgroupon = M::t('superman_mall_merge_groupon')->fetch($id);
            if (!$mgroupon) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            //拼团设置
            $mgroupon_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, $mgroupon['shopid']);
            $my_mgroupon = M::t('superman_mall_merge_groupon')->fetch(array(
                'uid' => $_W['member']['uid'],
                'id' => $id,
                'pay_status' => 1,
            ));
            if (!$my_mgroupon) {
                $my_mgroupon = M::t('superman_mall_merge_groupon')->fetch(array(
                    'uid' => $_W['member']['uid'],
                    'mgid' => $id,
                    'pay_status' => 1,
                ));
            }
            if ($mgroupon['mgid'] == 0) {
                $filter = array(
                    'mgid' => $id,
                    'pay_status' => 1,
                );
                $inviter = $mgroupon;
            } else {
                $filter = array(
                    'mgid' => $mgroupon['mgid'],
                    'pay_status' => 1,
                );
                $inviter = M::t('superman_mall_merge_groupon')->fetch($mgroupon['mgid']);
            }
            $inviter['member'] = mc_fetch($inviter['uid'], array('nickname', 'avatar'));
            /*if ($inviter['member'] && $inviter['uid'] != $_W['member']['uid']) {
                $inviter['member']['nickname'] = $inviter['member']['nickname'];
            }*/
            $invitee = M::t('superman_mall_merge_groupon')->fetchall($filter, 'ORDER BY id ASC', 0, -1); //被邀请人
            if ($invitee) {
                foreach ($invitee as &$li) {
                    //是否虚拟游客
                    if ($li['uid'] != 0) {
                        $li['member'] = mc_fetch($li['uid'], array('nickname', 'avatar'));
                    }
                    if ($li['member'] && $li['uid'] != $_W['member']['uid'] && $li['member']['nickname'] != '') {
                        $li['member']['nickname'] = SupermanUtil::hide_nickname($li['member']['nickname']);
                    }
                    if (!isset($li['member']) || $li['member']['nickname'] == '') {
                        $li['member']['nickname'] = '**';
                    }
                }
                unset($li);
            }
            $inviter['remain_total'] = $inviter['limit'] - count($invitee) - 1;
            $inviter['remain_total'] = $inviter['remain_total']>0?$inviter['remain_total']:0;
            $inviter['remain_time'] = $inviter['expiretime'] - TIMESTAMP>0?$inviter['expiretime'] - TIMESTAMP:0;
            if (!$inviter['remain_time']) {
                $inviter['expiretime'] = 0;
            }
            //print_r($inviter);
            //print_r($invitee);
            if ($inviter['remain_total']) {
                for ($i=0; $i<$inviter['remain_total']; $i++) {
                    $invitee[] = array(
                        'member' => array(
                            'nickname' => '&nbsp;',
                            'avatar' => $this->mobile_path.'/images/avatar.png',
                        ),
                    );
                }
            }
            if ($my_mgroupon) {
                $title = '我的拼团';
                //订单商品
                $filter = array(
                    'orderid' => $my_mgroupon['orderid'],
                );
                $my_mgroupon['order_item'] = M::t('superman_mall_order_item')->fetchall($filter, '', 0, -1);
            } else { //游客
                $title = '一起拼团';
                if ($inviter['remain_total'] > 0 &&  $inviter['remain_time']) {    //还有拼团名额 && 未过期
                    //记录所参与拼团id
                    isetcookie('__mgroupon_id', $inviter['id'], $inviter['remain_time']);
                }
                $order_item = M::t('superman_mall_order_item')->fetch(array(
                    'orderid' => $mgroupon['orderid']
                ));
                if ($order_item) {
                    $list[0] = M::t('superman_mall_item')->fetch($order_item['itemid']);
                }
                /*
                $filter = array(
                    'status' => 1,
                    'special' => 2,
                );
                if (isset($_GPC['shopid']) && $shopid) {
                    $filter['shopid'] = $shopid;
                } else {
                    $filter['uniacid'] = $_W['uniacid'];
                }
                $list = M::t('superman_mall_item')->fetchall($filter, '', 0, 10);*/
            }

            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHARE);
            if (isset($setting['mgroupon']) && $setting['mgroupon']['title'] != '' && isset($my_mgroupon['order_item'][0])) {
                $_share = array(
                    'title'   => $setting['mgroupon']['title'],
                    'link'    => $_W['siteroot'] . 'app/' . $this->createMobileUrl('mgroupon', array(
                        'act' => 'invite',
                        'id' => $id,
                        'shopid' => $shopid,
                    )),
                    'imgUrl'  => $setting['mgroupon']['imgurl']?tomedia($setting['mgroupon']['imgurl']):tomedia($my_mgroupon['order_item'][0]['cover']),
                    'content' => $setting['mgroupon']['desc'],
                );
                $arr = array(
                    'remain_total' => $inviter['remain_total'],
                    'item_title' => $my_mgroupon['order_item'][0]['title']
                );
                $_share = $this->set_share_arr($_share, $arr);
                //WeUtility::logging('trace', '$_share='.var_export($_share, true));
            }
            $show_mgroupon_rule = 0;
            if (!isset($_GPC['__show_mgroupon_rule']) && !$my_mgroupon) {
                $show_mgroupon_rule = 1;
                $expire = $mgroupon_setting&&$mgroupon_setting['expire']?$mgroupon_setting['expire']*3600:3*86400;
                isetcookie('__show_mgroupon_rule', 1, $expire);
            }
        }
        include $this->template('mgroupon');
    }

    private function format_data(&$row) {
        global $_W;
        if ($row['uid'] > 0) {
            $member = mc_fetch($row['uid'], array('nickname', 'avatar'));
            if ($member) {
                $row['member'] = $member;
                $row['children'][] = array(
                    'member' => array(
                        'nickname' => $member['nickname'],
                        'avatar' => tomedia($member['avatar']),
                    ),
                );
            } else {
                $row['children'][] = array(
                    'member' => array(
                        'nickname' => '&nbsp;',
                        'avatar' => $this->mobile_path.'/images/avatar.png',
                    ),
                );
            }
        }
        $row['remain_time'] = $row['expiretime'] - TIMESTAMP>0?$row['expiretime'] - TIMESTAMP:0;
        $children = M::t('superman_mall_merge_groupon')->fetchall(array(
            'mgid' => $row['id'],
            'pay_status' => 1,
        ), 'ORDER BY id ASC', 0, -1);
        if ($children) {
            foreach ($children as $child) {
                if ($child['uid'] > 0) {
                    $member = mc_fetch($child['uid'], array('nickname', 'avatar'));
                    $row['children'][] = array(
                        'member' => array(
                            'nickname' => $_W['member']['uid']!=$member['uid']?SupermanUtil::hide_nickname($member['nickname']):$member['nickname'],
                            'avatar' => tomedia($member['avatar']),
                        ),
                    );
                } else {
                    $row['children'][] = array(
                        'member' => array(
                            'nickname' => '**',
                            'avatar' => $this->mobile_path.'/images/avatar.png',
                        ),
                    );
                }

            }
        }
        if (count($row['children']) >= $row['limit']) {
            $row['status_title'] = '已完成';
        } else {
            if ($row['remain_time']) {
                $row['status_title'] = '进行中';
            } else {
                $row['status_title'] = '失败';
            }
            $len = $row['limit']-count($row['children']);
            for ($i=0; $i<$len; $i++) {
                $row['children'][] = array(
                    'member' => array(
                        'nickname' => '&nbsp;',
                        'avatar' => $this->mobile_path.'/images/avatar.png',
                    ),
                );
            }
        }
        return $row;
    }

    private function set_share_arr($share, $arr) {
        foreach ($share as $k => $v) {
            if ($k != 'link' || $k != 'imgUrl') {
                $v = str_replace('{人数}', $arr['remain_total'], $v);
                $v = str_replace('{标题}', $arr['item_title'], $v);
                $share[$k] = $v;
            }
        }
        return $share;
    }
}
$obj = new Superman_mall_doMobileMgroupon;