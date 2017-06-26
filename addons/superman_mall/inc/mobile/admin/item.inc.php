<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
$this->check_user_permission('superman_mall_menu_mobile_admin_item');
if ($act == 'display') {
    $title = '商品列表';
    $pindex = max(1, intval($_GPC['page']));
    $pagesize = 10;
    $start = ($pindex - 1) * $pagesize;

    $status = in_array($_GPC['status'], array('upshelf', 'stockout', 'offshelf'))?$_GPC['status']:'upshelf';
    $keyword = $_GPC['keyword'];
    $filter = array(
        'shopid' => $this->shop['id'],
        'special' => 0,
    );
    if ($status == 'upshelf') {
        $filter['status'] = array(1, 3);
    } else if ($status == 'stockout') {
        $filter['total'] = 0;
        $filter['status'] = array(1, 3);
    } else if ($status == 'offshelf') {
        $filter['status'] = 0;
    }
    if ($keyword != '') {
        $filter['title'] = '# LIKE "%'.$keyword.'%"';
    }
    $orderby = 'ORDER BY `position` DESC, id DESC';
    $list = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
    //加载更多
    if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
        die(json_encode($list));
    }
} else if ($act == 'post') {
    $title = '商品编辑';
    $id = intval($_GPC['id']);
    if ($id) {
        $item = M::t('superman_mall_item')->fetch($id);
        if (!$item || $item['shopid'] != $this->shop['id']) {
            $this->message('非法请求');
        }
        //商品sku
        $skus = M::t('superman_mall_item_sku')->fetchall(array('itemid' => $id), 'ORDER BY id ASC', 0, -1);
        if ($skus) {
            foreach ($skus as &$s) {
                $valueids = explode(',', $s['valueids']);
                $attrs = M::t('superman_mall_item_attr')->fetchall_by_valueids($valueids, '', 0, -1, 'id');
                if ($attrs) {
                    foreach ($attrs as $attr) {
                        $s['attr_titles'][] = $attr['value'];
                    }
                }
            }
            unset($s);
        }
        if (checksubmit('submit1')) {
            $id_arr = $_GPC['ids'];
            $price_arr = $_GPC['price'];
            $total_arr = $_GPC['total'];
            $total = 0;     //计算总库存
            $price = 0.00;  //计算最高价
            foreach ($id_arr as $k => $skuid) {
                $total += $total_arr[$k];
                $price = $price > $price_arr[$k]?$price:$price_arr[$k];
                if ($skuid > 0) {
                    $_data = array(
                        'price' => $price_arr[$k],
                        'total' => $total_arr[$k],
                    );
                    //更新商品sku库存和价格
                    M::t('superman_mall_item_sku')->update($_data, array('id' => $skuid));
                }
            }
            //更新商品总库存和价格
            M::t('superman_mall_item')->update(array(
                'price' => $price,
                'total' => $total,
                'updatetime' => TIMESTAMP
            ), array('id' => $id));
            $this->message('操作成功，跳转中...', $this->createMobileUrl('admin', array('route' => 'item.display', 'status' => $_GPC['status'])), 'success');
        }
    }
} else if ($act == 'setattr') {
    $id = intval($_GPC['id']);
    $field = $_GPC['field'];
    $value = $_GPC['value'];
    if ($id == '' || $field == '' || $value == '' || !in_array($field, array('status'))) {
        $this->json(ERRNO::INVALID_REQUEST);
    }
    $data = array(
        $field => $value?1:0,
        'updatetime' => TIMESTAMP
    );
    $ret = M::t('superman_mall_item')->update($data, array('id' => $id));
    if ($ret !== false) {
        $this->json(ERRNO::OK);
    } else {
        $this->json(ERRNO::SYSTEM_ERROR);
    }
}
include $this->template('item/index');