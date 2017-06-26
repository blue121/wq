<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileService extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '客服中心';
        $do = $do?$do:'service';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $shopid = intval($_GPC['shopid']);
            if ($shopid > 0) {
                $shop_service = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_SHOP_SERVICE, $shopid);
                if (!empty($shop_service['link'])) {
                    @header('Location: '.$shop_service['link']);
                    exit;
                }
            }
            if (!empty($this->module['config']['service']['link'])) {
                @header('Location: '.$this->module['config']['service']['link']);
                exit;
            }
        }
		include $this->template('service');
    }
}
$obj = new Superman_mall_doMobileService;