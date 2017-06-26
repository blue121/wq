<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileCart extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '购物车';
        $do = $do?$do:'category';
        $act = in_array($_GPC['act'], array('display', 'post', 'delete'))?$_GPC['act']:'display';
        if ($act == 'display') {
            //展示购物车全部商品
            $cart = array(
                'total_price' => '0.00',
                'total_item' => '0',
            );
            $list = array();
            if ($_W['member']['uid']) {
                $filter = array(
                    'uid' => $_W['member']['uid'],
                );
                $list = M::t('superman_mall_cart')->fetchall($filter, '', 0, -1);
                if ($list) {
                    foreach ($list as $k => &$li) {
                        $li['item'] = M::t('superman_mall_item')->fetch($li['itemid']);
                        if ($li['item']) {
                            //由库存判断是否缺货
                            $li['_no_stock'] = $li['item']['total']&&($li['item']['total']-$li['total'])>=0?false:true;
                            //获取商品规格数据
                            $li['item']['sku'] = array();
                            if ($li['skuid']) {
                                $li['item']['sku'] = M::t('superman_mall_item_sku')->fetch($li['skuid']);
                                if ($li['item']['sku']) {
                                    $valueids = explode(',', $li['item']['sku']['valueids']);
                                    $li['item']['sku']['attr'] = M::t('superman_mall_item_attr')->fetchall_by_valueids($valueids, '', 0, -1, 'id');
                                    $li['_no_stock'] = $li['item']['sku']['total']&&($li['item']['sku']['total']-$li['total'])>=0?false:true;
                                } else {
                                    $li['_no_stock'] = true;
                                }
                            } else {
                                if ($li['sku'] == '') { //无规格商品(既没有skuid，又没有原sku信息)
                                    //do nothing
                                } else { //缺货(没有skuid，有原sku信息)
                                    $li['_no_stock'] = true;
                                }
                            }
                            //统计数量、金额
                            if ($li['checked']) {
                                $cart['total_item'] += 1;
                                if (!$li['_no_stock']) {
                                    if (isset($li['item']['sku']['price']) && $li['item']['sku']['price']) {
                                        $cart['total_price'] += $li['item']['sku']['price'] * $li['total'];
                                    } else {
                                        $cart['total_price'] += $li['item']['price'] * $li['total'];
                                    }
                                }
                            }
                        } else {
                            //商品不存在时，从购物车删除
                            M::t('superman_mall_cart')->delete(array('id' => $li['id']));
                            unset($list[$k]);
                        }
                    }
                    unset($li);
                    $cart['total_price'] = SupermanUtil::money_format($cart['total_price']);

                    //获取商户信息
                    $newlist = array();
                    foreach ($list as $li) {
                        $shopid = $li['item']['shopid'];
                        if (!isset($newlist[$shopid])) {
                            $newlist[$shopid]['shop'] = M::t('superman_mall_shop')->fetch($shopid);
                        }
                        $newlist[$shopid]['list'][] = $li;
                    }
                    $list = $newlist;
                }
            }
            //print_r($list);die;
        } else if ($act == 'post') {
            $this->checkauth();
            //更新购物车
            $cartid = intval($_GPC['cartid']);
            if ($cartid) {
                $row = M::t('superman_mall_cart')->fetch($cartid);
                if (!$row) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $item = M::t('superman_mall_item')->fetch($row['itemid']);
                $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                if (in_array($item['special'], array('1', '2'))) {
                    if ($item['extend']['other_attr']['order_buy_num'] > 0) { //每订单可买
                        if (intval($_GPC['total']) > intval($item['extend']['other_attr']['order_buy_num'])) {
                            $this->json(ERRNO::ITEM_OVER_LIMIT, '商品【'.$item['title'].'】超过该订单可购买数量'.intval($item['extend']['other_attr']['order_buy_num']).'件');
                        }
                    }
                    if ($item['extend']['other_attr']['max_buy_num'] > 0) { //每帐号可买
                        $sql = 'SELECT SUM(a.total) FROM '.tablename('superman_mall_order_item').' AS a,'.tablename('superman_mall_order').' AS b ';
                        $sql .= ' WHERE a.orderid=b.id AND b.uid=:uid AND a.itemid=:itemid AND b.status>0';
                        $params = array(
                            ':uid' => $_W['member']['uid'],
                            ':itemid' => $row['itemid'],
                        );
                        $buy_sum = pdo_fetchcolumn($sql, $params);
                        if (($buy_sum + intval($_GPC['total'])) > $item['extend']['other_attr']['max_buy_num']) {
                            $this->json(ERRNO::ITEM_OVER_LIMIT, '商品【'.$item['title'].'】超过可购买数量'.intval($item['extend']['other_attr']['max_buy_num']).'件');
                        }
                    }
                }
                $data = array();
                if (isset($_GPC['total'])) {
                    $data['total'] = intval($_GPC['total']);
                }
                if (isset($_GPC['checked'])) {
                    $data['checked'] = intval($_GPC['checked'])?1:0;
                }
                if ($data) {
                    $data['dateline'] = TIMESTAMP;
                    $condition = array(
                        'id' => $cartid,
                    );
                    M::t('superman_mall_cart')->update($data, $condition);
                }
                $this->json(ERRNO::OK);
            }
            //全选操作
            if (isset($_GPC['checkall'])) {
                $checked = intval($_GPC['checkall']);
                $data = array(
                    'checked' => $checked?1:0,
                    'dateline' => TIMESTAMP,
                );
                $condition = array(
                    'uid' => $_W['member']['uid'],
                );
                M::t('superman_mall_cart')->update($data, $condition);
                $this->json(ERRNO::OK);
            }
            //添加商品到购物车
            $itemid = intval($_GPC['itemid']);
            $skuid = intval($_GPC['skuid']);
            if (!$itemid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $item = M::t('superman_mall_item')->fetch($itemid);
            if (!$item) {
                $this->json(ERRNO::ITEM_NOT_FOUND);
            }
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'uid' => $_W['member']['uid'],
                'itemid' => $itemid,
            );
            if ($skuid) {
                $filter['skuid'] = $skuid;
            }
            $row = M::t('superman_mall_cart')->fetch($filter);
            if (!$row) {
                $total = intval($_GPC['total']);
                if ($skuid) {
                    //获取商品规格数据
                    $sku = array();
                    $item_sku = M::t('superman_mall_item_sku')->fetch($skuid);
                    if (!$item_sku) {
                        $this->json(ERRNO::SKU_NOT_EXIST);
                    }
                    $valueids = explode(',', $item_sku['valueids']);
                    $item_sku_attr = M::t('superman_mall_item_attr')->fetchall_by_valueids($valueids, '', 0, -1, 'id');
                    if ($item_sku_attr) {
                        foreach ($item_sku_attr as $attr) {
                            $sku[] = "{$attr['title']}:{$attr['value']}";
                        }
                    }
                }
                if (in_array($item['special'], array('1', '2'))) {
                    $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                    if ($item['extend']['other_attr']['order_buy_num'] > 0) { //每订单可买
                        if ($total > intval($item['extend']['other_attr']['order_buy_num'])) {
                            $this->json(ERRNO::ITEM_OVER_LIMIT, '商品超过该订单可购买数量'.intval($item['extend']['other_attr']['order_buy_num']).'件');
                        }
                    }
                    if ($item['extend']['other_attr']['max_buy_num'] > 0) { //每帐号可买
                        $sql = 'SELECT SUM(a.total) FROM '.tablename('superman_mall_order_item').' AS a,'.tablename('superman_mall_order').' AS b ';
                        $sql .= ' WHERE a.orderid=b.id AND b.uid=:uid AND a.itemid=:itemid AND b.status>0';
                        $params = array(
                            ':uid' => $_W['member']['uid'],
                            ':itemid' => $itemid,
                        );
                        $buy_sum = pdo_fetchcolumn($sql, $params);
                        if (($buy_sum + $total) > $item['extend']['other_attr']['max_buy_num']) {
                            $this->json(ERRNO::ITEM_OVER_LIMIT, '商品超过可购买数量'.intval($item['extend']['other_attr']['max_buy_num']).'件');
                        }
                    }
                }
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $_W['member']['uid'],
                    'itemid' => $itemid,
                    'total' => $total>0?$total:1,
                    'skuid' => $skuid,
                    'sku' => $sku?implode(' ', $sku):'',
                    'checked' => 1,
                    'dateline' => TIMESTAMP,
                );
                $new_id = M::t('superman_mall_cart')->insert($data);
                if (!$new_id) {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
            } else {
                $this->json(ERRNO::CART_ITEM_EXISTED);
            }
            $this->json(ERRNO::OK);
        } else if ($act == 'delete') {
            //删除购物车商品（支持批量删除）
            $cart_ids = isset($_GPC['cart_ids'])?$_GPC['cart_ids']:'';  //cart_ids is array
            if ($cart_ids) {
                $cart_ids = explode(',', $cart_ids);
                foreach ($cart_ids as $cart_id) {
                    $condition = array(
                        'uid' => $_W['member']['uid'],
                        'id' => $cart_id,
                    );
                    M::t('superman_mall_cart')->delete($condition);
                }
                $this->json(ERRNO::OK);
            }
            $this->json(ERRNO::INVALID_REQUEST);
        }
        include $this->template('cart');
    }
}
$obj = new Superman_mall_doMobileCart;