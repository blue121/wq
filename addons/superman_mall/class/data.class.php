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
    public static function initFooterNav() {
        global $_W;
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'skey' => SUPERMAN_SKEY_FOOTER_NAV,
        );
        $row = M::t('superman_mall_kv')->fetch($filter);
        if (!$row) {
            $data = array(
                'uniacid' => $_W['uniacid'],
                'skey' => SUPERMAN_SKEY_FOOTER_NAV,
                'svalue' => iserializer(array(
                    array(
                        'displayorder' => 5,
                        'icon' => '#xe622;',
                        'title' => '首页',
                        'url' => murl('entry//home', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 4,
                        'icon' => '#xe609;',
                        'title' => '商户',
                        'url' => murl('entry//shop', array('m' => SUPERMAN_MODULE_NAME, 'act' => 'list')),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 3,
                        'icon' => '#xe62a;',
                        'title' => '分类',
                        'url' => murl('entry//category', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 2,
                        'icon' => '#xe627;',
                        'title' => '购物车',
                        'url' => murl('entry//cart', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 1,
                        'icon' => '#xe629;',
                        'title' => '我的',
                        'url' => murl('entry//my', array('m' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ))
                )
            );
            $new_id = M::t('superman_mall_kv')->insert($data);
            if (!$new_id) {
                message('initFooterNav:数据库操作失败(insert superman_mall_kv failed)！', '', 'error');
            }
        }
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
                        'icon' => '#xe609;',
                        'title' => '商户',
                        'url' => murl('entry//shop', array('act' => 'list', 'm' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                    array(
                        'displayorder' => 1,
                        'icon' => '&#xe63a;',
                        'title' => '分销',
                        'url' => murl('entry//partner', array('act' => 'home', 'm' => SUPERMAN_MODULE_NAME)),
                        'isshow' => 1,
                    ),
                )),
            );
            $new_id = M::t('superman_mall_kv')->insert($data);
            if (!$new_id) {
                message('initHomeApps:数据库操作失败(insert superman_mall_kv failed)！', '', 'error');
            }
        }
    }
    public static function initCustomMenus() {
        $sql = "SELECT * FROM ".tablename('modules_bindings')." WHERE module=:module AND entry=:entry";
        $params = array(
            ':module' => 'superman_mall',
            ':entry' => 'mine',
        );
        $bind = pdo_fetch($sql, $params);
        if (empty($bind)) {
            $data = array(
                'module' => 'superman_mall',
                'entry' => 'mine',
                'call' => 'customMenus',
            );
            pdo_insert('modules_bindings', $data);
            $new_id = pdo_insertid();
            if (!$new_id) {
                WeUtility::logging('fatal', '[customMenus] init failed');
            }
        }
    }
    public static function initExpressCompany() {
        global $_W;
        $content = file_get_contents(MODULE_ROOT.'/data/express_company.data');
        if (empty($content)) {
            return;
        }
        $arr = explode("\n", $content);
        foreach ($arr as $line) {
            $a = explode(',', $line);
            $title = trim($a[0]);
            $alias = trim($a[1]);
            $data = array(
                'uniacid' => $_W['uniacid'],
                'title' => $title,
                'alias' => $alias,
            );
            $row = M::t('superman_mall_express_company')->fetch($data);
            if ($row) {
                continue;
            }
            pdo_insert('superman_mall_express_company', $data);
        }
    }
    public static function initPartnerCustomText(&$texts) {
        if ($texts === NULL) {
            return;
        }
        $texts['innerprice'] = !empty($texts['innerprice'])?$texts['innerprice']:'内部价';
        $texts['distribution'] = !empty($texts['distribution'])?$texts['distribution']:'我要分销';
        $texts['commission'] = !empty($texts['commission'])?$texts['commission']:'佣金';
        $texts['self_sell'] = !empty($texts['self_sell'])?$texts['self_sell']:'自己卖出';
        $texts['invite_direct'] = !empty($texts['invite_direct'])?$texts['invite_direct']:'直接邀请成员买/卖';
        $texts['invite_indirect'] = !empty($texts['invite_indirect'])?$texts['invite_indirect']:'间接邀请成员买/卖';
        $texts['partner_center'] = !empty($texts['partner_center'])?$texts['partner_center']:'分销中心';
        $texts['get_commission'] = !empty($texts['get_commission'])?$texts['get_commission']:'赚佣金';
        $texts['commission_total'] = !empty($texts['commission_total'])?$texts['commission_total']:'累计佣金';
        $texts['commission_received'] = !empty($texts['commission_received'])?$texts['commission_received']:'已提佣金';
        $texts['commission_balance'] = !empty($texts['commission_balance'])?$texts['commission_balance']:'可提佣金';
        $texts['invite_total'] = !empty($texts['invite_total'])?$texts['invite_total']:'邀请人数';
        $texts['order_total'] = !empty($texts['order_total'])?$texts['order_total']:'订单数';
        $texts['downline1'] = !empty($texts['downline1'])?$texts['downline1']:'一级人数';
        $texts['downline2'] = !empty($texts['downline2'])?$texts['downline2']:'二级人数';
        $texts['downline3'] = !empty($texts['downline3'])?$texts['downline3']:'三级人数';
        $texts['order_reward'] = !empty($texts['order_reward'])?$texts['order_reward']:'本单奖励';
        $texts['apply_join'] = !empty($texts['apply_join'])?$texts['apply_join']:'申请加入';
        //分销中心首页九宫格
        $texts['myteam'] = !empty($texts['myteam'])?$texts['myteam']:'我的团队';
        $texts['invite_friend'] = !empty($texts['invite_friend'])?$texts['invite_friend']:'邀请好友';
        $texts['commission_rank'] = !empty($texts['commission_rank'])?$texts['commission_rank']:'佣金排行';
        $texts['order'] = !empty($texts['order'])?$texts['order']:'推广订单';
        $texts['getcash_display'] = !empty($texts['getcash_display'])?$texts['getcash_display']:'提现记录';
        $texts['getcash_apply'] = !empty($texts['getcash_apply'])?$texts['getcash_apply']:'我要提现';
        $texts['my_poster'] = !empty($texts['my_poster'])?$texts['my_poster']:'我的海报';
    }
    public static function initCountry() {
        global $_W;
        $content = file_get_contents(MODULE_ROOT.'/data/country.data');
        if (empty($content)) {
            return;
        }
        $arr = explode("\n", $content);
        foreach ($arr as $line) {
            if (substr($line, 0, 1) == '#') {
                continue;
            }
            $a = explode('|||', $line);
            $title = trim($a[0]);
            $areacode = trim($a[1]);
            $isshow = trim($a[2]);
            $mobile_pattern = trim($a[3]);
            $data = array(
                'title' => $title,
                'areacode' => $areacode,
                'mobile_pattern' => $mobile_pattern,
            );
            $row = M::t('superman_mall_country')->fetch($data);
            if ($row && $row['isshow'] == $isshow && !empty($row['mobile_pattern'])) {
                continue;
            }
            $data['isshow'] = $isshow;
            if ($row) {
                $condition = array(
                    'id' => $row['id'],
                );
                M::t('superman_mall_country')->update($data, $condition);
            } else {
                M::t('superman_mall_country')->insert($data);
            }
        }
    }
    public static function initSiteRoot() {
        global $_W;
        if (!defined('IN_SUPERMAN_MALL_ADMIN')) { //平台后台
            if (!file_exists(MODULE_ROOT.'/data/siteroot.txt')) {
                file_put_contents(MODULE_ROOT.'/data/siteroot.txt', $_W['siteroot']);
            } else {
                $siteroot = file_get_contents(MODULE_ROOT.'/data/siteroot.txt');
                if ($siteroot != $_W['siteroot']) {
                    file_put_contents(MODULE_ROOT.'/data/siteroot.txt', $_W['siteroot']);
                }
            }
        }
    }
}