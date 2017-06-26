<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebPostage extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_item_postage');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'copy', 'area'))?$_GPC['act']:'display';
        $nav['title'] = '邮费模板';
        if ($act == 'display') {
            $nav['subtitle'] = '模板列表';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            $total = M::t('superman_mall_postage_template')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_postage_template')->fetchall($filter, '', $start, $pagesize);
                foreach ($list as &$item) {
                    $filter = array(
                        'templateid' => $item['id'],
                    );
                    $item['items'] = M::t('superman_mall_postage_template_value')->fetchall($filter);
                }
                unset($item);
                $pager = pagination($total, $pindex, $pagesize);
            }
            //print_r($list);die;
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $item = array();
            if ($id > 0) {
                $item = M::t('superman_mall_postage_template')->fetch($id);
                if ($item) {
                    $filter = array(
                        'templateid' => $item['id'],
                    );
                    $item['items'] = M::t('superman_mall_postage_template_value')->fetchall($filter);
                    //其他地区单独取出
                    if ($item['items']) {
                        foreach ($item['items'] as $k => $v) {
                            if ($v['area'] == '其他地区') {
                                $default = $v;
                                unset($item['items'][$k]);
                                break;
                            }
                        }
                    }
                }
            } else {
                $this->check_web_shop();
            }
            if (!$item) {
                //添加时初始化参数
                $item['valuation'] = 1;
            }
            if (!isset($default)) {
                $default = array(
                    'area' => '其他地区',
                    'start' => 1,
                    'postage' => 6,
                    'step' => 1,
                    'renew' => 2,
                );
            }
            if (checksubmit()) {
                $data = array(
                    'title' => $_GPC['title'],
                    'valuation' => $_GPC['valuation'],
                );
                //查重
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'title' => $_GPC['title'],
                );
                $repeat = M::t('superman_mall_postage_template')->fetch($filter);
                if ($repeat && ($id <= 0 || $id > 0 && $id != $repeat['id'])) {
                    message('该模板名称已存在', referer(), 'error');
                }
                if ($id) {
                    $data['updatetime'] = TIMESTAMP;
                    M::t('superman_mall_postage_template')->update($data, array('id' => $id));
                    $templateid = $id;
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['shopid'] = $this->shop['id'];
                    $data['createtime'] = $data['updatetime'] = TIMESTAMP;
                    $templateid = M::t('superman_mall_postage_template')->insert($data);
                    if (!$templateid) {
                        message('数据库操作失败！(insert superman_mall_postage_template failed)', '', 'error');
                    }
                }

                $new_default = $_GPC['default'];
                if ($default['id'] > 0) {
                    M::t('superman_mall_postage_template_value')->update($new_default, array('id' => $default['id']));
                } else {
                    $new_default['area'] = '其他地区';
                    $new_default['templateid'] = $templateid;
                    M::t('superman_mall_postage_template_value')->insert($new_default);
                }

                //更新
                if (isset($_GPC['area']) && $_GPC['area']) {
                    foreach ($_GPC['area'] as $key=>$value) {
                        $data = array(
                            'area' => $_GPC['area'][$key],
                            'start' => $_GPC['start'][$key],
                            'postage' => $_GPC['postage'][$key],
                            'step' => $_GPC['step'][$key],
                            'renew' => $_GPC['renew'][$key],
                        );
                        M::t('superman_mall_postage_template_value')->update($data, array('id' => $key));
                    }
                }
                //新增
                if (isset($_GPC['new_area']) && $_GPC['new_area']) {
                    foreach ($_GPC['new_area'] as $key => $value) {
                        $data = array(
                            'templateid' => $templateid,
                            'area' => $_GPC['new_area'][$key],
                            'start' => $_GPC['new_start'][$key],
                            'postage' => $_GPC['new_postage'][$key],
                            'step' => $_GPC['new_step'][$key],
                            'renew' => $_GPC['new_renew'][$key],
                        );
                        M::t('superman_mall_postage_template_value')->insert($data);
                    }
                }
                message('操作成功！', $this->createWebUrl('postage'), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            if ($id) {
                $type = $_GPC['type'];
                if ($type == 'template') {
                    M::t('superman_mall_postage_template_value')->delete(array('templateid' => $id));
                    M::t('superman_mall_postage_template')->delete(array('id' => $id));
                } else if ($type == 'template_value') {
                    M::t('superman_mall_postage_template_value')->delete(array('id' => $id));
                } else if ($type == 'clear_template_value') {
                    M::t('superman_mall_postage_template_value')->delete(array('templateid' => $id));
                }
                if ($_W['isajax']) {
                    exit('success');
                } else {
                    message('操作成功！', referer(), 'success');
                }
            }
            if ($_W['isajax']) {
                exit('非法请求');
            } else {
                message('非法请求！', referer(), 'error');
            }
        } else if ($act == 'copy') {
            $id = intval($_GPC['id']);
            if ($id) {
                $item = M::t('superman_mall_postage_template')->fetch($id);
                if ($item) {
                    $filter = array(
                        'templateid' => $item['id'],
                    );
                    $values = M::t('superman_mall_postage_template_value')->fetchall($filter);
                    //复制template表数据
                    $item['title'] .= '的副本(1)';
                    $item['createtime'] = $item['updatetime'] = TIMESTAMP;
                    unset($item['id']);
                    $templateid = M::t('superman_mall_postage_template')->insert($item);
                    if (!$templateid) {
                        message('数据库操作失败！(insert superman_mall_postage_template failed)', '', 'error');
                    }
                    //复制template_value表数据
                    if ($values) {
                        foreach ($values as $val) {
                            unset($val['id']);
                            $val['templateid'] = $templateid;
                            M::t('superman_mall_postage_template_value')->insert($val);
                        }
                    }
                    message('操作成功！', referer(), 'success');
                }
            }
            message('非法请求！', referer(), 'error');
        } else if ($act == 'area') {
            include $this->template('postage-area');
            exit;
        }
		include $this->template('postage');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'copy', 'area'))?$_GPC['act']:'display';
        $nav['title'] = '邮费模板';
        if ($act == 'display') {
            $nav['subtitle'] = '模板列表';
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $this->shop['id'],
            );
            $total = M::t('superman_mall_postage_template')->count($filter);
            if ($total) {
                $list = M::t('superman_mall_postage_template')->fetchall($filter, '', $start, $pagesize);
                foreach ($list as &$item) {
                    $filter = array(
                        'templateid' => $item['id'],
                    );
                    $item['items'] = M::t('superman_mall_postage_template_value')->fetchall($filter);
                }
                unset($item);
                $pager = pagination($total, $pindex, $pagesize);
            }
            //print_r($list);die;
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $item = array();
            if ($id) {
                $item = M::t('superman_mall_postage_template')->fetch($id);
                if ($item) {
                    $filter = array(
                        'templateid' => $item['id'],
                    );
                    $item['items'] = M::t('superman_mall_postage_template_value')->fetchall($filter);
                    //其他地区单独取出
                    if ($item['items']) {
                        foreach ($item['items'] as $k => $v) {
                            if ($v['area'] == '其他地区') {
                                $default = $v;
                                unset($item['items'][$k]);
                                break;
                            }
                        }
                    }
                }
            }
            if (!$item) {
                //添加时初始化参数
                $item['valuation'] = 1;
            }
            if (!isset($default)) {
                $default = array(
                    'area' => '其他地区',
                    'start' => 1,
                    'postage' => 6,
                    'step' => 1,
                    'renew' => 2,
                );
            }
            if (checksubmit()) {
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'title' => $_GPC['title'],
                    'valuation' => $_GPC['valuation'],
                );
                //查重
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'title' => $_GPC['title'],
                );
                $repeat = M::t('superman_mall_postage_template')->fetch($filter);
                if ($repeat && ($id <= 0 || $id > 0 && $id != $repeat['id'])) {
                    message('该模板名称已存在', referer(), 'error');
                }

                if ($id) {
                    $data['updatetime'] = TIMESTAMP;
                    M::t('superman_mall_postage_template')->update($data, array('id' => $id));
                    $templateid = $id;
                } else {
                    $data['createtime'] = $data['updatetime'] = TIMESTAMP;
                    $templateid = M::t('superman_mall_postage_template')->insert($data);
                    if (!$templateid) {
                        message('数据库操作失败！(insert superman_mall_postage_template failed)', '', 'error');
                    }
                }

                $new_default = $_GPC['default'];
                if ($default['id'] > 0) {
                    M::t('superman_mall_postage_template_value')->update($new_default, array('id' => $default['id']));
                } else {
                    $new_default['area'] = '其他地区';
                    $new_default['templateid'] = $templateid;
                    M::t('superman_mall_postage_template_value')->insert($new_default);
                }

                //更新
                if (isset($_GPC['area']) && $_GPC['area']) {
                    foreach ($_GPC['area'] as $key=>$value) {
                        $data = array(
                            'area' => $_GPC['area'][$key],
                            'start' => $_GPC['start'][$key],
                            'postage' => $_GPC['postage'][$key],
                            'step' => $_GPC['step'][$key],
                            'renew' => $_GPC['renew'][$key],
                        );
                        M::t('superman_mall_postage_template_value')->update($data, array('id' => $key));
                    }
                }
                //新增
                if (isset($_GPC['new_area']) && $_GPC['new_area']) {
                    foreach ($_GPC['new_area'] as $key => $value) {
                        $data = array(
                            'templateid' => $templateid,
                            'area' => $_GPC['new_area'][$key],
                            'start' => $_GPC['new_start'][$key],
                            'postage' => $_GPC['new_postage'][$key],
                            'step' => $_GPC['new_step'][$key],
                            'renew' => $_GPC['new_renew'][$key],
                        );
                        M::t('superman_mall_postage_template_value')->insert($data);
                    }
                }
                message('操作成功！', $this->createWebUrl('postage'), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            if ($id) {
                $type = $_GPC['type'];
                if ($type == 'template') {
                    M::t('superman_mall_postage_template_value')->delete(array('templateid' => $id));
                    M::t('superman_mall_postage_template')->delete(array('id' => $id));
                } else if ($type == 'template_value') {
                    M::t('superman_mall_postage_template_value')->delete(array('id' => $id));
                } else if ($type == 'clear_template_value') {
                    M::t('superman_mall_postage_template_value')->delete(array('templateid' => $id));
                }
                if ($_W['isajax']) {
                    exit('success');
                } else {
                    message('操作成功！', referer(), 'success');
                }
            }
            if ($_W['isajax']) {
                exit('非法请求');
            } else {
                message('非法请求！', referer(), 'error');
            }
        } else if ($act == 'copy') {
            $id = intval($_GPC['id']);
            if ($id) {
                $item = M::t('superman_mall_postage_template')->fetch($id);
                if ($item) {
                    $filter = array(
                        'templateid' => $item['id'],
                    );
                    $values = M::t('superman_mall_postage_template_value')->fetchall($filter);
                    //复制template表数据
                    $item['title'] .= '的副本(1)';
                    $item['createtime'] = $item['updatetime'] = TIMESTAMP;
                    unset($item['id']);
                    $templateid = M::t('superman_mall_postage_template')->insert($item);
                    if (!$templateid) {
                        message('数据库操作失败！(insert superman_mall_postage_template failed)', '', 'error');
                    }
                    //复制template_value表数据
                    if ($values) {
                        foreach ($values as $val) {
                            unset($val['id']);
                            $val['templateid'] = $templateid;
                            M::t('superman_mall_postage_template_value')->insert($val);
                        }
                    }
                    message('操作成功！', referer(), 'success');
                }
            }
            message('非法请求！', referer(), 'error');
        } else if ($act == 'area') {
            include $this->template('postage/area');
            exit;
        }
        include $this->template('postage/index');
    }
}

$obj = new Superman_mall_doWebPostage;