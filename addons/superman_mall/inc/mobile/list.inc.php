<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileList extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $do = $do?$do:'list';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $style_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_STYLE);
            $list_style_cart_btn = $style_setting['list_style_cart_btn']?$style_setting['list_style_cart_btn']:0;
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'status' => 1,
                'special' => intval($_GPC['special']),
            );
            $orderby = '';
            $kw = trim($_GPC['kw']);
            //multiple|sale|comment|price
            $sort = in_array($_GPC['sort'], array('multiple', 'sale', 'comment', 'priceup', 'pricedown'))?$_GPC['sort']:'multiple';
            if ($kw != '') {
                $filter['title'] = "# LIKE '%{$kw}%'";
            }
            $sort_price = 'priceup';
            switch ($sort) {
                case 'multiple':
                    $orderby = 'ORDER BY position DESC, comment_count DESC, sales DESC, view_count DESC';
                    break;
                case 'sale':
                    $orderby = 'ORDER BY sales DESC, position DESC';
                    break;
                case 'comment':
                    $orderby = 'ORDER BY comment_count DESC, position DESC';
                    break;
                case 'priceup':
                    $sort_price = 'pricedown';
                    $orderby = 'ORDER BY price DESC, position DESC';
                    break;
                case 'pricedown':
                    $sort_price = 'priceup';
                    $orderby = 'ORDER BY price ASC, position DESC';
                    break;
            }
            $params = array(
                'kw' => $kw,
            );
            if (isset($_GPC['pcid'])) {
                $filter['pcid'] = intval($_GPC['pcid']);
                $params['pcid'] = intval($_GPC['pcid']);
            }
            if (isset($_GPC['cid'])) {
                $filter['cid'] = intval($_GPC['cid']);
                $params['cid'] = intval($_GPC['cid']);
            }
            if (isset($_GPC['ccid'])) {
                $filter['ccid'] = intval($_GPC['ccid']);
                $params['ccid'] = intval($_GPC['ccid']);
            }
            $list = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
            if ($list) {
                foreach ($list as &$value) {
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
                die(json_encode($list));
            }
            //切换样式
            if (($style_setting && $style_setting['list_style_switch'] == 1) || !$style_setting) {
                if (isset($_GPC['list_style'])) {
                    $list_style = $_GPC['list_style']?$_GPC['list_style']:2;
                } else {
                    $list_style = $style_setting['list_style_default']?$style_setting['list_style_default']:2;
                }
            } else {
                $list_style = $style_setting['list_style_default']?$style_setting['list_style_default']:2;
            }
        }
		include $this->template('list');
    }
}
$obj = new Superman_mall_doMobileList;