<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileHome extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '首页';
        $do = $do?$do:'home';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'status' => 1,
                'special' => 0,
            );
            //猜你喜欢
            $orderby = 'ORDER BY `position` DESC, id DESC';
            $items['likes'] = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
            if ($items['likes']) {
                foreach ($items['likes'] as &$value) {
                    $filter = array(
                        'itemid' => $value['id']
                    );
                    $sku = M::t('superman_mall_item_sku')->fetch($filter);
                    if ($sku) {
                        $value['skuid'] = $sku['id'];
                    }
                    unset($value, $sku);
                }
            }
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($items['likes']));
            }
            $slides = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_HOME_SLIDE);
            $apps = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_HOME_APPS);
            if ($apps) {
                usort($apps, array(SupermanUtil, "sort_displayorder_desc"));
                foreach ($apps as $k=>&$app) {
                    if (!$app['isshow']) {
                        unset($apps[$k]);
                    }
                }
                unset($app);
            }
            $top = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_HOME_TOP);
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
            $seckill_params = SupermanUtil::get_seckill_params();
            //秒杀
            $items['seckills'] = M::t('superman_mall_item')->fetchall(array(
                'uniacid' => $_W['uniacid'],
                'status' => 1,
                'special' => 1,
                'starttime' => '#<='.TIMESTAMP,
                'endtime' => '#>='.TIMESTAMP,
                'seckill_time' => $seckill_params['key'],
            ), 'ORDER BY position DESC, id DESC', 0, -1);
            if ($items['seckills']) {
                foreach ($items['seckills'] as &$li) {
                    if ($li['total'] == 0) {
                        $li['sale_percent'] = 100;
                    } else {
                        $li['sale_percent'] = floor($li['sales']/($li['sales']+$li['total'])*100);
                    }
                    if ($li['sale_percent'] <= 0) {
                        $li['sale_percent'] = 1;
                    }
                }
                unset($li);
            }
            //拼团
            $items['mgroupon'] = M::t('superman_mall_item')->fetchall(array(
                'uniacid' => $_W['uniacid'],
                'special' => 2,
                'status' => 1,
            ), ' ORDER BY position DESC, id DESC', 0, 12);
            $ad_settings = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_HOME_AD);
            $style_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_STYLE);
            //购物车按钮
            $list_style_cart_btn = $style_setting['list_style_cart_btn']?$style_setting['list_style_cart_btn']:0;
            //推荐店铺
            if (($style_setting && $style_setting['list_style_switch'] == 1) || !$style_setting) {
                if (isset($_GPC['list_style'])) {
                    $list_style = $_GPC['list_style']?$_GPC['list_style']:2;
                } else {
                    $list_style = $style_setting['list_style_default']?$style_setting['list_style_default']:2;
                }
            } else {
                $list_style = $style_setting['list_style_default']?$style_setting['list_style_default']:2;
            }
            $shops = M::t('superman_mall_shop')->fetchall(array(
                'uniacid' => $_W['uniacid'],
                'status' => 1,
                'ishome' => 1,
            ), 'ORDER BY displayorder DESC, id DESC', 0, -1);
            //print_r($shops);
        }
		include $this->template('home');
    }
}
$obj = new Superman_mall_doMobileHome;