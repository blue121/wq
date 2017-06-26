<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebComment extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_comment');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post', 'batch'))?$_GPC['act']:'display';
        $nav['title'] = '评价管理';
        if ($act == 'display') {
            if ($_GPC['status'] == -1 || empty($_GPC['status'])) {
                $nav['subtitle'] = '全部评价';
            } else if ($_GPC['status'] == 0) {
                $nav['subtitle'] = '未审核评价';
            } else if ($_GPC['status'] == 1) {
                $nav['subtitle'] = '已审核评价';
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            if ($this->shop) {
                $filter['shopid'] = $this->shop['id'];
            }
            if (isset($_GPC['status']) && $_GPC['status'] == 0) {       //未审核
                $filter['status'] = 0;
            } else if (isset($_GPC['status']) && $_GPC['status'] == 1) {//已审核评价
                $filter['status'] = '#>0';
            }
            $total = M::t('superman_mall_comment')->count($filter);
            $list = M::t('superman_mall_comment')->fetchall($filter, '', $start, $pagesize);
            if ($list) {
                foreach ($list as &$li) {
                    $fans = mc_fansinfo($li['uid']);
                    $li['nickname'] = $fans['nickname'] != ''?$fans['nickname']:$li['uid'];
                    $li['star'] = SupermanUtil::get_web_comment_star($li['score']);
                    $item = M::t('superman_mall_item')->fetch($li['itemid']);
                    $li['item_title'] = $item['title'] != ''?$item['title']:'商品已删除';
                }
                unset($li, $fans, $order, $item);
            }
            $pager = pagination($total, $pindex, $pagesize);
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $comment_status = intval($_GPC['comment_status']);
            if (!in_array($comment_status, array(1,2))) {
                echo '系统错误';
                exit;
            }
            $comment = M::t('superman_mall_comment')->fetch($id);
            M::t('superman_mall_comment')->update(array('status' => $comment_status), array('id' => $id));
            echo 'success';
            exit;
        } else if ($act == 'batch') {
            if ($_GPC['id']) {
                if (isset($_GPC['btn_batch_allow'])) {
                    $status = 1;
                } else if (isset($_GPC['btn_batch_refuse'])) {
                    $status = 2;
                }
                if ($status) {
                    $data = array(
                        'status' => $status,
                    );
                    $condition = array(
                        'id' => $_GPC['id']
                    );
                    M::t('superman_mall_comment')->update($data, $condition);
                }
            }
            message('操作成功！', referer(), 'success');
        }
		include $this->template('comment');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display', 'post'))?$_GPC['act']:'display';
        $nav['title'] = '评价管理';
        if ($act == 'display') {
            if ($_GPC['status'] == -1) {
                $nav['subtitle'] = '全部评价';
            } else if ($_GPC['status'] == 0) {
                $nav['subtitle'] = '未审核评价';
            } else if ($_GPC['status'] == 1) {
                $nav['subtitle'] = '已审核评价';
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 20;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'shopid' => $this->shop['id'],
            );
            if (isset($_GPC['status']) && $_GPC['status'] == 0) {       //未审核
                $filter['status'] = 0;
            } else if (isset($_GPC['status']) && $_GPC['status'] == 1) {//已审核评价
                $filter['status'] = '#>0';
            }
            $total = M::t('superman_mall_comment')->count($filter);
            $list = M::t('superman_mall_comment')->fetchall($filter, '', $start, $pagesize);
            if ($list) {
                foreach ($list as &$li) {
                    $fans = mc_fansinfo($li['uid']);
                    $li['nickname'] = $fans['nickname'] != ''?$fans['nickname']:$li['uid'];
                    $li['star'] = SupermanUtil::get_web_comment_star($li['score']);
                    $item = M::t('superman_mall_item')->fetch($li['itemid']);
                    $li['item_title'] = $item['title'] != ''?$item['title']:'商品已删除';
                }
                unset($li, $fans, $order, $item);
            }
            $pager = pagination($total, $pindex, $pagesize);
        } else if ($act == 'post') {
            $nav['subtitle'] = '编辑';
            $id = intval($_GPC['id']);
            $comment_status = intval($_GPC['comment_status']);
            if (!in_array($comment_status, array(1,2))) {
                echo '系统错误';
                exit;
            }
            $comment = M::t('superman_mall_comment')->fetch($id);
            M::t('superman_mall_comment')->update(array('status' => $comment_status), array('id' => $id));
            echo 'success';
            exit;
        }
        include $this->template('comment/index');
    }
}

$obj = new Superman_mall_doWebComment;