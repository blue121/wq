<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileMy extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '我的';
        $do = $do?$do:'my';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $shop_url = $this->createMobileUrl('shop', array('act' => 'reg'));
            $follow_total = $browse_total = 0;
            $credit_titles = $this->get_credit_titles();
            $subscribe_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
            if ($_W['member']['uid']) {
                //关注的商品
                $follow_total = M::t('superman_mall_item_follow')->count(array(
                    'uid' => $_W['member']['uid'],
                    'status' => 1,
                ));
                //浏览记录
                $browse_total = M::t('superman_mall_item_browse')->count(array(
                    'uid' => $_W['member']['uid'],
                ));
                //待支付角标
                $order_total['no_pay'] = M::t('superman_mall_order')->count(array(
                    'uid' => $_W['member']['uid'],
                    'status' => 0,
                ));
                $order_total['no_pay'] = $order_total['no_pay']>99?'99+':$order_total['no_pay'];
                //待收货角标
                $order_total['no_receive'] = M::t('superman_mall_order')->count(array(
                    'uid' => $_W['member']['uid'],
                    'status' => array(1,2),
                ));
                $order_total['no_receive'] = $order_total['no_receive']>99?'99+':$order_total['no_receive'];
                //待评价角标
                $order_total['no_comment'] = M::t('superman_mall_order')->count(array(
                    'uid' => $_W['member']['uid'],
                    'status' => 3,
                ));
                $order_total['no_comment'] = $order_total['no_comment']>99?'99+':$order_total['no_comment'];

                //检查核销权限
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $_W['member']['uid'],
                    'status' => 1,
                );
                $checkout_access = M::t('superman_mall_checkout_user')->count($filter);

                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $_W['openid'],
                );
                $shop_user = M::t('superman_mall_shop_user')->fetch($filter);
                if ($shop_user) { //已绑定商户账号
                    $shop_url = $this->createMobileUrl('admin', array('route' => 'dashboard.display'));
                }
            } else {
                for ($i=0; $i<4; $i++) {
                    $mycredit[] = array(
                        'value' => 0,
                        'title' => '',
                    );
                }
                $order_total['no_pay'] = 0;
                $order_total['no_receive'] = 0;
                $order_total['no_comment'] = 0;
            }
            //分销设置检查
            $partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
            if (!$partner_setting) {
                $partner_setting['text'] = array();
            }
            SupermanMallData::initPartnerCustomText($partner_setting['text']);
            //print_r($order_total);
            //print_r($_W['member']);
            //print_r($_W['fans']);
            //print_r(mc_fetch($_W['member']['uid']));
        }
        include $this->template('my');
    }
}
$obj = new Superman_mall_doMobileMy;