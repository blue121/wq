<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebSpec extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_item_spec');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'value', 'load'))?$_GPC['act']:'display';
        $nav['title'] = '商品规格';
        if ($act == 'display') {
            $nav['subtitle'] = '规格列表';
            /*//更新排序
            if(checksubmit('submit')) {
                $displayorder = $_GPC['displayorder'];
                if ($displayorder) {
                    foreach ($displayorder as $id=>$val) {
                        M::t('superman_mall_item_attr')->update(array('displayorder' => $val), array('id' => $id));
                    }
                    message('操作成功！', referer(), 'success');
                }
            }*/
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            //商品数据
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            $orderby = 'ORDER BY displayorder DESC, id DESC';
            $total = M::t('superman_mall_item_attr')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_item_attr')->fetchall($filter, $orderby, $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $filter = array(
                            'attrid' => $li['id']
                        );
                        $values = M::t('superman_mall_item_value')->fetchall($filter, $orderby, 0, -1);
                        if ($values) {
                            foreach ($values as $value) {
                                $li['value'] .= $value['value'].',';
                            }
                            $li['value'] = rtrim($li['value'], ',');
                        } else {
                            $li['value'] = '';
                        }
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            if ($id > 0) {
                $item = M::t('superman_mall_item_attr')->fetch($id);
                if (!$item) {   //找不到规格名
                    $id = 0;
                } else {
                    $filter = array(
                        'attrid' => $id
                    );
                    $orderby = 'ORDER BY displayorder DESC, id DESC';
                    $item['values'] = M::t('superman_mall_item_value')->fetchall($filter, $orderby, 0, -1);
                }
            } else {
                $this->check_web_shop();
            }
            if (checksubmit('submit')) {
                $title = trim($_GPC['title']);
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $title,
                    'displayorder' => $_GPC['displayorder'],
                );
                if ($id > 0) {  //编辑
                    M::t('superman_mall_item_attr')->update($data, array('id' => $id));
                } else {        //添加
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $this->shop['id'],
                        'title' => $title,
                    );
                    $row = M::t('superman_mall_item_attr')->fetch($filter);
                    if ($row) {
                        message('属性名称重复，请重新填写！', referer(), 'error');
                    }
                    $data['shopid'] = $this->shop['id'];
                    $id = M::t('superman_mall_item_attr')->insert($data);
                    if (!$id) {
                        message('数据库错误(insert superman_mall_item failed)', '', 'error');
                    }
                }
                //更新自定义属性
                if (isset($_GPC['value_id']) && $_GPC['value_id']) {
                    $count = count($_GPC['value_id']);
                    foreach ($_GPC['value_id'] as $key=>$value) {
                        if ($_GPC['value'][$key] == '') {
                            continue;
                        }
                        $data = array(
                            'attrid' => $id,
                            'value' => $_GPC['value'][$key],
                            'displayorder' => $count - $key,
                        );
                        if (empty($_GPC['value_id'][$key])) {   //insert
                            M::t('superman_mall_item_value')->insert($data);
                        } else {                                //edit
                            M::t('superman_mall_item_value')->update($data, array('id' => $_GPC['value_id'][$key]));
                        }
                    }
                }
                message('更新成功！', $this->createWebUrl('spec', array('act' => 'display')), 'success');
            }
        } else if ($act == 'delete') {
            $attrid = intval($_GPC['attrid']);
            if ($attrid > 0) {
                M::t('superman_mall_item_attr')->delete(array('id' => $attrid));
                M::t('superman_mall_item_value')->delete(array('attrid' => $attrid));
                message('删除成功！', referer(), 'success');
            }
            $valueid = intval($_GPC['valueid']);
            if ($valueid > 0) {
                M::t('superman_mall_item_value')->delete(array('id' => $valueid));
                echo 'success';
                exit;
            } else {
                echo '非法请求';
                exit;
            }
        } else if ($act == 'value') {
            include $this->template('spec-value');
            exit;
        } else if ($act == 'load') {
            if (isset($_GPC['attrid']) && $_GPC['attrid']) {
                $attrid = intval($_GPC['attrid']);
                $filter = array(
                    'attrid' => $attrid,
                );
                $list = M::t('superman_mall_item_value')->fetchall($filter, 'ORDER BY displayorder DESC, id DESC', 0, -1);
            } else {
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $list = M::t('superman_mall_item_attr')->fetchall($filter, 'ORDER BY id DESC', 0, -1);
            }
            $this->json(ERRNO::OK, '', $list);
        }
		include $this->template('spec');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'value', 'load'))?$_GPC['act']:'display';
        $nav['title'] = '商品规格';
        if ($act == 'display') {
            $nav['subtitle'] = '规格列表';
            //更新排序
            if(checksubmit('submit')) {
                $displayorder = $_GPC['displayorder'];
                if ($displayorder) {
                    foreach ($displayorder as $id=>$val) {
                        M::t('superman_mall_item_attr')->update(array('displayorder' => $val), array('id' => $id));
                    }
                    message('操作成功！', referer(), 'success');
                }
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $this->shop['id'],
            );
            $orderby = 'ORDER BY displayorder DESC, id DESC';
            $total = M::t('superman_mall_item_attr')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_item_attr')->fetchall($filter, $orderby, $start, $pagesize);
                if ($list) {
                    foreach ($list as &$li) {
                        $filter = array(
                            'attrid' => $li['id']
                        );
                        $values = M::t('superman_mall_item_value')->fetchall($filter, $orderby, 0, -1);
                        if ($values) {
                            foreach ($values as $value) {
                                $li['value'] .= $value['value'].',';
                            }
                            $li['value'] = rtrim($li['value'], ',');
                        } else {
                            $li['value'] = '';
                        }
                    }
                    unset($li);
                }
                $pager = pagination($total, $pindex, $pagesize);
            }
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            if ($id > 0) {
                $item = M::t('superman_mall_item_attr')->fetch($id);
                if (!$item) {   //找不到规格名
                    $id = 0;
                } else {
                    $filter = array(
                        'attrid' => $id
                    );
                    $orderby = 'ORDER BY displayorder DESC, id DESC';
                    $item['values'] = M::t('superman_mall_item_value')->fetchall($filter, $orderby, 0, -1);
                }
            }
            if (checksubmit('submit')) {
                $title = trim($_GPC['title']);
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $title,
                    'displayorder' => $_GPC['displayorder'],
                );
                if ($id > 0) {  //编辑
                    M::t('superman_mall_item_attr')->update($data, array('id' => $id));
                } else {        //添加
                    $filter = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $this->shop['id'],
                        'title' => $title,
                    );
                    $row = M::t('superman_mall_item_attr')->fetch($filter);
                    if ($row) {
                        message('属性名称重复，请重新填写！', referer(), 'error');
                    }
                    $data['shopid'] = $this->shop['id'];
                    $id = M::t('superman_mall_item_attr')->insert($data);
                    if (!$id) {
                        message('数据库错误(insert superman_mall_item failed)', '', 'error');
                    }
                }
                //更新自定义属性
                if (isset($_GPC['value_id']) && $_GPC['value_id']) {
                    $count = count($_GPC['value_id']);
                    foreach ($_GPC['value_id'] as $key=>$value) {
                        if ($_GPC['value'][$key] == '') {
                            continue;
                        }
                        $data = array(
                            'attrid' => $id,
                            'value' => $_GPC['value'][$key],
                            'displayorder' => $count - $key,
                        );
                        if (empty($_GPC['value_id'][$key])) {   //insert
                            M::t('superman_mall_item_value')->insert($data);
                        } else {                                //edit
                            M::t('superman_mall_item_value')->update($data, array('id' => $_GPC['value_id'][$key]));
                        }
                    }
                }
                message('更新成功！', $this->createWebUrl('spec', array('act' => 'display')), 'success');
            }
        } else if ($act == 'delete') {
            $attrid = intval($_GPC['attrid']);
            if ($attrid > 0) {
                M::t('superman_mall_item_attr')->delete(array('id' => $attrid));
                M::t('superman_mall_item_value')->delete(array('attrid' => $attrid));
                message('删除成功！', referer(), 'success');
            }
            $valueid = intval($_GPC['valueid']);
            if ($valueid > 0) {
                M::t('superman_mall_item_value')->delete(array('id' => $valueid));
                echo 'success';
                exit;
            } else {
                echo '非法请求';
                exit;
            }

        } else if ($act == 'value') {
            include $this->template('spec/value');
            exit;
        } else if ($act == 'load') {
            if (isset($_GPC['attrid']) && $_GPC['attrid']) {
                $attrid = intval($_GPC['attrid']);
                $filter = array(
                    'attrid' => $attrid,
                );
                $list = M::t('superman_mall_item_value')->fetchall($filter, 'ORDER BY displayorder DESC, id DESC', 0, -1);
            } else {
                $filter = array(
                    'shopid' => $this->shop['id'],
                );
                $list = M::t('superman_mall_item_attr')->fetchall($filter, 'ORDER BY displayorder DESC, id DESC', 0, -1);
            }
            $this->json(ERRNO::OK, '', $list);
        }
        //include $this->template('spec');
        include $this->template('spec/index');
    }
}
$obj = new Superman_mall_doWebSpec();