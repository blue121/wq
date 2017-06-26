<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebMgroupon extends Superman {
    public function __construct() {
        parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_plugin_mgroupon');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
    }
    public function do_admin() {
        global $_W, $_GPC;
        if (isset($this->shop['id']) && $this->shop['id']) {
            $plugin_permission = $this->check_plugin_permission('mgroupon', $this->shop['id']);
        }
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'setting')) ? $_GPC['act'] : 'display';
        $nav['title'] = '功能模块';
        //一级分类加载
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'pid' => 0
        );
        $pcids = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
        if ($act == 'display') {
            $nav['subtitle'] = '拼团';
            //更新排序
            if(checksubmit('submit')) {
                if ($_GPC['position']) {
                    foreach ($_GPC['position'] as $id=>$val) {
                        M::t('superman_mall_item')->update(array('position' => $val), array('id' => $id));
                    }
                    message('操作成功！', referer(), 'success');
                }
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            //商品数据
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'special' => 2,
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
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
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $item = array();
            if ($id > 0) {  //编辑
                $item = M::t('superman_mall_item')->fetch($id);
                if (!$item) {
                    message('商品不存在或已删除！', referer(), 'error');
                }
                $itemurl = $_W['siteroot'].'app/'.$this->createMobileUrl('detail', array('itemid' => $id));
                $item['album'] = !empty($item['album'])?iunserializer($item['album']):array();
                $item['user'] = user_single($item['userid']);
                $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                //自定义属性加载
                $filter = array(
                    'itemid' => $id,
                );
                $orderby = 'ORDER BY displayorder DESC';
                $item['params'] =  M::t('superman_mall_item_params')->fetchall($filter, $orderby, 0, -1);
                //邮费模板
                if ($item['postage_tmplid'] > 0) {
                    $item['postage_select'] = 2;
                } else {
                    $item['postage_select'] = 1;
                }
            } else if (intval($_GPC['copyid']) > 0) {   //复制商品
                $this->check_web_shop();
                $copyid = intval($_GPC['copyid']);
                $item = M::t('superman_mall_item')->fetch($copyid);
                if (!$item) {
                    message('复制的商品不存在或已删除！', referer(), 'error');
                }
                $item['album'] = !empty($item['album'])?iunserializer($item['album']):array();
                //自定义属性复制
                $filter = array(
                    'itemid' => $copyid,
                );
                $orderby = 'ORDER BY displayorder DESC';
                $item['params'] =  M::t('superman_mall_item_params')->fetchall($filter, $orderby, 0, -1);
                $item['status'] = 1;
            } else {
                $this->check_web_shop();
                $item['type'] = 1;
                $item['status'] = 1;
                $item['minus_total'] = 1;
                $item['postage_select'] = 1;
            }
            //邮费模版
            $filter = array(
                'shopid' => $item['shopid'],
            );
            $postage_tmpl = M::t('superman_mall_postage_template')->fetchall($filter, '', 0, -1);
            if (checksubmit()) {
                $extend = $_GPC['extend'];
                $extend['single_price'] = $extend['single_price']?SupermanUtil::float_format($extend['single_price']):'0.00';
                $other_attr = $_GPC['other_attr'];
                $extend['other_attr'] = array(
                    'order_buy_num' => $other_attr['order_buy_num']?$other_attr['order_buy_num']:'',
                    'max_buy_num' => $other_attr['max_buy_num']?$other_attr['max_buy_num']:'',
                );
                $extend = iserializer($extend);
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
                    'status' => $_GPC['status'],
                    'summary' => $_GPC['summary'],
                    'minus_total' => in_array($_GPC['minus_total'], array(1, 2))?$_GPC['minus_total']:1,
                    'description' => $_GPC['description'],
                    'total' => intval($_GPC['total']),
                    'price' => $_GPC['price'],
                    'displayorder' => $_GPC['displayorder'],
                    'postage' => $_GPC['postage'],
                    'market_price' => $_GPC['market_price'],
                    'cost_price' => $_GPC['cost_price'],
                    'special' => 2,
                    'extend' => $extend,
                    'weight' => $_GPC['weight'],
                    'isreceipt' => intval($_GPC['isreceipt'])?1:0,
                    'isrepair' => intval($_GPC['isrepair'])?1:0,
                    'ischeckout' => intval($_GPC['ischeckout'])?1:0,
                );
                if ($_GPC['postage_select'] == 2 && $_GPC['postage_tmplid'] > 0) {
                    $_data['postage_tmplid'] = intval($_GPC['postage_tmplid']); //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                } else {
                    $_data['postage_tmplid'] = 0; //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                }
                if ($id) {  //编辑
                    $_data['updatetime'] = TIMESTAMP;
                    M::t('superman_mall_item')->update($_data, array('id' => $id));
                } else {    //新增
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['shopid'] = $this->shop['id'];
                    $_data['createtime'] = TIMESTAMP;
                    $id = M::t('superman_mall_item')->insert($_data);
                    if (!$id) {
                        message('数据库错误(insert superman_mall_item failed)', '', 'error');
                    }
                    if (isset($copyid) && $item['id'] == $copyid) { //复制
                        $path = SupermanUtil::attachment_path();
                        if ($_data['cover'] != '' && $_data['cover'] == $item['cover']) {   //封面复制
                            $source = $_data['cover'];
                            $pos = strrpos($source, '.');
                            $destination = substr($source, 0, $pos) . "-{$id}-" . TIMESTAMP . substr($source, $pos);
                            $copy_result = @copy($path . $source, $path . $destination);
                            if ($copy_result === true) {
                                M::t('superman_mall_item')->update(array('cover' => $destination), array('id' => $id));
                            }
                        }
                        if (!empty($_data['album']) && !empty($item['album']) && is_array($item['album'])) {     //相册复制
                            $_album = $_GPC['album'];
                            foreach ($_GPC['album'] as $pic) {
                                if (in_array($pic, $item['album'])) {
                                    $source = $pic;
                                    $pos = strrpos($source, '.');
                                    $destination = substr($source, 0, $pos) . "-{$id}-" . TIMESTAMP . substr($source, $pos);
                                    $copy_result = @copy($path . $source, $path . $destination);
                                    if ($copy_result === true) {
                                        $_album = str_replace($pic, $destination, $_album);
                                    }
                                }
                            }
                            $_album = iserializer($_album);
                            if ($_album != $_data['album']) {
                                M::t('superman_mall_item')->update(array('album' => $_album), array('id' => $id));
                            }
                        }
                    }
                }
                //更新自定义属性
                if (isset($_GPC['param_id']) && $_GPC['param_id']) {
                    $count = count($_GPC['param_id']);
                    foreach ($_GPC['param_id'] as $key=>$value) {
                        if ($_GPC['param_name'][$key] == '' && $_GPC['param_value'][$key] == '') {
                            continue;
                        }
                        $_data = array(
                            'itemid' => $id,
                            'name' => $_GPC['param_name'][$key],
                            'value' => $_GPC['param_value'][$key],
                            'displayorder' => $count - $key,
                        );
                        if (empty($_GPC['param_id'][$key])) {   //insert
                            M::t('superman_mall_item_params')->insert($_data);
                        } else {                                //edit
                            M::t('superman_mall_item_params')->update($_data, array('id' => $_GPC['param_id'][$key]));
                        }
                    }
                }
                message('操作成功！', $this->createWebUrl('mgroupon'), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            M::t('superman_mall_item')->delete(array('id' => $id));
            //更新商品属性(产品参数)
            M::t('superman_mall_item_params')->delete(array('itemid' => $id));
            message('操作成功！', $this->createWebUrl('mgroupon'), 'success');
        } else if ($act == 'setting') {
            $this->check_web_shop();
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, $this->shop['id']);
            if (checksubmit()) {
                $_data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MGROUPON_SETTING, $this->shop['id']);
                if ($setting) {
                    M::t('superman_mall_kv')->update($_data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($_data);
                }
                message('操作成功！', referer(), 'success');
            }
        }
        include $this->template('mgroupon');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $plugin_permission = $this->check_plugin_permission('mgroupon', $this->shop['id']);
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'setting')) ? $_GPC['act'] : 'display';
        $nav['title'] = '功能模块';
        //一级分类加载
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'pid' => 0
        );
        $pcids = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
        if ($act == 'display') {
            $nav['subtitle'] = '拼团';
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
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            //商品数据
            $filter = array(
                'shopid' => $this->shop['id'],
                'special' => 2,
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
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
            $orderby = 'ORDER BY displayorder DESC';
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
            if ($id > 0) {  //编辑
                $item = M::t('superman_mall_item')->fetch($id);
                if (!$item) {
                    message('商品不存在或已删除！', referer(), 'error');
                }
                $itemurl = $_W['siteroot'].'app/'.$this->createMobileUrl('detail', array('itemid' => $id));
                $item['album'] = !empty($item['album'])?iunserializer($item['album']):array();
                $item['user'] = user_single($item['userid']);
                $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                //自定义属性加载
                $filter = array(
                    'itemid' => $id,
                );
                $orderby = 'ORDER BY displayorder DESC';
                $item['params'] =  M::t('superman_mall_item_params')->fetchall($filter, $orderby, 0, -1);
                //邮费模板
                if ($item['postage_tmplid'] > 0) {
                    $item['postage_select'] = 2;
                } else {
                    $item['postage_select'] = 1;
                }
            } else if (intval($_GPC['copyid']) > 0) {   //复制商品
                $copyid = intval($_GPC['copyid']);
                $item = M::t('superman_mall_item')->fetch($copyid);
                if (!$item) {
                    message('复制的商品不存在或已删除！', referer(), 'error');
                }
                $item['album'] = !empty($item['album'])?iunserializer($item['album']):array();
                //自定义属性复制
                $filter = array(
                    'itemid' => $copyid,
                );
                $orderby = 'ORDER BY displayorder DESC';
                $item['params'] =  M::t('superman_mall_item_params')->fetchall($filter, $orderby, 0, -1);
                $item['status'] = 1;
            } else {
                $item['type'] = 1;
                $item['status'] = 1;
                $item['minus_total'] = 1;
                $item['postage_select'] = 1;
            }
            //邮费模版
            $filter = array(
                'shopid' => $item['shopid'],
            );
            $postage_tmpl = M::t('superman_mall_postage_template')->fetchall($filter, '', 0, -1);
            if (checksubmit()) {
                $extend = $_GPC['extend'];
                $extend['single_price'] = $extend['single_price']?SupermanUtil::float_format($extend['single_price']):'0.00';
                $other_attr = $_GPC['other_attr'];
                $extend['other_attr'] = array(
                    'order_buy_num' => $other_attr['order_buy_num']?$other_attr['order_buy_num']:'',
                    'max_buy_num' => $other_attr['max_buy_num']?$other_attr['max_buy_num']:'',
                );
                $extend = iserializer($extend);
                $_data = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
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
                    'status' => $_GPC['status'],
                    'summary' => $_GPC['summary'],
                    'minus_total' => in_array($_GPC['minus_total'], array(1, 2))?$_GPC['minus_total']:1,
                    'description' => $_GPC['description'],
                    'total' => intval($_GPC['total']),
                    'price' => $_GPC['price'],
                    'displayorder' => $_GPC['displayorder'],
                    'postage' => $_GPC['postage'],
                    'market_price' => $_GPC['market_price'],
                    'cost_price' => $_GPC['cost_price'],
                    'special' => 2,
                    'extend' => $extend,
                    'weight' => $_GPC['weight'],
                    'isreceipt' => intval($_GPC['isreceipt'])?1:0,
                    'isrepair' => intval($_GPC['isrepair'])?1:0,
                    'ischeckout' => intval($_GPC['ischeckout'])?1:0,
                );
                if ($_GPC['postage_select'] == 2 && $_GPC['postage_tmplid'] > 0) {
                    $_data['postage_tmplid'] = intval($_GPC['postage_tmplid']); //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                } else {
                    $_data['postage_tmplid'] = 0; //邮费模版
                    $_data['postage'] = $_GPC['postage'];   //统一邮费
                }
                if ($id) {  //编辑
                    $_data['updatetime'] = TIMESTAMP;
                    M::t('superman_mall_item')->update($_data, array('id' => $id));
                } else {    //新增
                    $_data['createtime'] = TIMESTAMP;
                    $id = M::t('superman_mall_item')->insert($_data);
                    if (!$id) {
                        message('数据库错误(insert superman_mall_item failed)', '', 'error');
                    }
                    if (isset($copyid) && $item['id'] == $copyid) { //复制
                        $path = SupermanUtil::attachment_path();
                        if ($_data['cover'] != '' && $_data['cover'] == $item['cover']) {   //封面复制
                            $source = $_data['cover'];
                            $pos = strrpos($source, '.');
                            $destination = substr($source, 0, $pos) . "-{$id}-" . TIMESTAMP . substr($source, $pos);
                            $copy_result = @copy($path . $source, $path . $destination);
                            if ($copy_result === true) {
                                M::t('superman_mall_item')->update(array('cover' => $destination), array('id' => $id));
                            }
                        }
                        if (!empty($_data['album']) && !empty($item['album']) && is_array($item['album'])) {     //相册复制
                            $_album = $_GPC['album'];
                            foreach ($_GPC['album'] as $pic) {
                                if (in_array($pic, $item['album'])) {
                                    $source = $pic;
                                    $pos = strrpos($source, '.');
                                    $destination = substr($source, 0, $pos) . "-{$id}-" . TIMESTAMP . substr($source, $pos);
                                    $copy_result = @copy($path . $source, $path . $destination);
                                    if ($copy_result === true) {
                                        $_album = str_replace($pic, $destination,$_album);
                                    }
                                }
                            }
                            $_album = iserializer($_album);
                            if ($_album != $_data['album']) {
                                M::t('superman_mall_item')->update(array('album' => $_album), array('id' => $id));
                            }
                        }
                    }
                }
                //更新自定义属性
                if (isset($_GPC['param_id']) && $_GPC['param_id']) {
                    $count = count($_GPC['param_id']);
                    foreach ($_GPC['param_id'] as $key=>$value) {
                        if ($_GPC['param_name'][$key] == '' && $_GPC['param_value'][$key] == '') {
                            continue;
                        }
                        $_data = array(
                            'itemid' => $id,
                            'name' => $_GPC['param_name'][$key],
                            'value' => $_GPC['param_value'][$key],
                            'displayorder' => $count - $key,
                        );
                        if (empty($_GPC['param_id'][$key])) {   //insert
                            M::t('superman_mall_item_params')->insert($_data);
                        } else {                                //edit
                            M::t('superman_mall_item_params')->update($_data, array('id' => $_GPC['param_id'][$key]));
                        }
                    }
                }
                message('操作成功！', $this->createWebUrl('mgroupon'), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            M::t('superman_mall_item')->delete(array('id' => $id));
            //更新商品属性(产品参数)
            M::t('superman_mall_item_params')->delete(array('itemid' => $id));
            message('操作成功！', $this->createWebUrl('mgroupon'), 'success');
        } else if ($act == 'setting') {
            $nav['subtitle'] = '参数设置';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, $this->shop['id']);
            if (checksubmit()) {
                $_data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MGROUPON_SETTING, $this->shop['id']);
                if ($setting) {
                    M::t('superman_mall_kv')->update($_data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $_data['uniacid'] = $_W['uniacid'];
                    $_data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($_data);
                }
                message('操作成功！', referer(), 'success');
            }
        }
        include $this->template('mgroupon/index');
    }
}

$obj = new Superman_mall_doWebMgroupon;