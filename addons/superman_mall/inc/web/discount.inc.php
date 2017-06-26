<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebDiscount extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_plugin');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('index'))?$_GPC['act']:'index';
        if ($act == 'index') {
            if (isset($_GPC['type']) && !empty($_GPC['type'])) {
                $type = in_array($_GPC['type'], array(1,2,3))?$_GPC['type']:1;
                $method = "discount_type{$type}";
                if (!method_exists($this, $method)) {
                    message('非法请求！', referer(), 'error');
                }
                $this->$method('admin', $this->shop['id']);
            } else {
                include $this->template('discount/index');
            }
        }
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('index'))?$_GPC['act']:'index';
        if ($act == 'index') {
            if (isset($_GPC['type']) && !empty($_GPC['type'])) {
                $type = in_array($_GPC['type'], array(1,2,3))?$_GPC['type']:1;
                $method = "discount_type{$type}";
                if (!method_exists($this, $method)) {
                    message('非法请求！', referer(), 'error');
                }
                $this->$method('shop_admin', $this->shop['id']);
            } else {
                include $this->template('discount/index');
            }
        }
    }

    //满包邮活动
    private function discount_type1($admin_type, $shopid) {
        global $_W, $_GPC;
        if (isset($this->shop['id']) && $this->shop['id']) {
            $plugin_permission = $this->check_plugin_permission('discount', $this->shop['id']);
        }
        $act = $_GPC['act'];
        $op = in_array($_GPC['op'], array('display', 'post', 'delete'))?$_GPC['op']:'display';
        if ($op == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'type' => 1
            );
            if ($this->shop['id']) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_shop_activity')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_activity')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        if ($li['isglobal'] == 1) { //全店活动
                            $li['item_total'] = '全店';
                        } else {
                            $li['item_total'] = M::t('superman_mall_shop_activity_item')->count(array(
                                'activityid' => $li['id']
                            ));
                            if ($li['item_total'] > 0) {
                                $sql = "SELECT a.cover FROM ".tablename('superman_mall_item')."AS a,".tablename('superman_mall_shop_activity_item')." AS b";
                                $sql .= " WHERE b.activityid=:activityid AND a.id=b.itemid";
                                $sql .= " LIMIT 0,4";
                                $params = array(
                                    ':activityid' => $li['id']
                                );
                                $li['activity_imgs'] = pdo_fetchall($sql, $params);
                            }
                            $li['item_total'] = $li['item_total'].'个';
                        }
                        $li['start'] = $li['starttime']?date('Y-m-d H:i:s', $li['starttime']):'';
                        $li['end'] = $li['endtime']?date('Y-m-d H:i:s', $li['endtime']):'';
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($op == 'post') {
            define('SUPERMAN_WEB_FULL_SCREEN', true);
            $id = $_GPC['id'];
            if ($id > 0) {
                //读取活动详情
                $row = M::t('superman_mall_shop_activity')->fetch($id);
                if ($row) {
                    if ($admin_type == 'shop_admin' && $this->shop['id'] != $row['shopid']) {
                        message('非法请求', referer(), 'error');
                    }
                    $row['extend'] = $row['extend']?iunserializer($row['extend']):array();
                    //读取活动商品
                    $sql = "SELECT a.id AS aiid,i.* FROM ".tablename('superman_mall_shop_activity_item')." AS a,".tablename('superman_mall_item')." AS i";
                    $sql .= " WHERE a.itemid=i.id AND a.activityid=:activityid";
                    $params = array(
                        ':activityid' => $id,
                    );
                    $row['items'] = pdo_fetchall($sql, $params, 'aiid');
                    if ($row['items']) {
                        foreach ($row['items'] as &$li) {
                            $li['ai_extend'] = $li['ai_extend']?iunserializer($li['ai_extend']):array();
                        }
                        unset($li);
                    }
                    $activity_time = array(
                        'start' => date('Y-m-d H:i:s', $row['starttime']),
                        'end' => date('Y-m-d H:i:s', $row['endtime']),
                    );
                    $inner_title = $row['inner_title'];
                    $activity_url = $_W['siteroot'].'app/'.$this->createMobileUrl('shop', array('shopid' => $row['shopid'], 'activityid' => $id));
                }
            } else {
                $this->check_web_shop();
                $activity_time = array(
                    'start' => date('Y-m-d H:i:s'),
                    'end' => date('Y-m-d H:i:s', strtotime('+1 week')),
                );
                $inner_title = '满包邮'.date('ymdHi');
            }
            //读取商品数据
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => isset($row['shopid'])?$row['shopid']:$this->shop['id'],
                'status' => 1,
                'special' => 0,
            );
            if (isset($_GPC['keyword']) && $_GPC['keyword']) {
                $filter['title'] = '# LIKE "%'.$_GPC['keyword'].'%"';
            }
            //过滤已打折商品
            if (isset($_GPC['isdiscount']) && $_GPC['isdiscount'] == 1) {
                $sql = "SELECT DISTINCT(i.itemid) FROM ".tablename('superman_mall_shop_activity')." AS a,".tablename('superman_mall_shop_activity_item')." AS i";
                $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=:type";
                $params = array(
                    ':uniacid' => $_W['uniacid'],
                    ':shopid' => $filter['shopid'],
                    ':type' => 1,
                );
                $discount_itemids = pdo_fetchall($sql, $params, 'itemid');
                if ($discount_itemids) {
                    $discount_itemids = array_keys($discount_itemids);
                    $filter['id'] = "# NOT IN (".implode(',', $discount_itemids).")";
                }
            }
            $item_list = array();
            $count = M::t('superman_mall_item')->count($filter);
            if ($count) {
                $item_list = M::t('superman_mall_item')->fetchall($filter, '', $start, $pagesize);
                if ($item_list) {
                    foreach ($item_list as &$item) {
                        /*//获取商品是否已有折扣
                        $activity_item = M::t('superman_mall_shop_activity_item')->fetch(array(
                            'itemid' => $item['id']
                        ));
                        if ($activity_item) {
                            $ai_extend = $activity_item['extend']?iunserializer($activity_item['extend']):array();
                            if (isset($ai_extend['value'])) {
                                $item['discount_price'] = $item['price']*$ai_extend['value'] /10;
                            }
                        }
                        unset($activity_item, $ai_extend);*/
                        //取商品规格
                        $sku = M::t('superman_mall_item_sku')->fetchall(array(
                            'itemid' => $item['id'],
                        ), 'ORDER BY id ASC', 0, -1);
                        if ($sku) {
                            $item['sku'] = $this->get_sku_value($sku);
                        }
                        unset($sku);
                    }
                    unset($item);
                }
                $pager = SupermanUtil::pagination($count, $pindex, $pagesize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => 'loadMoreItem'));
            }
            if ($_W['isajax']) {
                echo json_encode($item_list);
                die;
            }

            if (checksubmit()) {
                /*array(
                    'unit' => '',   //单位:yuan元,piece件
                    'value' => ''   //值
                ),*/
                $extend_value = $_GPC['extend_value'];  //值
                $extend_unit = $_GPC['extend_unit'];   //单位
                $extend = array();
                if ($extend_value) {
                    foreach ($extend_value as $k => $value) {
                        $extend[] = array(
                            'unit' => $extend_unit[$k],
                            'value' => $value,
                            'area' => ''
                        );
                    }
                    unset($k, $value);
                }
                $activity_time = $_GPC['activity_time'];
                $_data = array(
                    'title' => $_GPC['title'],
                    'starttime' => strtotime($activity_time['start']),
                    'endtime' => strtotime($activity_time['end']),
                    'isglobal' => $_GPC['isglobal']?1:0,
                    'extend' => $extend?iserializer($extend):'',
                    'inner_title' => $_GPC['inner_title'],
                );
                if (isset($row) && $row) {
                    M::t('superman_mall_shop_activity')->update($_data, array('id' => $row['id']));
                } else {
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    $_data['type'] = 1;
                    $_data['dateline'] = TIMESTAMP;
                    $id = M::t('superman_mall_shop_activity')->insert($_data);
                }

                $aiid_arr = $_GPC['aiid'];
                $itemid_arr = $_GPC['itemid'];
                if ($itemid_arr) {
                    foreach ($itemid_arr as $k => $itemid) {
                        $_itemdata = array(
                            'activityid' => $id,
                            'itemid' => $itemid,
                            'extend' => '',
                        );
                        if ($aiid_arr[$k] > 0) {
                            M::t('superman_mall_shop_activity_item')->update($_itemdata, array('id' => $aiid_arr[$k]));
                        } else {
                            M::t('superman_mall_shop_activity_item')->insert($_itemdata);
                        }
                    }
                    if ($row['items']) {
                        $aiids = array_keys($row['items']);
                        $diff = array_diff($aiids, $aiid_arr);
                        if ($diff) {
                            M::t('superman_mall_shop_activity_item')->delete(array(
                                'activityid' => $id,
                                'id' => $diff,
                            ));
                        }
                    }
                } else {
                    M::t('superman_mall_shop_activity_item')->delete(array(
                        'activityid' => $id,
                    ));
                }
                message('更新成功！', $this->createWebUrl('discount', array('act' => 'display', 'type' => 1)), 'success');
            }
        } else if ($op == 'delete') {
            $id = $_GPC['id'];
            if ($id <= 0) {
                message('非法请求', referer(), 'error');
            }
            $filter = array(
                'id' => $id,
                'uniacid' => $_W['uniacid'],
                'type' => 1
            );
            if ($admin_type == 'shop_admin') {
                $filter['shopid'] = $this->shop['id'];
            }
            $ret = M::t('superman_mall_shop_activity')->delete($filter);
            if ($ret === 1) {
                M::t('superman_mall_shop_activity_item')->delete(array(
                    'activityid' => $id
                ));
            }
            message('操作成功！', referer(), 'success');
        }
        include $this->template('discount/type1/index');
    }

    //限时折扣活动
    private function discount_type2($admin_type, $shopid) {
        global $_W, $_GPC;
        if (isset($this->shop['id']) && $this->shop['id']) {
            $plugin_permission = $this->check_plugin_permission('discount', $this->shop['id']);
        }
        $act = $_GPC['act'];
        $op = in_array($_GPC['op'], array('display', 'post', 'delete'))?$_GPC['op']:'display';
        if ($op == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'type' => 2
            );
            if ($this->shop['id']) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_shop_activity')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_activity')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        if ($li['isglobal'] == 1) { //全店活动
                            $li['item_total'] = '全店';
                        } else {
                            $li['item_total'] = M::t('superman_mall_shop_activity_item')->count(array(
                                'activityid' => $li['id']
                            ));
                            if ($li['item_total'] > 0) {
                                $sql = "SELECT * FROM ".tablename('superman_mall_item')."AS a,".tablename('superman_mall_shop_activity_item')." AS b";
                                $sql .= " WHERE b.activityid=:activityid AND a.id=b.itemid";
                                $sql .= " LIMIT 0,4";
                                $params = array(
                                    ':activityid' => $li['id']
                                );
                                $li['activity_imgs'] = pdo_fetchall($sql, $params);
                            }
                            $li['item_total'] = $li['item_total'].'个';
                        }
                        $li['start'] = $li['starttime']?date('Y-m-d H:i:s', $li['starttime']):'';
                        $li['end'] = $li['endtime']?date('Y-m-d H:i:s', $li['endtime']):'';
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($op == 'post') {
            define('SUPERMAN_WEB_FULL_SCREEN', true);
            $id = $_GPC['id'];
            if ($id > 0) {
                //读取活动详情
                $row = M::t('superman_mall_shop_activity')->fetch($id);
                if ($row) {
                    if ($admin_type == 'shop_admin' && $this->shop['id'] != $row['shopid']) {
                        message('非法请求', referer(), 'error');
                    }
                    $row['extend'] = $row['extend']?iunserializer($row['extend']):array();
                    //读取活动商品
                    $sql = "SELECT a.extend AS ai_extend,a.id AS aiid,i.* FROM ".tablename('superman_mall_shop_activity_item')." AS a,".tablename('superman_mall_item')." AS i";
                    $sql .= " WHERE a.itemid=i.id AND a.activityid=:activityid";
                    $params = array(
                        ':activityid' => $id,
                    );
                    $row['items'] = pdo_fetchall($sql, $params, 'aiid');
                    if ($row['items']) {
                        foreach ($row['items'] as &$li) {
                            $li['ai_extend'] = $li['ai_extend']?iunserializer($li['ai_extend']):array();
                        }
                        unset($li);
                    }
                    $activity_time = array(
                        'start' => date('Y-m-d H:i:s', $row['starttime']),
                        'end' => date('Y-m-d H:i:s', $row['endtime']),
                    );
                    $inner_title = $row['inner_title'];
                    $activity_url = $_W['siteroot'].'app/'.$this->createMobileUrl('shop', array('shopid' => $row['shopid'], 'activityid' => $id));
                }
            } else {
                $this->check_web_shop();
                $activity_time = array(
                    'start' => date('Y-m-d H:i:s'),
                    'end' => date('Y-m-d H:i:s', strtotime('+1 week')),
                );
                $inner_title = '限时打折'.date('ymdHi');
            }
            //读取商品数据
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => isset($row['shopid'])?$row['shopid']:$this->shop['id'],
                'status' => 1,
                'special' => 0,
            );
            if (isset($_GPC['keyword']) && $_GPC['keyword']) {
                $filter['title'] = '# LIKE "%'.$_GPC['keyword'].'%"';
            }
            //过滤已打折商品
            if (isset($_GPC['isdiscount']) && $_GPC['isdiscount'] == 1) {
                $sql = "SELECT DISTINCT(i.itemid) FROM ".tablename('superman_mall_shop_activity')." AS a,".tablename('superman_mall_shop_activity_item')." AS i";
                $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=:type";
                $params = array(
                    ':uniacid' => $_W['uniacid'],
                    ':shopid' => $filter['shopid'],
                    ':type' => 2,
                );
                $discount_itemids = pdo_fetchall($sql, $params, 'itemid');
                if ($discount_itemids) {
                    $discount_itemids = array_keys($discount_itemids);
                    $filter['id'] = "# NOT IN (".implode(',', $discount_itemids).")";
                }
            }
            $item_list = array();
            $count = M::t('superman_mall_item')->count($filter);
            if ($count) {
                $item_list = M::t('superman_mall_item')->fetchall($filter, '', $start, $pagesize);
                if ($item_list) {
                    foreach ($item_list as &$item) {
                        //获取商品是否已有折扣
                        $activity_item = M::t('superman_mall_shop_activity_item')->fetch(array(
                            'itemid' => $item['id']
                        ));
                        if ($activity_item) {
                            $ai_extend = $activity_item['extend']?iunserializer($activity_item['extend']):array();
                            if (isset($ai_extend['value'])) {
                                $item['discount_price'] = $item['price']*$ai_extend['value'] /10;
                            }
                        }
                        unset($activity_item, $ai_extend);
                        //取商品规格
                        $sku = M::t('superman_mall_item_sku')->fetchall(array(
                            'itemid' => $item['id'],
                        ), 'ORDER BY id ASC', 0, -1);
                        if ($sku) {
                            $item['sku'] = $this->get_sku_value($sku);
                        }
                        unset($sku);
                    }
                    unset($item);
                }
                $pager = SupermanUtil::pagination($count, $pindex, $pagesize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => 'loadMoreItem'));
            }
            if ($_W['isajax']) {
                echo json_encode($item_list);
                die;
            }

            if (checksubmit()) {
                /*array(
                    'unit' => '',   //单位:yuan元,piece件
                    'value' => ''   //值
                ),*/
                $activity_time = $_GPC['activity_time'];
                $extend = $_GPC['extend'];
                $_data = array(
                    'title' => $_GPC['title'],
                    'starttime' => strtotime($activity_time['start']),
                    'endtime' => strtotime($activity_time['end']),
                    'isglobal' => $_GPC['isglobal']?1:0,
                    'extend' => $extend?iserializer($extend):'',
                    'inner_title' => $_GPC['inner_title'],
                );
                if (isset($row) && $row) {
                    M::t('superman_mall_shop_activity')->update($_data, array('id' => $row['id']));
                } else {
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    $_data['type'] = 2;
                    $_data['dateline'] = TIMESTAMP;
                    $id = M::t('superman_mall_shop_activity')->insert($_data);
                }

                $aiid_arr = $_GPC['aiid'];
                $itemid_arr = $_GPC['itemid'];
                $extend_arr = $_GPC['item_extend'];
                if ($itemid_arr) {
                    foreach ($itemid_arr as $k => $itemid) {
                        $item_extend = array(
                            'value' => $extend_arr[$k]
                        );
                        $_itemdata = array(
                            'activityid' => $id,
                            'itemid' => $itemid,
                            'extend' => iserializer($item_extend),
                        );
                        if ($aiid_arr[$k] > 0) {
                            M::t('superman_mall_shop_activity_item')->update($_itemdata, array('id' => $aiid_arr[$k]));
                        } else {
                            M::t('superman_mall_shop_activity_item')->insert($_itemdata);
                        }
                    }
                    if ($row['items']) {
                        $aiids = array_keys($row['items']);
                        $diff = array_diff($aiids, $aiid_arr);
                        if ($diff) {
                            M::t('superman_mall_shop_activity_item')->delete(array(
                                'activityid' => $id,
                                'id' => $diff,
                            ));
                        }
                    }
                } else {
                    M::t('superman_mall_shop_activity_item')->delete(array(
                        'activityid' => $id,
                    ));
                }
                message('更新成功！', $this->createWebUrl('discount', array('act' => 'display', 'type' => 2)), 'success');
            }
        } else if ($op == 'delete') {
            $id = $_GPC['id'];
            if ($id <= 0) {
                message('非法请求', referer(), 'error');
            }
            $filter = array(
                'id' => $id,
                'uniacid' => $_W['uniacid'],
                'type' => 2
            );
            if ($admin_type == 'shop_admin') {
                $filter['shopid'] = $this->shop['id'];
            }
            $ret = M::t('superman_mall_shop_activity')->delete($filter);
            if ($ret === 1) {
                M::t('superman_mall_shop_activity_item')->delete(array(
                    'activityid' => $id
                ));
            }
            message('操作成功！', referer(), 'success');
        }
        include $this->template('discount/type2/index');
    }

    //满减优惠活动
    private function discount_type3($admin_type, $shopid) {
        global $_W, $_GPC;
        if (isset($this->shop['id']) && $this->shop['id']) {
            $plugin_permission = $this->check_plugin_permission('discount', $this->shop['id']);
        }
        $act = $_GPC['act'];
        $op = in_array($_GPC['op'], array('display', 'post', 'delete'))?$_GPC['op']:'display';
        if ($op == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'type' => 3
            );
            if ($this->shop['id']) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_shop_activity')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_shop_activity')->fetchall($filter, '', $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        if ($li['isglobal'] == 1) { //全店活动
                            $li['item_total'] = '全店';
                        } else {
                            $li['item_total'] = M::t('superman_mall_shop_activity_item')->count(array(
                                'activityid' => $li['id']
                            ));
                            if ($li['item_total'] > 0) {
                                $sql = "SELECT * FROM ".tablename('superman_mall_item')."AS a,".tablename('superman_mall_shop_activity_item')." AS b";
                                $sql .= " WHERE b.activityid=:activityid AND a.id=b.itemid";
                                $sql .= " LIMIT 0,4";
                                $params = array(
                                    ':activityid' => $li['id']
                                );
                                $li['activity_imgs'] = pdo_fetchall($sql, $params);
                            }
                            $li['item_total'] = $li['item_total'].'个';
                        }
                        $li['start'] = $li['starttime']?date('Y-m-d H:i:s', $li['starttime']):'';
                        $li['end'] = $li['endtime']?date('Y-m-d H:i:s', $li['endtime']):'';
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($op == 'post') {
            define('SUPERMAN_WEB_FULL_SCREEN', true);
            $id = $_GPC['id'];
            if (isset($_GPC['add']) && $_GPC['add'] == 'yes') {
                include $this->template('discount/type3/new');
                exit();
            }
            if ($id > 0) {
                //读取活动详情
                $row = M::t('superman_mall_shop_activity')->fetch($id);
                if ($row) {
                    if ($admin_type == 'shop_admin' && $this->shop['id'] != $row['shopid']) {
                        message('非法请求', referer(), 'error');
                    }
                    $row['extend'] = $row['extend']?iunserializer($row['extend']):array();
                    //读取活动商品
                    $sql = "SELECT a.id AS aiid,i.* FROM ".tablename('superman_mall_shop_activity_item')." AS a,".tablename('superman_mall_item')." AS i";
                    $sql .= " WHERE a.itemid=i.id AND a.activityid=:activityid";
                    $params = array(
                        ':activityid' => $id,
                    );
                    $row['items'] = pdo_fetchall($sql, $params, 'aiid');
                    //if ($row['items']) {
                        //foreach ($row['items'] as &$li) {
                        //   $li['ai_extend'] = $li['ai_extend']?iunserializer($li['ai_extend']):array();
                        //}
                        //unset($li);
                    //}
                    $activity_time = array(
                        'start' => date('Y-m-d H:i:s', $row['starttime']),
                        'end' => date('Y-m-d H:i:s', $row['endtime']),
                    );
                    $inner_title = $row['inner_title'];
                    $activity_url = $_W['siteroot'].'app/'.$this->createMobileUrl('shop', array('shopid' => $row['shopid'], 'activityid' => $id));
                }
            } else {
                $this->check_web_shop();
                $activity_time = array(
                    'start' => date('Y-m-d H:i:s'),
                    'end' => date('Y-m-d H:i:s', strtotime('+1 week')),
                );
            }
            //读取商品数据
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = isset($_GPC['export'])?-1:10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'shopid' => isset($row['shopid'])?$row['shopid']:$this->shop['id'],
                'status' => 1,
                'special' => 0,
            );
            if (isset($_GPC['keyword']) && $_GPC['keyword']) {
                $filter['title'] = '# LIKE "%'.$_GPC['keyword'].'%"';
            }
            //过滤已打折商品
            if (isset($_GPC['isdiscount']) && $_GPC['isdiscount'] == 1) {
                $sql = "SELECT DISTINCT(i.itemid) FROM ".tablename('superman_mall_shop_activity')." AS a,".tablename('superman_mall_shop_activity_item')." AS i";
                $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=:type";
                $params = array(
                    ':uniacid' => $_W['uniacid'],
                    ':shopid' => $filter['shopid'],
                    ':type' => 3,
                );
                $discount_itemids = pdo_fetchall($sql, $params, 'itemid');
                if ($discount_itemids) {
                    $discount_itemids = array_keys($discount_itemids);
                    $filter['id'] = "# NOT IN (".implode(',', $discount_itemids).")";
                }
            }
            $item_list = array();
            $count = M::t('superman_mall_item')->count($filter);
            if ($count) {
                $item_list = M::t('superman_mall_item')->fetchall($filter, '', $start, $pagesize);
                if ($item_list) {
                    foreach ($item_list as &$item) {
                        /*//获取商品是否已有折扣
                        $activity_item = M::t('superman_mall_shop_activity_item')->fetch(array(
                            'itemid' => $item['id']
                        ));
                        if ($activity_item) {
                            $ai_extend = $activity_item['extend']?iunserializer($activity_item['extend']):array();
                            if (isset($ai_extend['value'])) {
                                $item['discount_price'] = $item['price']*$ai_extend['value'] /10;
                            }
                        }
                        unset($activity_item, $ai_extend);*/
                        //取商品规格
                        $sku = M::t('superman_mall_item_sku')->fetchall(array(
                            'itemid' => $item['id'],
                        ), 'ORDER BY id ASC', 0, -1);
                        if ($sku) {
                            $item['sku'] = $this->get_sku_value($sku);
                        }
                        unset($sku);
                    }
                    unset($item);
                }
                $pager = SupermanUtil::pagination($count, $pindex, $pagesize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => 'loadMoreItem'));
            }
            if ($_W['isajax']) {
                echo json_encode($item_list);
                die;
            }

            if (checksubmit()) {
                /*array(
                    'full' => array( //满
                        'value' => '',  //值
                        'unit' => '',   //单位:yuan元,piece件
                    ),
                    'minus' => array(//减
                        'value' => '',  //值
                        'unit' => '',   //单位:yuan元,zhe折

                    )
                ),*/
                $full_value = $_GPC['full_value'];
                $full_unit = $_GPC['full_unit'];
                $minus_value = $_GPC['minus_value'];
                $minus_unit = $_GPC['minus_unit'];
                //var_dump($minus_unit);
                $extend = array();
                if ($full_value) {
                    foreach ($full_value as $k => $fv) {
                        $extend[] = array(
                            'full' => array(
                                'unit' => $full_unit[$k],
                                'value' => $fv,
                            ),
                            'minus' => array(
                                'value' => $minus_value[$k],
                                'unit' => $minus_unit[$k],
                            ),
                        );
                    }
                }
                $activity_time = $_GPC['activity_time'];
                $_data = array(
                    'title' => $_GPC['title'],
                    'starttime' => strtotime($activity_time['start']),
                    'endtime' => strtotime($activity_time['end']),
                    'isglobal' => $_GPC['isglobal']?1:0,
                    'extend' => $extend?iserializer($extend):'',
                    'inner_title' => $_GPC['inner_title'],
                );
                if (isset($row) && $row) {
                    M::t('superman_mall_shop_activity')->update($_data, array('id' => $row['id']));
                } else {
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    $_data['type'] = 3;
                    $_data['dateline'] = TIMESTAMP;
                    $id = M::t('superman_mall_shop_activity')->insert($_data);
                }

                $aiid_arr = $_GPC['aiid'];
                $itemid_arr = $_GPC['itemid'];
                if ($itemid_arr) {
                    foreach ($itemid_arr as $k => $itemid) {
                        $_itemdata = array(
                            'activityid' => $id,
                            'itemid' => $itemid,
                            'extend' => '',
                        );
                        if ($aiid_arr[$k] > 0) {
                            M::t('superman_mall_shop_activity_item')->update($_itemdata, array('id' => $aiid_arr[$k]));
                        } else {
                            M::t('superman_mall_shop_activity_item')->insert($_itemdata);
                        }
                    }
                    if ($row['items']) {
                        $aiids = array_keys($row['items']);
                        $diff = array_diff($aiids, $aiid_arr);
                        if ($diff) {
                            M::t('superman_mall_shop_activity_item')->delete(array(
                                'activityid' => $id,
                                'id' => $diff,
                            ));
                        }
                    }
                } else {
                    M::t('superman_mall_shop_activity_item')->delete(array(
                        'activityid' => $id,
                    ));
                }
                message('更新成功！', $this->createWebUrl('discount', array('act' => 'display', 'type' => 3)), 'success');
            }
        } else if ($op == 'delete') {
            $id = $_GPC['id'];
            if ($id <= 0) {
                message('非法请求', referer(), 'error');
            }
            $filter = array(
                'id' => $id,
                'uniacid' => $_W['uniacid'],
                'type' => 3
            );
            if ($admin_type == 'shop_admin') {
                $filter['shopid'] = $this->shop['id'];
            }
            $ret = M::t('superman_mall_shop_activity')->delete($filter);
            if ($ret === 1) {
                M::t('superman_mall_shop_activity_item')->delete(array(
                    'activityid' => $id
                ));
            }
            message('操作成功！', referer(), 'success');
        }
        include $this->template('discount/type3/index');
    }

    //获取sku内容
    private function get_sku_value($sku) {
        $arr = array();
        foreach ($sku as $s) {
            $valueids = explode(',', $s['valueids']);
            $attrs = M::t('superman_mall_item_attr')->fetchall_by_valueids($valueids, '', 0, -1, 'id');
            if ($attrs) {
                $title = '';
                foreach ($attrs as $attr) {
                    if ($title == '') {
                        $title = $attr['value'];
                    } else {
                        $title .= ' - '.$attr['value'];
                    }
                }
                $arr[] = array(
                    'title' => $title,
                    'price' => $s['price']
                );
            }
        }
        return $arr;
    }
}
$obj = new Superman_mall_doWebDiscount();