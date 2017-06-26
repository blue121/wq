<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class SupermanMallData {
    public static function clearHomeApps() {
        global $_W;
        $condition = array(
            'uniacid' => $_W['uniacid'],
            'skey' => SUPERMAN_SKEY_HOME_APPS,
        );
        M::t('superman_mall_kv')->delete($condition);
    }
    public static function initHomeApps() {
        global $_W;
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'skey' => SUPERMAN_SKEY_HOME_APPS,
        );
        $row = M::t('superman_mall_kv')->fetch($filter);
        if (!$row) {
            $data = array(
                'uniacid' => $_W['uniacid'],
                'skey' => SUPERMAN_SKEY_HOME_APPS,
                'svalue' => iserializer(array(
                    array(
                        'displayorder' => 8,
                        'icon' => '#xe622;',
                        'title' => '首页',
                        'url' => murl('entry//home', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 7,
                        'icon' => '#xe62a;',
                        'title' => '分类',
                        'url' => murl('entry//category', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 6,
                        'icon' => '#xe627;',
                        'title' => '购物车',
                        'url' => murl('entry//cart', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 5,
                        'icon' => '#xe629;',
                        'title' => '我的',
                        'url' => murl('entry//my', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 4,
                        'icon' => '#xe622;',
                        'title' => '秒杀',
                        'url' => murl('entry//seckill', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 3,
                        'icon' => '#xe62a;',
                        'title' => '拼团',
                        'url' => murl('entry//mgroupon', array('act' => 'list', 'm' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 2,
                        'icon' => '#xe629;',
                        'title' => '签到',
                        'url' => murl('entry//sign', array('m' => 'superman_sign')),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 1,
                        'icon' => '#xe627;',
                        'title' => '积分商城',
                        'url' => murl('entry//home', array('m' => 'superman_creditmall')),
                        'isshow' => 1,
                    ),
                )),
            );
            $new_id = M::t('superman_mall_kv')->insert($data);
            if (!$new_id) {
                message('数据库操作失败(insert superman_mall_kv failed)！', '', 'error');
            }
        }
    }
}