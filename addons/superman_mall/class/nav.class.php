<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class SuermanMallNav {
    public static function shop($user_permission = array()) {
        global $_W, $_GPC;
        $navs = array(
            array(
                'title' => '首页',
                'icon' => '',
                'querystring' => array(
                    'do' => 'dashboard',
                    'act' => 'display',
                ),
                'items' => array(),
            ),
            array(
                'title' => '商品',
                'icon' => '',
                'querystring' => array(
                    'do' => 'item',
                    'act' => 'display',
                    'status' => 'upshelf',
                ),
                'items' => array(
                    array(
                        'title' => '商品管理',
                        'icon' => 'fa fa-diamond',
                        'menus' => array(
                            array(
                                'title' => '出售中',
                                'querystring' => array(
                                    'do' => 'item',
                                    'act' => 'display',
                                    'status' => 'upshelf',
                                ),
                            ),
                            array(
                                'title' => '已售罄',
                                'querystring' => array(
                                    'do' => 'item',
                                    'act' => 'display',
                                    'status' => 'stockout',
                                ),
                            ),
                            array(
                                'title' => '仓库中',
                                'querystring' => array(
                                    'do' => 'item',
                                    'act' => 'display',
                                    'status' => 'offshelf',
                                ),
                            ),
                            array(
                                'title' => '禁售',
                                'querystring' => array(
                                    'do' => 'item',
                                    'act' => 'display',
                                    'status' => 'forbid',
                                ),
                            ),
                            array(
                                'title' => '规格',
                                'querystring' => array(
                                    'do' => 'spec',
                                    'act' => 'display',
                                ),
                                'do_prefix' => 'item',
                            ),
                            array(
                                'title' => '邮费模板',
                                'querystring' => array(
                                    'do' => 'postage',
                                    'act' => 'display',
                                ),
                                'do_prefix' => 'item',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'title' => '订单',
                'icon' => '',
                'querystring' => array(
                    'do' => 'order',
                    'act' => 'overview',
                ),
                'startpage' => array(
                    'title' => '订单概况',
                    'icon' => '',
                    'querystring' => array(
                        'do' => 'order',
                        'act' => 'overview',
                    ),
                ),
                'items' => array(
                    array(
                        'title' => '订单管理',
                        'icon' => 'fa fa-file-text-o',
                        'menus' => array(
                            array(
                                'title' => '全部',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'status' => 'all',
                                ),
                            ),
                            array(
                                'title' => '待付款',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'status' => '0',
                                ),
                            ),
                            array(
                                'title' => '待发货',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'status' => '1',
                                ),
                            ),
                            /*array(
                                'title' => '待收货',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'status' => '2',
                                ),
                            ),*/
                            array(
                                'title' => '待自提',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'dispatch_type' => '2',
                                ),
                            ),
                            array(
                                'title' => '货到付款',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'pay_type' => '3',
                                ),
                            ),
                            array(
                                'title' => '拼团',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'type' => '1',
                                ),
                            ),
                            /*array(
                                'title' => '已完成',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'status' => '4',
                                ),
                            ),
                            array(
                                'title' => '已关闭',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'status' => '-1',
                                ),
                            ),*/
                            array(
                                'title' => '自定义导出',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'export',
                                ),
                                'development' => 1,
                            ),
                            array(
                                'title' => '批量发货',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'batch',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'title' => '退款售后',
                        'icon' => 'fa fa-money',
                        'menus' => array(
                            array(
                                'title' => '申请退款',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'display',
                                    'status' => '-4',
                                ),
                            ),
                            array(
                                'title' => '售后管理',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'refund',
                                    'service_type' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'title' => '线下核销',
                        'icon' => 'fa fa-check-square-o',
                        'menus' => array(
                            array(
                                'title' => '核销记录',
                                'querystring' => array(
                                    'do' => 'checkout',
                                    'act' => 'display',
                                ),
                                'do_suffix' => 'display',
                            ),
                            array(
                                'title' => '扫码核销员',
                                'querystring' => array(
                                    'do' => 'checkout',
                                    'act' => 'qrcode',
                                ),
                                'do_suffix' => 'qrcode',
                            ),
                            array(
                                'title' => '自助核销员',
                                'querystring' => array(
                                    'do' => 'checkout',
                                    'act' => 'oneself',
                                ),
                                'do_suffix' => 'oneself',
                            ),
                        ),
                    ),
                    array(
                        'title' => '快递物流',
                        'icon' => 'fa fa-truck',
                        'menus' => array(
                            array(
                                'title' => '快递公司',
                                'querystring' => array(
                                    'do' => 'order',
                                    'act' => 'express',
                                ),
                            ),
                            array(
                                'title' => '自定义配送',
                                'querystring' => array(
                                    'do' => 'delivery',
                                    'act' => 'display',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'title' => '评价管理',
                        'icon' => 'fa fa-comments-o',
                        'menus' => array(
                            array(
                                'title' => '全部',
                                'querystring' => array(
                                    'do' => 'comment',
                                    'act' => 'display',
                                    'status' => '-1',
                                ),
                            ),
                            array(
                                'title' => '未审核',
                                'querystring' => array(
                                    'do' => 'comment',
                                    'act' => 'display',
                                    'status' => '0',
                                ),
                            ),
                            array(
                                'title' => '已审核',
                                'querystring' => array(
                                    'do' => 'comment',
                                    'act' => 'display',
                                    'status' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'title' => '数据',
                'icon' => '',
                'querystring' => array(
                    'do' => 'stat',
                    'act' => 'display',
                    'type' => 'item',
                ),
                'items' => array(
                    array(
                        'title' => '数据统计',
                        'icon' => 'fa fa-bar-chart',
                        'menus' => array(
                            array(
                                'title' => '店铺',
                                'querystring' => array(
                                    'do' => 'stat',
                                    'act' => 'display',
                                    'type' => 'shop',
                                ),
                                'development' => 1,
                            ),
                            array(
                                'title' => '商品',
                                'querystring' => array(
                                    'do' => 'stat',
                                    'act' => 'display',
                                    'type' => 'item',
                                ),
                            ),
                            array(
                                'title' => '订单',
                                'querystring' => array(
                                    'do' => 'stat',
                                    'act' => 'display',
                                    'type' => 'order',
                                ),
                                'development' => 1,
                            ),
                            array(
                                'title' => '会员',
                                'querystring' => array(
                                    'do' => 'stat',
                                    'act' => 'display',
                                    'type' => 'member',
                                ),
                                'development' => 1,
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'title' => '应用',
                'icon' => '',
                'querystring' => array(
                    'do' => 'plugin',
                    'act' => 'display',
                ),
                'startpage' => array(
                    'title' => '全部应用',
                    'icon' => '',
                    'querystring' => array(
                        'do' => 'plugin',
                        'act' => 'display',
                    ),
                ),
                'items' => array(
                    array(
                        'title' => '业务',
                        'icon' => 'fa fa-suitcase',
                        'menus' => array(
                            array(
                                'title' => '秒杀',
                                'querystring' => array(
                                    'do' => 'seckill',
                                    'act' => 'display',
                                ),
                                'do_prefix' => 'plugin',
                            ),
                            array(
                                'title' => '拼团',
                                'querystring' => array(
                                    'do' => 'mgroupon',
                                    'act' => 'display',
                                ),
                                'do_prefix' => 'plugin',
                            ),
                            array(
                                'title' => '分销',
                                'querystring' => array(
                                    'do' => 'partner',
                                    'act' => 'display',
                                ),
                                'development' => 1,
                            ),
                        ),
                    ),
                    array(
                        'title' => '营销活动',
                        'icon' => 'fa fa-star-o',
                        'menus' => array(
                            array(
                                'title' => '满包邮',
                                'querystring' => array(
                                    'do' => 'discount',
                                    'act' => 'display',
                                    'type' => '1',
                                ),
                            ),
                            array(
                                'title' => '限时打折',
                                'querystring' => array(
                                    'do' => 'discount',
                                    'act' => 'display',
                                    'type' => '2',
                                ),
                            ),
                            array(
                                'title' => '满减优惠',
                                'querystring' => array(
                                    'do' => 'discount',
                                    'act' => 'display',
                                    'type' => '3',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'title' => '工具',
                        'icon' => 'fa fa-wrench',
                        'menus' => array(
                            array(
                                'title' => '打印机',
                                'querystring' => array(
                                    'do' => 'printer',
                                    'act' => 'display',
                                ),
                            ),
                            array(
                                'title' => '淘宝助手',
                                'querystring' => array(
                                    'do' => 'tbast',
                                    'act' => 'display',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'title' => '财务',
                'icon' => '',
                'querystring' => array(
                    'do' => 'finance',
                    'act' => 'log',
                ),
                'items' => array(
                    array(
                        'title' => '财务管理',
                        'icon' => 'fa fa-usd',
                        'menus' => array(
                            array(
                                'title' => '提现记录',
                                'querystring' => array(
                                    'do' => 'finance',
                                    'act' => 'log',
                                ),
                            ),
                            array(
                                'title' => '申请提现',
                                'querystring' => array(
                                    'do' => 'finance',
                                    'act' => 'apply',
                                ),
                            ),
                            array(
                                'title' => '提现账户',
                                'querystring' => array(
                                    'do' => 'finance',
                                    'act' => 'user',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'title' => '设置',
                'icon' => '',
                'querystring' => array(
                    'do' => 'shop',
                    'act' => 'post',
                ),
                'items' => array(
                    array(
                        'title' => '我的店铺',
                        'icon' => 'fa fa-gift',
                        'menus' => array(
                            array(
                                'title' => '商户资料',
                                'querystring' => array(
                                    'do' => 'shop',
                                    'act' => 'post',
                                ),
                            ),
                            array(
                                'title' => '店铺入口',
                                'querystring' => array(
                                    'do' => 'shop',
                                    'act' => 'cover',
                                ),
                                'development' => 1,
                            ),
                            array(
                                'title' => '联系客服',
                                'querystring' => array(
                                    'do' => 'setting',
                                    'act' => 'service',
                                ),
                            ),
                            array(
                                'title' => '幻灯图广告',
                                'querystring' => array(
                                    'do' => 'setting',
                                    'act' => 'slide',
                                ),
                            ),
                            array(
                                'title' => '线下门店',
                                'querystring' => array(
                                    'do' => 'myfetch',
                                    'act' => 'display',
                                ),
                                'do_prefix' => 'item',
                            ),
                        ),
                    ),
                    array(
                        'title' => '权限',
                        'icon' => 'fa fa-unlock-alt',
                        'menus' => array(
                            array(
                                'title' => '账号管理',
                                'querystring' => array(
                                    'do' => 'user',
                                    'act' => 'user',
                                    'op' => 'display',
                                ),
                                'do_suffix' => 'user',
                            ),
                            array(
                                'title' => '账号身份',
                                'querystring' => array(
                                    'do' => 'user',
                                    'act' => 'group',
                                    'op' => 'display',
                                ),
                                'do_suffix' => 'group',
                            ),
                        ),
                    ),
                    array(
                        'title' => '触发器',
                        'icon' => 'fa fa-bullhorn',
                        'menus' => array(
                            array(
                                'title' => '添加规则',
                                'querystring' => array(
                                    'do' => 'trigger',
                                    'act' => 'post',
                                ),
                            ),
                            array(
                                'title' => '规则管理',
                                'querystring' => array(
                                    'do' => 'trigger',
                                    'act' => 'display',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $menu_active = 0;
        foreach ($navs as $n=>&$nav) {
            //nav active
            $nav['do'] = $nav['querystring']['do'];
            $nav['act'] = $nav['querystring']['act'];
            $segment = 'site/entry/' . $nav['do'];
            unset($nav['querystring']['do']);
            $nav['querystring']['m'] = SUPERMAN_MODULE_NAME;
            $nav['querystring']['_n'] = $n;
            $nav['url'] = wurl($segment, $nav['querystring']);
            $_n = isset($_GET['_n'])?$_GET['_n']:$_GPC['_n'];
            if ($_n == $n || (!isset($_GPC['_n']) && $n == 0)) {
                isetcookie('_n', $n, 0, true);
                $nav['active'] = 'active';
            } else {
                $nav['active'] = '';
            }
            if (!empty($nav['startpage'])) {
                $nav['startpage']['do'] = $nav['startpage']['querystring']['do'];
                $nav['startpage']['act'] = $nav['startpage']['querystring']['act'];
                $segment = 'site/entry/' . $nav['startpage']['do'];
                unset($nav['startpage']['querystring']['do']);
                $nav['startpage']['querystring']['m'] = SUPERMAN_MODULE_NAME;
                $nav['startpage']['querystring']['_n'] = $n;
                $nav['startpage']['url'] = wurl($segment, $nav['startpage']['querystring']);
                $query = parse_url($nav['startpage']['url'], PHP_URL_QUERY);
                parse_str($query, $urls);
                $query = parse_url($_W['siteurl'], PHP_URL_QUERY);
                parse_str($query, $get);
                $diff = array_diff_assoc($urls, $get);
                if (empty($diff)) {
                    $nav['startpage']['active'] = 'active';
                } else {
                    $nav['startpage']['active'] = '';
                }
            }
            foreach ($nav['items'] as &$item) {
                //menu
                if (empty($item['menus'])) {
                    continue;
                }
                $item['max_matching_count'] = 0;
                foreach ($item['menus'] as $k=>&$menu) {
                    $menu['do'] = $menu['querystring']['do'];
                    $menu['act'] = $menu['querystring']['act'];
                    $segment = 'site/entry/' . $menu['querystring']['do'];
                    unset($menu['querystring']['do']);
                    $menu['querystring']['m'] = SUPERMAN_MODULE_NAME;
                    $menu['querystring']['_n'] = $n;
                    $menu['url'] = wurl($segment, $menu['querystring']);
                    $query = parse_url($menu['url'], PHP_URL_QUERY);
                    parse_str($query, $menu_urls);
                    ksort($menu_urls);
                    $query = parse_url($_W['siteurl'], PHP_URL_QUERY);
                    parse_str($query, $querystring);
                    ksort($querystring);
                    if (!isset($querystring['_n'])) {
                        unset($menu_urls['_n']);
                    }
                    $menu['querystring_matching_count'] = count(array_intersect_assoc($querystring, $menu_urls));
                    if ($menu['querystring_matching_count'] > $item['max_matching_count']) {
                        $item['max_matching_count'] = $menu['querystring_matching_count'];
                    }

                    if (isset($menu['development']) && $menu['development']) {
                        if (defined('SUPERMAN_DEVELOPMENT')) {
                            $menu['title'] = $menu['title'].'-dev';
                        } else {
                            unset($item['menus'][$k]);
                            continue;
                        }
                    }
                    //menu permission
                    if (is_array($user_permission)) {
                        $menu_permission = 'superman_mall_menu_';
                        if (isset($menu['do_prefix'])) {
                            $menu_permission .= $menu['do_prefix'] . '_';
                        }
                        $menu_permission .= $menu['do'];
                        if (isset($menu['do_suffix'])) {
                            $menu_permission .= '_'.$menu['do_suffix'];
                        }
                        $has_permission = false;
                        foreach ($user_permission as $p) {
                            if ($menu_permission == $p || strexists($p, $menu_permission)) {
                                $has_permission = true;
                                break;
                            }
                        }
                        if (!$has_permission) {
                            unset($item['menus'][$k]);
                            continue;
                        }
                    }
                    //menu active
                    if ($menu['querystring_matching_count'] == count($querystring)) {
                        $menu['active'] = 'active';
                        $menu_active = 1;
                    } else {
                        $menu['active'] = '';
                    }
                    /*if (count($querystring) > count($menu_urls)) {
                        $diff = array_diff_assoc($querystring, $menu_urls);
                    } else {
                        $diff = array_diff_assoc($menu_urls, $querystring);
                    }
                    if (empty($diff)) {
                        var_dump($diff);
                        echo '<hr>';
                        $menu['active'] = 'active';
                        $menu_active = 1;
                    } else {
                        $menu['active'] = '';
                    }*/
                }
            }
        }

        //recheck menu active
        if (!$menu_active) {
            foreach ($navs as $n=>&$nav) {
                if (!$nav['active'] || !empty($nav['startpage']['active'])) {
                    continue;
                }
                foreach ($nav['items'] as &$item) {
                    foreach ($item['menus'] as $k => &$menu) {
                        if ($menu['querystring_matching_count'] == $item['max_matching_count']) {
                            $menu['active'] = 'active';
                            $menu_active = 1;
                            break;
                        }
                    }
                    if ($menu_active) {
                        break;
                    }
                }
            }
        }
        unset($nav);
        unset($menu);
        unset($item);
        return $navs;
    }
}