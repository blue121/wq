<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebItem extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_item');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'params', 'postage_tmpl', 'setattr', 'getcate'))?$_GPC['act']:'display';
        $nav['title'] = '商品管理';
        //一级分类加载
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'pid' => 0,
        );
        $pcids = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
        if ($act == 'display') {
            //更新排序
            if(checksubmit('orderby_submit')) {
                if ($_GPC['position']) {
                    foreach ($_GPC['position'] as $id=>$val) {
                        M::t('superman_mall_item')->update(array('position' => $val), array('id' => $id));
                    }
                    message('操作成功！', referer(), 'success');
                }
            }
            $status = $_GPC['status'];
            if (!in_array($status, array('upshelf', 'stockout', 'offshelf', 'forbid'))) {
                message('非法访问', referer(), 'error');
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            //商品数据
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'special' => 0,
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            if ($status == 'upshelf') {        //出售中（上架、隐藏）
                $nav['subtitle'] = '出售中商品';
                $filter['status'] = array(1, 3);
            } else if ($status == 'stockout') { //已售罄
                $nav['subtitle'] = '已售罄商品';
                $filter['total'] = 0;
            } else if ($status == 'offshelf') { //仓库中
                $nav['subtitle'] = '仓库中商品';
                $filter['status'] = 0;
            }  else if ($status == 'forbid') { //禁售
                $nav['subtitle'] = '禁售商品';
                $filter['status'] = 2;
            }
            if (trim($_GPC['title'])) {
                $filter['title'] = '# LIKE "%'.trim($_GPC['title']).'%"';
            }
            if ($_GPC['pcid'] > 0) {
                $filter['pcid'] = $_GPC['pcid'];
            }
            if ($_GPC['cid'] > 0) {
                $filter['cid'] = $_GPC['cid'];
            }
            if ($_GPC['ccid'] > 0) {
                $filter['ccid'] = $_GPC['ccid'];
            }
            $orderby = 'ORDER BY `position` DESC, id DESC';
            $total = M::t('superman_mall_item')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $li['shop'] = M::t('superman_mall_shop')->fetch($li['shopid']);
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $discount_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_DISCOUNT);
            if (isset($discount_setting['credit']['cash_open']) && isset($discount_setting['credit']['credit_type'])) {
                $uni_settings = uni_setting($_W['uniacid']);
                if ($uni_settings && $uni_settings['creditnames']) {
                    $credit_group = iunserializer($uni_settings['creditnames']);
                    $creditname = $credit_group[$discount_setting['credit']['credit_type']]['title'];
                }
            }
            $item = array();
            if ($id > 0) {  //编辑
                //商品数据加载
                $item = M::t('superman_mall_item')->fetch($id);
                if (empty($item)) {
                    message('商品不存在或已删除！', referer(), 'error');
                }
                $itemurl = $_W['siteroot'].'app/'.$this->createMobileUrl('detail', array('itemid' => $id));
                $item['album'] = !empty($item['album'])?iunserializer($item['album']):array();
                $item['user'] = user_single($item['userid']);
                //自定义属性加载
                $filter = array(
                    'itemid' => $id,
                );
                $orderby = 'ORDER BY `displayorder` DESC';
                $all_params =  M::t('superman_mall_item_params')->fetchall($filter, $orderby, 0, -1);
                //商品规格
                $filter = array(
                    'itemid' => $id,
                );
                $item['sku'] = M::t('superman_mall_item_sku')->fetchall($filter, null, 0, -1);
                //print_r($item['sku']);
                $item['attr_title'] = array();
                $item_value = array();
                if ($item['sku']) {
                    foreach ($item['sku'] as &$li) {
                        $valueids = explode(',', $li['valueids']);
                        $li['attr'] = M::t('superman_mall_item_attr')->fetchall_by_valueids($valueids, '', 0, -1, 'id');
                        if ($li['attr']) {
                            foreach ($li['attr'] as $attr) {
                                $title = $attr['title'];
                                if (!isset($item_value[$title]) || (isset($item_value[$title]) && !in_array($attr['value'], $item_value[$title]))) {
                                    $item_value[$title][] = $attr['value'];
                                }
                                if (!isset($item['attr_title'][$title])) {
                                    $item['attr_title'][$title] = M::t('superman_mall_item_value')->fetchall(array('attrid' => $attr['attrid']), 'ORDER BY displayorder DESC, id DESC', 0, -1);
                                }
                            }
                        }
                    }
                    unset($li);
                }
                //print_r($item_value);
                if ($item['attr_title']) {
                    foreach ($item['attr_title'] as $title=>&$value) {
                        foreach ($value as &$v) {
                            $v['checked'] = 0;
                            if (isset($item_value[$title]) && in_array($v['value'], $item_value[$title])) {
                                $v['checked'] = 1;
                            }
                        }
                    }
                    unset($value);
                    unset($v);
                }
                $item_attr = M::t('superman_mall_item_attr')->fetchall(array('uniacid' => $_W['uniacid']), 'ORDER BY displayorder DESC, id DESC', 0, -1);
                //邮费模板
                if ($item['postage_tmplid'] > 0) {
                    $item['postage_select'] = 2;
                } else {
                    $item['postage_select'] = 1;
                }
                $item['partner_attr'] = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $id));
                $item['shop'] = M::t('superman_mall_shop')->fetch($item['shopid']);
                //print_r($item);die;
                //print_r($item_attr);die;
            } else {    //新增商品的内容初始化
                $this->check_web_shop();
                $item['shopid'] = $this->shop['id'];
                $item['type'] = 1;
                $item['status'] = 1;
                $item['minus_total'] = 1;
                $item['postage_select'] = 1;
                $item['activity_time'] = array(
                    'start' => date('Y-m-d H:i:s'),
                    'end' => date('Y-m-d H:i:s', strtotime('+1 months')),
                );
            }
            //邮费模版
            $filter = array(
                'shopid' => $item['shopid'],
            );
            $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
            $postage_tmpl = M::t('superman_mall_postage_template')->fetchall($filter, '', 0, -1);
            if (checksubmit('submit')) {
                //$activity_time = $_GPC['activity_time'];
                $_data = array(
                    'userid' => $_W['uid'], //最后修改人
                    'type' => !empty($_GPC['type'])?$_GPC['type']:1,
                    'title' => $_GPC['title'],
                    'subtitle' => $_GPC['subtitle'],
                    'pcid' => intval($_GPC['pcid']),
                    'cid' => intval($_GPC['cid']),
                    'ccid' => intval($_GPC['ccid']),
                    'number' => $_GPC['number'],
                    'cover' => $_GPC['cover'],
                    'album' => $_GPC['album'] != ''?iserializer($_GPC['album']):'',
                    'status' => intval($_GPC['status']),
                    'summary' => $_GPC['summary'],
                    'minus_total' => in_array($_GPC['minus_total'], array(1, 2))?$_GPC['minus_total']:1,
                    'description' => $_GPC['description'],
                    'total' => intval($_GPC['total']),
                    'price' => $_GPC['price'],
                    'postage' => $_GPC['postage'],
                    'market_price' => $_GPC['market_price'],
                    'cost_price' => $_GPC['cost_price'],
                    'special' => 0,
                    'extend' => '',
                    'weight' => $_GPC['weight'],
                    'isreceipt' => intval($_GPC['isreceipt'])?1:0,
                    'isrepair' => intval($_GPC['isrepair'])?1:0,
                    'ischeckout' => intval($_GPC['ischeckout'])?1:0,
                    'cash_credit' => floatval($_GPC['cash_credit']),
                    'partner_open' => intval($_GPC['partner_open'])?1:0,
                    'customs_clearance' => $_GPC['customs_clearance']?1:0,
                    'delivery_mode' => ($_GPC['delivery_mode'] == 0 || in_array($_GPC['delivery_mode'], array(1,2)))?$_GPC['delivery_mode']:0,
                    'iscash' => $_GPC['iscash']?1:0,
                );
                if ($_GPC['postage_select'] == 2 && $_GPC['postage_tmplid'] > 0) {
                    $_data['postage_tmplid'] = intval($_GPC['postage_tmplid']); //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                } else {
                    $_data['postage_tmplid'] = 0; //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                }
                if ($id > 0) {      //编辑
                    $_data['updatetime'] = TIMESTAMP;
                    M::t('superman_mall_item')->update($_data, array('id' => $id));
                } else {            //添加
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    $_data['createtime'] = TIMESTAMP;
                    $id = M::t('superman_mall_item')->insert($_data);
                    if (!$id) {
                        message('数据库错误(insert superman_mall_item failed)', '', 'error');
                    }
                }
                //更新自定义属性
                if (isset($_GPC['param_id']) && $_GPC['param_id']) {
                    $count = count($_GPC['param_id']);
                    foreach ($_GPC['param_id'] as $key => $value) {
                        if ($_GPC['param_name'][$key] == '' && $_GPC['param_value'][$key] == '') {
                            continue;
                        }
                        $data = array(
                            'itemid' => $id,
                            'name' => $_GPC['param_name'][$key],
                            'value' => $_GPC['param_value'][$key],
                            'displayorder' => $count - $key,
                        );
                        if (empty($_GPC['param_id'][$key])) {   //insert
                            M::t('superman_mall_item_params')->insert($data);
                        } else {                                //edit
                            M::t('superman_mall_item_params')->update($data, array('id' => $_GPC['param_id'][$key]));
                        }
                    }
                }
                //更新商品规格
                if ($_GPC['item_spec_switch'] == 0) {   //未开启sku
                    $this->clean_sku($id);
                } else {
                    if (isset($_GPC['sku']) && $_GPC['sku']) {
                        //WeUtility::logging('debug', '[sku] item_spec_switch='.$_GPC['item_spec_switch']);
                        if ($_GPC['item_spec_switch'] == 2) { //规格发生变化，先删除，后添加
                            $this->clean_sku($id);
                        }
                        $sales = 0;
                        foreach ($_GPC['sku']['valueid'] as $k=>$v) {
                            $valueids = explode(',', $v);
                            $skuid = isset($_GPC['sku']['id'][$k])?$_GPC['sku']['id'][$k]:0;
                            $data = array(
                                'valueids' => implode(',', $valueids),
                                'cover' => '', //TODO
                                'weight' => $_GPC['sku']['weight'][$k],
                                'market_price' => $_GPC['sku']['market_price'][$k],
                                'cost_price' => $_GPC['sku']['cost_price'][$k],
                                'price' => $_GPC['sku']['price'][$k],
                                'total' => $_GPC['sku']['total'][$k],
                                'sales' => $_GPC['sku']['sales'][$k],
                            );
                            $sales += $_GPC['sku']['sales'][$k];
                            //WeUtility::logging('debug', '[sku] data='.var_export($data, true));
                            /*print_r($data);
                            echo '<hr>';*/
                            if ($skuid && $_GPC['item_spec_switch'] != 2) {
                                M::t('superman_mall_item_sku')->update($data, array('id' => $skuid));
                            } else {
                                $data['itemid'] = $id;
                                M::t('superman_mall_item_sku')->insert($data);
                            }
                        }
                        if ($sales) {
                            pdo_update('superman_mall_item', array('sales' => $sales), array('id' => $id));
                        }
                        //die;
                    }
                }
                //更新商品分销属性
                if ($_GPC['partner_open'] == '1') {
                    $data = array(
                        'commission_custom' => intval($_GPC['commission_custom'])?1:0,
                        'commission_show' => intval($_GPC['commission_show'])?1:0,
                        'discount_rate' => $_GPC['discount_rate'],
                        'discount_value' => $_GPC['discount_value'],
                        'commission1_rate' => $_GPC['commission1_rate'],
                        'commission1_value' => $_GPC['commission1_value'],
                        'commission2_rate' => $_GPC['commission2_rate'],
                        'commission2_value' => $_GPC['commission2_value'],
                        'commission3_rate' => $_GPC['commission3_rate'],
                        'commission3_value' => $_GPC['commission3_value'],
                    );
                    $partner_attr = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $id));
                    if ($partner_attr) {
                        M::t('superman_mall_item_partner_attr')->update($data, array('itemid' => $id));
                    } else {
                        $data['itemid'] = $id;
                        M::t('superman_mall_item_partner_attr')->insert($data);
                    }
                }
                message('更新成功！', $this->createWebUrl('item', array('act' => 'display','status' => 'upshelf')));
            }
        } else if ($act == 'delete') {
            if (empty($_SERVER['HTTP_REFERER'])) {
                message('非法请求！', '', 'error');
            }
            $id = intval($_GPC['id']);
            M::t('superman_mall_item')->delete(array('id' => $id));

            //更新商品属性(产品参数)
            M::t('superman_mall_item_params')->delete(array('itemid' => $id));

            //清理商品规格
            $this->clean_sku($id);

            message('删除成功！', referer(), 'success');
        } else if ($act == 'params') {
            if ($_GPC['behavior'] == 'add') {			//新增模板
                include $this->template('item-params');
                exit;
            } elseif ($_GPC['behavior'] == 'delete') {	//删除模板
                $paramid = $_GPC['paramid'];
                if ($paramid) {
                    M::t('superman_mall_item_params')->delete(array('id' => $paramid));
                    echo 'success';
                    exit;
                } else {
                    echo '非法请求';
                    exit;
                }
            }
        } else if ($act == 'postage_tmpl') {
            $filter = array(
                'shopid' => intval($_GPC['shopid']),
            );
            $list = M::t('superman_mall_postage_template')->fetchall($filter, '', 0, -1);
            echo json_encode($list);
            die;
        } else if ($act == 'setattr') {
            $id = intval($_GPC['id']);
            $field = $_GPC['field'];
            $value = $_GPC['value'];
            if ($id == '' || $field == '' || $value == '' || !in_array($field, array('status'))) {
                echo '非法请求';
                exit;
            }
            $data = array(
                $field => $value==1?0:1
            );
            $ret = M::t('superman_mall_item')->update($data, array('id' => $id));
            if ($ret !== false) {
                echo 'success';
            } else {
                echo '系统错误';
            }
            exit;
        } else if ($act == 'getcate') {
            $cid = intval($_GPC['cid']);
            if (!$cid) {
                exit;
            }
            $filter = array(
                'pid' => $cid,
                'isshow' => 1
            );
            $childs = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
            echo json_encode($childs);
            exit;
        }
		include $this->template('item');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'params', 'postage_tmpl', 'setattr', 'getcate'))?$_GPC['act']:'display';
        $nav['title'] = '商品管理';
        //一级分类加载
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'pid' => 0,
        );
        $pcids = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
        $status = $_GPC['status'];
        if ($act == 'display') {
            if ($status == 'upshelf') {        //出售中（上架、隐藏）
                $nav['subtitle'] = '出售中商品';
                $filter['status'] = array(1, 3);
            } else if ($status == 'stockout') { //已售罄
                $nav['subtitle'] = '已售罄商品';
                $filter['total'] = 0;
            } else if ($status == 'offshelf') { //仓库中
                $nav['subtitle'] = '仓库中商品';
                $filter['status'] = 0;
            }  else if ($status == 'forbid') { //禁售
                $nav['subtitle'] = '禁售商品';
                $filter['status'] = 2;
            }
            //更新排序
            if(checksubmit('submit')) {
                $displayorder = $_GPC['displayorder'];
                if ($displayorder) {
                    foreach ($displayorder as $id=>$val) {
                        M::t('superman_mall_item')->update(array('displayorder' => $val), array('id' => $id));
                    }
                    message('操作成功！', referer(), 'success');
                }
            }
            if (!in_array($status, array('upshelf', 'stockout', 'offshelf', 'forbid'))) {
                message('非法访问', referer(), 'error');
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            //商品数据
            $filter = array(
                'shopid' => $this->shop['id'],
                'special' => 0,
            );
            if ($status == 'upshelf') {        //出售中
                $filter['status'] = 1;
            } else if ($status == 'stockout') { //已售罄
                $filter['total'] = 0;
            } else if ($status == 'offshelf') { //仓库中
                $filter['status'] = 0;
            }  else if ($status == 'forbid') { //禁售
                $filter['status'] = 2;
            }
            if (trim($_GPC['title'])) {
                $filter['title'] = '# LIKE "%'.trim($_GPC['title']).'%"';
            }
            if ($_GPC['pcid'] > 0) {
                $filter['pcid'] = $_GPC['pcid'];
            }
            if ($_GPC['cid'] > 0) {
                $filter['cid'] = $_GPC['cid'];
            }
            if ($_GPC['ccid'] > 0) {
                $filter['ccid'] = $_GPC['ccid'];
            }
            $orderby = 'ORDER BY displayorder DESC, id DESC';
            $total = M::t('superman_mall_item')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            define('SUPERMAN_WEB_FULL_SCREEN', true);
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $item = array();
            //商户功能模块权限
            $partner_permission = $this->check_plugin_permission('partner', $this->shop['id']);
            $discount_permission = $this->check_plugin_permission('discount', $this->shop['id']);
            $partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
            $discount_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_DISCOUNT);
            if (isset($discount_setting['credit']['cash_open']) && isset($discount_setting['credit']['credit_type'])) {
                $uni_settings = uni_setting($_W['uniacid']);
                if ($uni_settings && $uni_settings['creditnames']) {
                    $credit_group = iunserializer($uni_settings['creditnames']);
                    $creditname = $credit_group[$discount_setting['credit']['credit_type']]['title'];
                }
            }
            if ($id > 0) {  //编辑
                //商品数据加载
                $item = M::t('superman_mall_item')->fetch($id);
                if (empty($item)) {
                    message('商品不存在或已删除！', referer(), 'error');
                }
                $itemurl = $_W['siteroot'].'app/'.$this->createMobileUrl('detail', array('itemid' => $id));
                $item['album'] = !empty($item['album'])?iunserializer($item['album']):array();
                $item['user'] = user_single($item['userid']);
                //自定义属性加载
                $filter = array(
                    'itemid' => $id,
                );
                $orderby = 'ORDER BY displayorder DESC';
                $all_params =  M::t('superman_mall_item_params')->fetchall($filter, $orderby, 0, -1);
                //商品规格
                $filter = array(
                    'itemid' => $id,
                );
                $item['sku'] = M::t('superman_mall_item_sku')->fetchall($filter, 'ORDER BY id ASC', 0, -1);
                $item['attr_title'] = array();
                $item_value = array();
                if ($item['sku']) {
                    foreach ($item['sku'] as &$li) {
                        $valueids = explode(',', $li['valueids']);
                        $li['attr'] = M::t('superman_mall_item_attr')->fetchall_by_valueids($valueids, '', 0, -1, 'id');
                        if ($li['attr']) {
                            foreach ($li['attr'] as $attr) {
                                $title = $attr['title'];
                                if (!isset($item_value[$title]) || (isset($item_value[$title]) && !in_array($attr['value'], $item_value[$title]))) {
                                    $item_value[$title][] = $attr['value'];
                                }
                                if (!isset($item['attr_title'][$title])) {
                                    $item['attr_title'][$title] = M::t('superman_mall_item_value')->fetchall(array('attrid' => $attr['attrid']), 'ORDER BY displayorder DESC, id DESC', 0, -1);

                                }
                            }
                        }
                    }
                    unset($li);
                }
                //print_r($item_value);
                if ($item['attr_title']) {
                    foreach ($item['attr_title'] as $title=>&$value) {
                        foreach ($value as &$v) {
                            $v['checked'] = 0;
                            if (isset($item_value[$title]) && in_array($v['value'], $item_value[$title])) {
                                $v['checked'] = 1;
                            }
                        }
                    }
                    unset($value);
                    unset($v);
                }
                $item_attr = M::t('superman_mall_item_attr')->fetchall(array('uniacid' => $_W['uniacid']), 'ORDER BY displayorder DESC, id DESC', 0, -1);
                //邮费模板
                if ($item['postage_tmplid'] > 0) {
                    $item['postage_select'] = 2;
                } else {
                    $item['postage_select'] = 1;
                }
                //print_r($item);die;
                //print_r($item_attr);die;
            } else {    //新增商品的内容初始化
                $item['type'] = 1;
                $item['status'] = 1;
                $item['minus_total'] = 1;
                $item['postage_select'] = 1;
                $item['activity_time'] = array(
                    'start' => date('Y-m-d H:i:s'),
                    'end' => date('Y-m-d H:i:s', strtotime('+1 months')),
                );
            }
            //邮费模版
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $this->shop['id']
            );
            $item['partner_attr'] = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $id));
            $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
            $postage_tmpl = M::t('superman_mall_postage_template')->fetchall($filter, '', 0, -1);

            if (checksubmit('submit')) {
                if ($_GPC['partner_open'] == 1) {
                    if ($partner_setting['base']['commission1_rate_max'] > 0) {
                        if ($_GPC['commission1_rate'] > $partner_setting['base']['commission1_rate_max']) {
                            message('一级分销佣金不能超过'.$partner_setting['base']['commission1_rate_max'].'%！', referer(), 'error');
                        }
                    }
                    if ($partner_setting['base']['commission1_value_max'] > 0) {
                        if ($_GPC['commission1_value'] > $partner_setting['base']['commission1_value_max']) {
                            message('一级分销佣金不能超过'.$partner_setting['base']['commission1_value_max'].'元！', referer(), 'error');
                        }
                    }
                    if ($partner_setting['base']['commission2_rate_max'] > 0) {
                        if ($_GPC['commission2_rate'] > $partner_setting['base']['commission2_rate_max']) {
                            message('二级分销佣金不能超过'.$partner_setting['base']['commission2_rate_max'].'%！', referer(), 'error');
                        }
                    }
                    if ($partner_setting['base']['commission2_value_max'] > 0) {
                        if ($_GPC['commission2_value'] > $partner_setting['base']['commission2_value_max']) {
                            message('二级分销佣金不能超过'.$partner_setting['base']['commission2_value_max'].'元！', referer(), 'error');
                        }
                    }
                    if ($partner_setting['base']['commission3_rate_max'] > 0) {
                        if ($_GPC['commission3_rate'] > $partner_setting['base']['commission3_rate_max']) {
                            message('三级分销佣金不能超过'.$partner_setting['base']['commission3_rate_max'].'%！', referer(), 'error');
                        }
                    }
                    if ($partner_setting['base']['commission3_value_max'] > 0) {
                        if ($_GPC['commission3_value'] > $partner_setting['base']['commission3_value_max']) {
                            message('三级分销佣金不能超过'.$partner_setting['base']['commission3_value_max'].'元！', referer(), 'error');
                        }
                    }
                    if ($partner_setting['base']['discount_rate_min'] > 0 && $_GPC['discount_rate'] > 0 ) {
                        if ($_GPC['discount_rate'] < $partner_setting['base']['discount_rate_min']) {
                            message('分销商优惠价最低'.$partner_setting['base']['discount_rate_min'].'%！', referer(), 'error');
                        }
                    }
                    if ($partner_setting['base']['discount_value_max'] > 0) {
                        if ($_GPC['discount_value'] > $partner_setting['base']['discount_value_max']) {
                            message('分销商优惠价不能超过'.$partner_setting['base']['discount_value_max'].'元！', referer(), 'error');
                        }
                    }
                }

                //$activity_time = $_GPC['activity_time'];
                $_data = array(
                    'userid' => $_W['uid'], //最后修改人
                    'type' => !empty($_GPC['type'])?$_GPC['type']:1,
                    'title' => $_GPC['title'],
                    'subtitle' => $_GPC['subtitle'],
                    'pcid' => intval($_GPC['pcid']),
                    'cid' => intval($_GPC['cid']),
                    'ccid' => intval($_GPC['ccid']),
                    'number' => $_GPC['number'],
                    'cover' => $_GPC['cover'],
                    'album' => $_GPC['album'] != ''?iserializer($_GPC['album']):'',
                    'status' => intval($_GPC['status']),
                    'summary' => $_GPC['summary'],
                    'minus_total' => in_array($_GPC['minus_total'], array(1, 2))?$_GPC['minus_total']:1,
                    'description' => $_GPC['description'],
                    'total' => intval($_GPC['total']),
                    'price' => $_GPC['price'],
                    'displayorder' => $_GPC['displayorder'],
                    'postage' => $_GPC['postage'],
                    'market_price' => $_GPC['market_price'],
                    'cost_price' => $_GPC['cost_price'],
                    'special' => 0,
                    'extend' => '',
                    'weight' => $_GPC['weight'],
                    'isreceipt' => intval($_GPC['isreceipt'])?1:0,
                    'isrepair' => intval($_GPC['isrepair'])?1:0,
                    'ischeckout' => intval($_GPC['ischeckout'])?1:0,
                    'cash_credit' => floatval($_GPC['cash_credit']),
                    'partner_open' => intval($_GPC['partner_open'])?1:0,
                    'customs_clearance' => $_GPC['customs_clearance']?1:0,
                    'delivery_mode' => ($_GPC['delivery_mode'] == 0 || in_array($_GPC['delivery_mode'], array(1,2)))?$_GPC['delivery_mode']:0,
                    'iscash' => $_GPC['iscash']?1:0,
                );
                if ($_GPC['postage_select'] == 2 && $_GPC['postage_tmplid'] > 0) {
                    $_data['postage_tmplid'] = intval($_GPC['postage_tmplid']); //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                } else {
                    $_data['postage_tmplid'] = 0; //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                }
                if ($id > 0) {      //编辑
                    if ($item['status'] == 2) { //禁售商品不允许修改状态
                        unset($_data['status']);
                    }
                    $_data['updatetime'] = TIMESTAMP;
                    M::t('superman_mall_item')->update($_data, array('id' => $id));
                } else {            //添加
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    $_data['createtime'] = TIMESTAMP;
                    $id = M::t('superman_mall_item')->insert($_data);
                    if (!$id) {
                        message('数据库错误(insert superman_mall_item failed)', '', 'error');
                    }
                }
                //更新自定义属性
                if (isset($_GPC['param_id']) && $_GPC['param_id']) {
                    $count = count($_GPC['param_id']);
                    foreach ($_GPC['param_id'] as $key=>$value) {
                        if ($_GPC['param_name'][$key] == '' && $_GPC['param_value'][$key] == '') {
                            continue;
                        }
                        $data = array(
                            'itemid' => $id,
                            'name' => $_GPC['param_name'][$key],
                            'value' => $_GPC['param_value'][$key],
                            'displayorder' => $count - $key,
                        );
                        if (empty($_GPC['param_id'][$key])) {   //insert
                            M::t('superman_mall_item_params')->insert($data);
                        } else {                                //edit
                            M::t('superman_mall_item_params')->update($data, array('id' => $_GPC['param_id'][$key]));
                        }
                    }
                }
                //更新商品规格
                if ($_GPC['item_spec_switch'] == 0) {   //未开启sku
                    $this->clean_sku($id);
                } else {
                    if (isset($_GPC['sku']) && $_GPC['sku']) {
                        //WeUtility::logging('debug', '[sku] item_spec_switch='.$_GPC['item_spec_switch']);
                        if ($_GPC['item_spec_switch'] == 2) { //规格发生变化，先删除，后添加
                            $this->clean_sku($id);
                        }
                        $sales = 0;
                        foreach ($_GPC['sku']['valueid'] as $k=>$v) {
                            $valueids = explode(',', $v);
                            $skuid = isset($_GPC['sku']['id'][$k])?$_GPC['sku']['id'][$k]:0;
                            $data = array(
                                'valueids' => implode(',', $valueids),
                                'cover' => '', //TODO
                                'weight' => $_GPC['sku']['weight'][$k],
                                'market_price' => $_GPC['sku']['market_price'][$k],
                                'cost_price' => $_GPC['sku']['cost_price'][$k],
                                'price' => $_GPC['sku']['price'][$k],
                                'total' => $_GPC['sku']['total'][$k],
                                'sales' => $_GPC['sku']['sales'][$k],
                            );
                            $sales += $_GPC['sku']['sales'][$k];
                            //WeUtility::logging('debug', '[sku] data='.var_export($data, true));
                            /*print_r($data);
                            echo '<hr>';*/
                            if ($skuid && $_GPC['item_spec_switch'] != 2) {
                                M::t('superman_mall_item_sku')->update($data, array('id' => $skuid));
                            } else {
                                $data['itemid'] = $id;
                                M::t('superman_mall_item_sku')->insert($data);
                            }
                        }
                        if ($sales) {
                            pdo_update('superman_mall_item', array('sales' => $sales), array('id' => $id));
                        }
                        //die;
                    }
                }
                if ($_GPC['partner_open'] == '1') {
                    $data = array(
                        'commission_custom' => intval($_GPC['commission_custom'])?1:0,
                        'commission_show' => intval($_GPC['commission_show'])?1:0,
                        'commission1_rate' => $_GPC['commission1_rate'],
                        'commission1_value' => $_GPC['commission1_value'],
                        'commission2_rate' => $_GPC['commission2_rate'],
                        'commission2_value' => $_GPC['commission2_value'],
                        'commission3_rate' => $_GPC['commission3_rate'],
                        'commission3_value' => $_GPC['commission3_value'],
                        'discount_rate' => $_GPC['discount_rate'],
                        'discount_value' => $_GPC['discount_value'],
                    );
                    $partner_attr = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $id));
                    if ($partner_attr) {
                        //update
                        M::t('superman_mall_item_partner_attr')->update($data, array('itemid' => $id));
                    } else {
                        //insert
                        $data['itemid'] = $id;
                        M::t('superman_mall_item_partner_attr')->insert($data);
                    }
                }
                message('更新成功！', $this->createWebUrl('item', array('act' => 'display','status' => 'upshelf')));
            }
        } else if ($act == 'delete') {
            if (empty($_SERVER['HTTP_REFERER'])) {
                message('非法请求！', '', 'error');
            }
            $id = intval($_GPC['id']);
            M::t('superman_mall_item')->delete(array('id' => $id));

            //更新商品属性(产品参数)
            M::t('superman_mall_item_params')->delete(array('itemid' => $id));

            //清理商品规格
            $this->clean_sku($id);

            message('删除成功！', referer(), 'success');
        } else if ($act == 'params') {
            if ($_GPC['behavior'] == 'add') {			//新增模板
                //include $this->template('item-params');
                include $this->template('item/params');
                exit;
            } elseif ($_GPC['behavior'] == 'delete') {	//删除模板
                $paramid = $_GPC['paramid'];
                if ($paramid) {
                    M::t('superman_mall_item_params')->delete(array('id' => $paramid));
                    echo 'success';
                    exit;
                } else {
                    echo '非法请求';
                    exit;
                }
            }
        } else if ($act == 'postage_tmpl') {
            $filter = array(
                'shopid' => $this->shop['id'],
            );
            $list = M::t('superman_mall_postage_template')->fetchall($filter, '', 0, -1);
            echo json_encode($list);
            die;
        } else if ($act == 'setattr') {
            $id = intval($_GPC['id']);
            $field = $_GPC['field'];
            $value = $_GPC['value'];
            if ($id == '' || $field == '' || $value == '' || !in_array($field, array('status'))) {
                echo '非法请求';
                exit;
            }
            $data = array(
                $field => $value==1?0:1
            );
            $ret = M::t('superman_mall_item')->update($data, array('id' => $id));
            if ($ret !== false) {
                echo 'success';
            } else {
                echo '系统错误';
            }
            exit;
        } else if ($act == 'getcate') {
            $cid = intval($_GPC['cid']);
            if (!$cid) {
                exit;
            }
            $filter = array(
                'pid' => $cid,
                'isshow' => 1
            );
            $childs = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
            echo json_encode($childs);
            exit;
        }
        //include $this->template('item');
        include $this->template('item/index');
    }
    private function clean_sku($itemid) {
        if ($itemid) {
            M::t('superman_mall_item_sku')->delete(array('itemid' => $itemid));
//            M::t('superman_mall_cart')->update(array('skuid' => 0), array('itemid' => $itemid));
        }
    }
}

$obj = new Superman_mall_doWebItem;
