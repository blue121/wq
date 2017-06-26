<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebSetting extends Superman {
    public function __construct() {
        parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_setting');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        }/* else {
            $this->do_admin();
        }*/
    }
/*    public function do_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('service')) ? $_GPC['act'] : 'service';
        $op = $_GPC['op'];
        if ($act == 'service') {


        } else {
            $this->json(ERRNO::INVALID_REQUEST);
        }
        include $this->template('setting');
    }*/
    public function do_shop_admin() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('service', 'slide')) ? $_GPC['act'] : 'service';
        $op = $_GPC['op'];
        $nav['title'] = '基本设置';
        if ($act == 'service') {
            $nav['subtitle'] = '联系客服';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_SHOP_SERVICE, $this->shop['id']);
            if (checksubmit()) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting'])
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_SHOP_SERVICE, $this->shop['id']);
                if ($setting) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('更新成功！', referer(), 'success');
            }
        } else if ($act == 'slide') {
            $nav['subtitle'] = '幻灯片';
            if (isset($_GPC['add']) && $_GPC['add'] == 'yes') {
                //include $this->template('slide-new');
                include $this->template('setting/new');
                exit();
            }
            $id = 0;
            $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_SHOP_SLIDE, $this->shop['id']);
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'skey' => $skey,
            );
            $row = M::t('superman_mall_kv')->fetch($filter);
            if ($row) {
                $id = $row['id'];
                $slides = iunserializer($row['svalue']);
            }
            if (checksubmit()) {
                $slides_data = array();
                if (isset($_GPC['slides_title']) && $_GPC['slides_title']) {
                    foreach ($_GPC['slides_title'] as $k => $v) {
                        $slides_data[] = array(
                            'title' => $_GPC['slides_title'][$k],
                            'img' => $_GPC['slides_img'][$k],
                            'url' => $_GPC['slides_url'][$k],
                        );
                    }
                }
                $data = array(
                    'svalue' => $slides_data?iserializer($slides_data):'',
                );
                if ($id) {
                    M::t('superman_mall_kv')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else {
            $this->json(ERRNO::INVALID_REQUEST);
        }
        //include $this->template('setting');
        include $this->template('setting/index');
    }

}
$obj = new Superman_mall_doWebSetting;