<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
define('IN_SUPERMAN_MALL_PLATFORM', true);
class Superman_mall_doWebCategory extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_platform');
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'delete'))?$_GPC['act']:'display';
        $nav['title'] = '商品分类';
        if ($act == 'display') {
            $nav['subtitle'] = '分类列表';
            //分类列表展示
            $filter = array(
                'uniacid' => $_W['uniacid']
            );
            $orderby = 'ORDER BY displayorder DESC';
            $list = M::t('superman_mall_item_category')->fetchall_recurse($filter, $orderby, 0, -1, '', true);
            //更新排序/商品名
            if (checksubmit('submit')) {
                $update_field = array('displayorder');
                foreach ($update_field as $field) {
                    if ($_GPC[$field]) {
                        foreach ($_GPC[$field] as $id=>$val) {
                            M::t('superman_mall_item_category')->update(array($field => $val), array('id' => $id));
                        }
                    }
                }
                M::t('superman_mall_item_category')->clear_cache($_W['uniacid']);
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'post') {
            $id = intval($_GPC['id']);
            $pid = intval($_GPC['pid']);
            if ($pid > 0) { //非一级分类
                $parent = M::t('superman_mall_item_category')->fetch($pid); //查询是否三级分类
            }
            //加载分类数据
            if ($id > 0) {
                $nav['subtitle'] = '编辑分类';
                if ($_GPC['level'] == 1) {
                    $itemurl = $_W['siteroot'].'app/'.$this->createMobileUrl('list', array('pcid' => $id));
                } else if ($_GPC['level'] == 2) {
                    $itemurl = $_W['siteroot'].'app/'.$this->createMobileUrl('list', array('cid' => $id));
                } else if ($_GPC['level'] == 3) {
                    $itemurl = $_W['siteroot'].'app/'.$this->createMobileUrl('list', array('ccid' => $id));
                }
                $item = M::t('superman_mall_item_category')->fetch($id);
            } else {
                //添加分类初始化
                $nav['subtitle'] = '添加分类';
                $item['isshow'] = 1;
            }

            //添加/编辑分类
            if (checksubmit('submit')) {
                $_data = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $_GPC['title'],
                    'cover' => $_GPC['cover'],
                    'isshow' => $_GPC['isshow'],
                    'pid' => $_GPC['pid']
                );
                if ($_GPC['id'] > 0) {  //编辑
                    M::t('superman_mall_item_category')->update($_data, array('id' => $_GPC['id']));
                } else {                //添加
                    M::t('superman_mall_item_category')->insert($_data);
                }
                M::t('superman_mall_item_category')->clear_cache($_W['uniacid']);
                message('更新成功！', $this->createWebUrl('category', array('act' => 'display')), 'success');
            }
        } else if ($act == 'delete') {
            $id = intval($_GPC['id']);
            $this->recurse_delete($id);
            M::t('superman_mall_item_category')->clear_cache($_W['uniacid']);
            message('删除成功！', referer(), 'success');
        }
		include $this->template('category');
    }

    private function recurse_delete($id, $delete_cover = false) {
        $list = M::t('superman_mall_item_category')->fetchall(array(
            'pid' => $id,
        ));
        if ($list) {
            foreach ($list as $row) {
                $this->recurse_delete($row['id']);
            }
        }
        $row = M::t('superman_mall_item_category')->fetch($id);
        if ($row) {
            if ($delete_cover) {
                @unlink(SupermanUtil::attachment_path().$row['cover']);
            }
            M::t('superman_mall_item_category')->delete(array('id' => $id));

            //更新商品分类
            M::t('superman_mall_item')->update(array('pcid' => 0), array('pcid' => $id));
            M::t('superman_mall_item')->update(array('cid' => 0), array('cid' => $id));
            M::t('superman_mall_item')->update(array('ccid' => 0), array('ccid' => $id));
        }
        return;
    }
}

$obj = new Superman_mall_doWebCategory;
