<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
define('IN_SUPERMAN_MALL_PLATFORM', true);
class Superman_mall_doWebShop extends Superman {
    public function __construct() {
        global $_GPC;
        parent::__construct();
        parent::init();
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
    }

    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'plugin', 'switch', 'charge_sms_log', 'send_sms_log')) ? $_GPC['act'] : 'display';
        $nav['title'] = '商户管理';
        $this->check_user_permission('superman_mall_menu_shop');
        if ($act == 'display') {
            $nav['subtitle'] = '全部';
            //更新排序
            if(checksubmit('displayorder_submit')) {
                if ($_GPC['displayorder']) {
                    foreach ($_GPC['displayorder'] as $id => $val) {
                        M::t('superman_mall_shop')->update(array('displayorder' => $val), array('id' => $id));
                    }
                    message('操作成功！', referer(), 'success');
                }
            }
            if (checksubmit('submit')) {
                $shopid = intval($_GPC['shopid']);
                $remark = $_GPC['remark'];
                $audit_status = $_GPC['audit_status'];
                if ($shopid <= 0) {
                    message('非法请求', referer(), 'error');
                }
                $data = array(
                    'status' => $audit_status
                );
                $ret = M::t('superman_mall_shop')->update($data, array('id' => $shopid));
                if ($ret !== false) {
                    $shop = M::t('superman_mall_shop')->fetch($shopid);
                    $shop_user = M::t('superman_mall_shop_user')->fetch(array('shopid' => $shopid, 'groupid' => 0));
                    $shop_url = '';
                    if ($audit_status == 1) {
                        $shop_url = $_W['siteroot'].'app/'.$this->createMobileUrl('shop', array('act' => 'join'));
                    }
                    $this->send_shop_tmplmsg($shop, $shop_user['openid'], $audit_status, $remark, $shop_url);
                    message('操作成功！', referer(), 'success');
                } else {
                    message('系统错误，请稍候再试', referer(), 'error');
                }
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $status = in_array($_GPC['status'], array(0, 1)) ? $_GPC['status'] : 'all';
            $ishome = isset($_GPC['ishome']) && in_array($_GPC['ishome'], array(0, 1)) ? $_GPC['ishome'] : 'all';

            $filter = array(
                'uniacid' => $_W['uniacid']
            );
            if ($status == '1') {
                $nav['subtitle'] = '已审核';
                $filter['status'] = '# !=0';
            } else if ($status == '0') {
                $nav['subtitle'] = '待审核';
                $filter['status'] = 0;
            }
            if (isset($_GPC['title']) && trim($_GPC['title'])) {
                $filter['title'] = '# LIKE "%'.trim($_GPC['title']).'%"';
            }
            if ($ishome != 'all') {
                $filter['ishome'] = $_GPC['ishome'];
            }
            $orderby = ' ORDER BY `displayorder` DESC, `id` DESC';
            $total = M::t('superman_mall_shop')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop')->fetchall($filter, $orderby, $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['createtime'] = date('Y-m-d H:i:s', $li['createtime']);
                        $shop_user = M::t('superman_mall_shop_user')->fetch(array(
                            'uniacid' => $_W['uniacid'],
                            'shopid' => $li['id'],
                        ), 'ORDER BY id ASC');
                        if ($shop_user && !empty($shop_user['openid'])) {
                            $li['user'] = mc_fetch($shop_user['openid'], array('nickname', 'avatar'));
                            $li['user']['mobile'] = $shop_user['mobile'];
                        }
                    }
                    unset($li);
                    $pager = pagination($total, $pindex, $pagesize);
                }
            }
            if (isset($_GPC['switch'])) {
                define('SUPERMAN_MALL_NO_NAVS', true);
                include $this->template('shop-switch');
            } else {
                include $this->template('shop');
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $location = array('lng' => '', 'lat' => '');
            $shop_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
            if ($shop_setting && $shop_setting['international']) {
                $countrys = M::t('superman_mall_country')->fetchall(array('isshow' => 1), '', 0, -1);
            }
            if ($id) {
                $item = M::t('superman_mall_shop')->fetch($id);
                if (!$item) {
                    message('商户不存在或已删除！', referer(), 'error');
                }
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $id,
                );
                $item['createtime'] = date('Y-m-d H:i:s', $item['createtime']);
                $shop_user = M::t('superman_mall_shop_user')->fetch($filter, 'ORDER BY id ASC');
                if ($shop_user && !empty($shop_user['openid'])) {
                    $user_info = mc_fetch($shop_user['openid'], array('nickname', 'avatar'));
                }
                $location = array(
                    'lng' => $item['longitude'],
                    'lat' => $item['latitude'],
                );
            }
            if (defined('SUPERMAN_CONNECT_BMPAYU')) {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'module' => 'bm_payu',
                );
                $payu_list = M::t('rule')->fetchall($filter);
            }

            if (checksubmit()) {
                $title = $_GPC['title'];
                if ($title == '') {
                    message('商户名称为空', referer(), 'error');
                }
                $location = $_GPC['location'];
                $data = array(
                    'title' => $title,
                    'countryid' => $_GPC['countryid'],
                    'logo' => $_GPC['logo'],
                    'description' => $_GPC['description'],
                    'business_scope' => $_GPC['business_scope'],
                    'province' => $_GPC['area']['province'],
                    'city' => $_GPC['area']['city'],
                    'district' => $_GPC['area']['district'],
                    'address' => $_GPC['address'],
                    'longitude' => $location['lng'],
                    'latitude' => $location['lat'],
                    'phone' => $_GPC['phone'],
                    'fee_rate' => $_GPC['fee_rate'],
                    'fee_min' => $_GPC['fee_min'],
                    'fee_max' => $_GPC['fee_max'],
                    'ishome' => $_GPC['ishome']?1:0,
                    'payu_rid' => $_GPC['payu_rid']?$_GPC['payu_rid']:0,
                );
                $sms_total = intval($_GPC['sms_total']);
                if ($sms_total > 0) {
                    $data['sms_total'] = $sms_total;
                    M::t('superman_mall_shop_sms_total')->insert(array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $id,
                        'total' => $item['sms_total']?$item['sms_total']:0,
                        'new_total' => $sms_total,
                        'operator' => $_W['user']['username'],
                        'dateline' => TIMESTAMP,
                    ));
                }
                if ($id) {
                    $data['updatetime'] = TIMESTAMP;
                    M::t('superman_mall_shop')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['status'] = 1;
                    $data['createtime'] = TIMESTAMP;
                    M::t('superman_mall_shop')->insert($data);
                }
                message('操作成功！', $this->createWebUrl('shop', array('act' => 'list')), 'success');
            }
            include $this->template('shop');
        } else if ($act == 'delete') {
            if (empty($_SERVER['HTTP_REFERER'])) {
                message('非法请求！', '', 'error');
            }
            $id = intval($_GPC['id']);
            $row = M::t('superman_mall_shop')->fetch($id);
            if (!$row) {
                message('商户不存在或已删除！', referer(), 'error');
            }
            M::t('superman_mall_shop')->delete(array('id' => $id));
            M::t('superman_mall_shop_user')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_user_group')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_attachment')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_money')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_money_log')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_money_stat')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_getcash_user')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_getcash_log')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_sms_sendlog')->delete(array('shopid' => $id));
            M::t('superman_mall_shop_sms_total')->delete(array('shopid' => $id));
            message('操作成功！',referer(), 'success');
        } else if ($act == 'plugin') {
            $nav['subtitle'] = '功能模块';
            $id = intval($_GPC['id']);
            $shop = M::t('superman_mall_shop')->fetch($id);
            $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_SHOP_PLUGIN, $id);
            $setting = M::t('superman_mall_kv')->fetch_value($skey);
            //时间格式化
            $usetime = array(
                'seckill' => array(
                    'start' => $setting['seckill']['starttime']>0?date('Y-m-d H:i:s', $setting['seckill']['starttime']):date('Y-m-d H:i:s'),
                    'end' => $setting['seckill']['endtime']>0?date('Y-m-d H:i:s', $setting['seckill']['endtime']):date('Y-m-d H:i:s', strtotime('+30days')),
                ),
                'mgroupon' => array(
                    'start' => $setting['mgroupon']['starttime']>0?date('Y-m-d H:i:s', $setting['mgroupon']['starttime']):date('Y-m-d H:i:s'),
                    'end' => $setting['mgroupon']['endtime']>0?date('Y-m-d H:i:s', $setting['mgroupon']['endtime']):date('Y-m-d H:i:s', strtotime('+30days')),
                ),
                'partner' => array(
                    'start' => $setting['partner']['starttime']>0?date('Y-m-d H:i:s', $setting['partner']['starttime']):date('Y-m-d H:i:s'),
                    'end' => $setting['partner']['endtime']>0?date('Y-m-d H:i:s', $setting['partner']['endtime']):date('Y-m-d H:i:s', strtotime('+30days')),
                ),
                'discount' => array(
                    'start' => $setting['discount']['starttime']>0?date('Y-m-d H:i:s', $setting['discount']['starttime']):date('Y-m-d H:i:s'),
                    'end' => $setting['discount']['endtime']>0?date('Y-m-d H:i:s', $setting['discount']['endtime']):date('Y-m-d H:i:s', strtotime('+30days')),
                ),
                'printer' => array(
                    'start' => $setting['printer']['starttime']>0?date('Y-m-d H:i:s', $setting['printer']['starttime']):date('Y-m-d H:i:s'),
                    'end' => $setting['printer']['endtime']>0?date('Y-m-d H:i:s', $setting['printer']['endtime']):date('Y-m-d H:i:s', strtotime('+30days')),
                ),
                'tbast' => array(
                    'start' => $setting['tbast']['starttime']>0?date('Y-m-d H:i:s', $setting['tbast']['starttime']):date('Y-m-d H:i:s'),
                    'end' => $setting['tbast']['endtime']>0?date('Y-m-d H:i:s', $setting['tbast']['endtime']):date('Y-m-d H:i:s', strtotime('+30days')),
                ),
                'bm_payu' => array(
                    'start' => $setting['bm_payu']['starttime']>0?date('Y-m-d H:i:s', $setting['bm_payu']['starttime']):date('Y-m-d H:i:s'),
                    'end' => $setting['bm_payu']['endtime']>0?date('Y-m-d H:i:s', $setting['bm_payu']['endtime']):date('Y-m-d H:i:s', strtotime('+30days')),
                ),
            );

            if (checksubmit('submit')) {
                $svalue = array(
                    'seckill' => array(
                        'open' => $_GPC['seckill']['switch']=='on'?1:0,
                        'starttime' => strtotime($_GPC['seckill']['usetime']['start']),
                        'endtime' => $_GPC['seckill']['limit'] == 'on'?-1:strtotime($_GPC['seckill']['usetime']['end']),
                    ),
                    'mgroupon' => array(
                        'open' => $_GPC['mgroupon']['switch']=='on'?1:0,
                        'starttime' => strtotime($_GPC['mgroupon']['usetime']['start']),
                        'endtime' => $_GPC['mgroupon']['limit'] == 'on'?-1:strtotime($_GPC['mgroupon']['usetime']['end']),
                    ),
                    'partner' => array(
                        'open' => $_GPC['partner']['switch']=='on'?1:0,
                        'starttime' => strtotime($_GPC['partner']['usetime']['start']),
                        'endtime' => $_GPC['partner']['limit'] == 'on'?-1:strtotime($_GPC['partner']['usetime']['end']),
                    ),
                    'discount' => array(
                        'open' => $_GPC['discount']['switch']=='on'?1:0,
                        'starttime' => strtotime($_GPC['discount']['usetime']['start']),
                        'endtime' => $_GPC['discount']['limit'] == 'on'?-1:strtotime($_GPC['discount']['usetime']['end']),
                    ),
                    'printer' => array(
                        'open' => $_GPC['printer']['switch']=='on'?1:0,
                        'starttime' => strtotime($_GPC['printer']['usetime']['start']),
                        'endtime' => $_GPC['printer']['limit'] == 'on'?-1:strtotime($_GPC['printer']['usetime']['end']),
                    ),
                    'tbast' => array(
                        'open' => $_GPC['tbast']['switch']=='on'?1:0,
                        'starttime' => strtotime($_GPC['tbast']['usetime']['start']),
                        'endtime' => $_GPC['tbast']['limit'] == 'on'?-1:strtotime($_GPC['tbast']['usetime']['end']),
                    ),
                    'bm_payu' => array(
                        'open' => $_GPC['bm_payu']['switch']=='on'?1:0,
                        'starttime' => strtotime($_GPC['bm_payu']['usetime']['start']),
                        'endtime' => $_GPC['bm_payu']['limit'] == 'on'?-1:strtotime($_GPC['bm_payu']['usetime']['end']),
                    ),
                );

                $data = array(
                    'svalue' => iserializer($svalue),
                );
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
            include $this->template('shop');
        } else if ($act == 'switch') {
            $shopid = intval($_GPC['shopid']);
            $shopid = $shopid>0?$shopid:0;
            if (!empty($_GPC['referer'])) {
                $referer = urldecode($_GPC['referer']);
            } else {
                $referer = $this->createWebUrl('dashboard');
            }
            $expiretime = -1;
            if ($shopid > 0) {
                $expiretime = 30 * 86400;
            }
            isetcookie('__superman_mall_web_shopid:'.$_W['uniacid'], $shopid, $expiretime);
            @header('Location: '.$referer);
            exit();
        } else if ($act == 'charge_sms_log') {
            $nav['subtitle'] = '充值短信记录';
            $id = intval($_GPC['id']);
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $id,
            );
            $total = M::t('superman_mall_shop_sms_total')->count($filter);
            if ($total) {
                $shop = M::t('superman_mall_shop')->fetch($id);
                $list = M::t('superman_mall_shop_sms_total')->fetchall($filter, '', $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
            }
            include $this->template('shop');
        } else if ($act == 'send_sms_log') {
            $nav['subtitle'] = '发送短信记录';
            $id = intval($_GPC['id']);
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $id,
            );
            $total = M::t('superman_mall_shop_sms_sendlog')->count($filter);
            if ($total) {
                $shop = M::t('superman_mall_shop')->fetch($id);
                $list = M::t('superman_mall_shop_sms_sendlog')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['content'] = $li['content']?nl2br(var_export(iunserializer($li['content']), true)):'';
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
            include $this->template('shop');
        }
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('post')) ? $_GPC['act'] : 'post';
        if ($act == 'post') {
            if ($this->shop_user['groupid'] != 0) {
                message('没有操作权限！', referer(), 'error');
            }
            $nav['subtitle'] = '编辑';
            if (!empty($this->shop_user['openid'])) {
                $apply_user = mc_fetch($this->shop_user['openid'], array('nickname', 'avatar'));
            }
            $location = array(
                'lng' => $this->shop['longitude'],
                'lat' => $this->shop['latitude'],
            );
            if (checksubmit()) {
                $title = $_GPC['title'];
                if ($title == '') {
                    message('商户名称为空', referer(), 'error');
                }
                $location = $_GPC['location'];
                $data = array(
                    'title' => $title,
                    'logo' => $_GPC['logo'],
                    'description' => $_GPC['description'],
                    'business_scope' => $_GPC['business_scope'],
                    'province' => $_GPC['area']['province'],
                    'city' => $_GPC['area']['city'],
                    'district' => $_GPC['area']['district'],
                    'address' => $_GPC['address'],
                    'longitude' => $location['lng'],
                    'latitude' => $location['lat'],
                    'phone' => $_GPC['phone'],
                    'updatetime' => TIMESTAMP,
                );
                M::t('superman_mall_shop')->update($data, array('id' => $this->shop['id']));
                message('操作成功！', referer(), 'success');
            }
            include $this->template('shop/index');
        }
    }
}
$obj = new Superman_mall_doWebShop;