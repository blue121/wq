<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileDetail extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '商品详情';
        $do = $do ? $do : 'detail';
        $act = in_array($_GPC['act'], array('display', 'follow', 'share', 'view', 'checkauth')) ? $_GPC['act'] : 'display';
        if ($act == 'checkauth') {
            $itemid = intval($_GPC['itemid']);
            $this->checkauth();
            @header('Location: '.$this->createMobile('detail', array('itemid' => $itemid)));
            exit;
        } else if ($act == 'display') {
            $itemid = intval($_GPC['itemid']);
            if (!$itemid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $item = M::t('superman_mall_item')->fetch($itemid);
            if (!$item) {
                $this->json(ERRNO::ITEM_NOT_FOUND);
            }
            $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
            $item['shop'] = M::t('superman_mall_shop')->fetch($item['shopid']);
            //商户客服设置
            $shop_service = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_SHOP_SERVICE, $item['shopid']);
            //秒杀商品
            if ($item['special'] == 1) {
                $seckill_time = SupermanUtil::get_seckill_time();
                if ($item['starttime'] > TIMESTAMP) {
                    $starttime = strtotime(date('Y-m-d '.$item['seckill_time'].':0:0', $item['starttime']));
                } else if ($item['starttime'] < TIMESTAMP && $item['endtime'] > TIMESTAMP) {
                    if ($seckill_time == $item['seckill_time']) {
                        $starttime = 0; //进行中
                    } else if ($seckill_time > $item['seckill_time']) {
                        $starttime = strtotime(date('Y-m-d '.$item['seckill_time'].':0:0', strtotime('+1 days'))); //明天
                    } else {
                        $starttime = strtotime(date('Y-m-d '.$item['seckill_time'].':0:0'));
                    }
                } else {
                    $starttime = -1; //已结束
                }
            }
            //拼团商品是否有团长
            if ($item['special'] == 2) {
                //取未完成的团
                $sql = "SELECT g.* FROM ".tablename('superman_mall_order_item').' AS i,'.tablename('superman_mall_merge_groupon').' AS g';
                $sql .= " WHERE i.itemid=:itemid AND i.orderid=g.orderid AND g.expiretime>:expiretime AND g.mgid=:mgid AND g.status IN (0, 2) AND g.pay_status=:pay_status";
                $sql .= " ORDER BY g.id DESC LIMIT 5";
                $params = array(
                    ':itemid' => $itemid,
                    ':mgid' => 0,
                    ':pay_status' => 1,
                    ':expiretime' => TIMESTAMP
                );
                $mg_list = pdo_fetchall($sql, $params);
                if ($mg_list) {
                    foreach ($mg_list as &$li) {
                        $li['member'] = mc_fetch($li['uid']);
                        $count = M::t('superman_mall_merge_groupon')->count(array(
                            'mgid' => $li['id'],
                            'pay_status' => 1,
                        ));
                        $li['residue'] = $li['limit'] - $count - 1;
                    }
                    unset($li);
                }

                $mgroupon_id = intval($_GPC['__mgroupon_id']);
                if ($mgroupon_id > 0) { //有团长
                    $mgroupon = M::t('superman_mall_merge_groupon')->fetch($mgroupon_id);
                    if ($mgroupon) {
                        $order_item = M::t('superman_mall_order_item')->fetch(array(
                            'orderid' => $mgroupon['orderid']
                        ));
                        if ($mgroupon['uid'] == $_W['member']['uid'] || $order_item && $order_item['itemid'] != $item['id'] || $mgroupon['shopid'] != $item['shopid']) {
                            //不同商品不能拼团
                            isetcookie('__mgroupon_id', 0, -1);
                            $mgroupon_id = 0;
                        } else {
                            isetcookie('__mgroupon_id', $mgroupon_id, $mgroupon['expiretime'] - TIMESTAMP);
                        }
                    } else {
                        isetcookie('__mgroupon_id', 0, -1);
                        $mgroupon_id = 0;
                    }
                }
            }

            //已购买数量
            if (isset($item['extend']['other_attr']['max_buy_num']) && $item['extend']['other_attr']['max_buy_num'] > 0) { //每帐号可买
                $sql = 'SELECT SUM(a.total) FROM '.tablename('superman_mall_order_item').' AS a,'.tablename('superman_mall_order').' AS b ';
                $sql .= ' WHERE a.orderid=b.id AND b.uid=:uid AND a.itemid=:itemid AND b.status>0';
                $params = array(
                    ':uid' => $_W['member']['uid'],
                    ':itemid' => $itemid,
                );
                $buy_sum = pdo_fetchcolumn($sql, $params);
            }

            //获取引导关注设置
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);
            if ($_W['member']['uid']) {
                $fans = mc_fansinfo($_W['member']['uid']);
                if ($fans['follow']) {
                    unset($setting);    //已关注不显示引导关注内容
                }
            }
            //获取评价数据
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 5;
            $start = ($pindex - 1) * $pagesize;
            $comments = M::t('superman_mall_comment')->fetchall(array(
                'uniacid' => $_W['uniacid'],
                'itemid' => $itemid,
                'status' => 1,
            ), '', $start, $pagesize);
            if ($comments) {
                $_members = array();
                $attachment_path = SupermanUtil::attachment_path();
                foreach ($comments as &$comment) {
                    $comment['star'] = SupermanUtil::get_comment_star($comment['score']);
                    $comment['dateline'] = date('Y-m-d H:i:s', $comment['dateline']);
                    $imgs = $comment['img']?unserialize($comment['img']):array();
                    foreach ($imgs as $img) {
                        $thumb = SupermanUtil::get_thumb_filename($img);
                        $comment['imglist'][] = array(
                            'thumb' => file_exists($attachment_path.$thumb)?$thumb:$img,
                            'img' => $img,
                        );
                    }

                    if (!isset($_members[$comment['uid']])) {
                        $_members[$comment['uid']] = mc_fetch($comment['uid'], array('nickname'));
                        if ($comment['anonymous'] && $_members[$comment['uid']] && $_members[$comment['uid']]['nickname']) {
                            $_members[$comment['uid']]['nickname'] = SupermanUtil::hide_nickname($_members[$comment['uid']]['nickname']);
                        }
                    }
                    $comment['member'] = $_members[$comment['uid']];
                }
                unset($comment);
            }
            //加载更多评价数据
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($comments));
            }
            if ($item['special'] == 2) {    //拼团商品加载规则
                $mgroupon_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, intval($item['shopid']));
            }

            //设置分享参数
            $_share = array(
                'title'   => $item['title'],
                'link'    => $_W['siteurl'],
                'imgUrl'  => tomedia($item['cover']),
                'content' => $item['subtitle']?cutstr($item['subtitle'], 60):cutstr($item['summary'], 60),
            );
            $item['album'] = $item['album']?iunserializer($item['album']):array();
            $item['params'] = M::t('superman_mall_item_params')->fetchall(array(
                'itemid' => $itemid,
            ), 'ORDER BY displayorder DESC,id DESC', 0, -1);
            $item['comments'] = M::t('superman_mall_comment')->fetchall(array(
                'itemid' => $itemid,
            ));
            $item['isfollow'] = false;
            if ($_W['member']['uid']) {
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'itemid' => $itemid,
                    'status' => 1,
                );
                $row = M::t('superman_mall_item_follow')->fetch($filter);
                $item['isfollow'] = $row?true:false;
            }

            $myfetch_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MYFETCH_SETTING, $item['shopid']);
            //支付设置
            $payments_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYMENTS);
            $uni_setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
            $credit_group = $this->get_credit_titles();

            //平台营销设置
            $activitys = array();
            $has_discount = 0;
            if (isset($this->plugin_setting['discount']) && $this->plugin_setting['discount']) {
                $discount_permission = $this->check_plugin_permission('discount', $item['shopid']);
                if (!is_error($discount_permission)) {
                    //包邮活动
                    $activitys['type1'] = $this->get_type1_activity($item);
                    //限时折扣
                    $activitys['type2'] = $this->get_type2_activity($item);
                    //满减促销
                    $activitys['type3'] = $this->get_type3_activity($item);
                    //积分抵现
                    $discount_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_DISCOUNT);
                    if (isset($discount_setting['credit']['cash_open']) && $discount_setting['credit']['cash_open'] == 1
                        && $item['cash_credit'] > 0) {
                        $creditname = $credit_group[$discount_setting['credit']['credit_type']]['title'];
                    }
                    if ($activitys['type1'] || $activitys['type2'] || $activitys['type3']) {
                        $has_discount = 1;
                    }
                }
            }

            //默认选择的规格
            $item_default_spec = '';
            $attrids = array();
            $filter = array(
                'itemid' => $itemid,
            );
            $item['sku'] = M::t('superman_mall_item_sku')->fetchall($filter, 'ORDER BY id ASC', 0, -1);
            $valueids = array();
            if ($item['sku']) {
                foreach ($item['sku'] as &$li) {
                    if (!isset($item_default_spec_total)) {
                        $item_default_spec_total = $li['total'];
                    }
                    if (!isset($item_default_spec_price)) {
                        if (isset($activitys['type2']['ai_extend']['value']) && $activitys['type2']['starttime'] < TIMESTAMP) {
                            $item_default_spec_price = SupermanUtil::float_format($li['price']*$activitys['type2']['ai_extend']['value']/10);
                            $item_default = $li['price'];
                        } else {
                            $item_default_spec_price = $li['price'];
                        }
                    }
                    if (!isset($item_default_spec_skuid)) {
                        $item_default_spec_skuid = $li['id'];
                    }
                    $li['attr'] = M::t('superman_mall_item_attr')->fetchall_by_valueids(explode(',', $li['valueids']), '', 0, -1, 'id');
                    if ($li['attr']) {
                        if (empty($item_default_spec)) {
                            $arr = array();
                            foreach ($li['attr'] as $attr) {
                                $arr[] = "{$attr['title']}:{$attr['value']}";
                            }
                            $item_default_spec = implode(' ', $arr);
                        }
                        foreach ($li['attr'] as $attr) {
                            $attrids[] = $attr['attrid'];
                            $valueids[] = $attr['valueid'];
                        }
                        //print_r($li['attr']);
                    }
                }
                unset($li);
            } else {
                $item_default_spec_total = $item['total'];
                if (isset($activitys['type2']['ai_extend']['value']) && $activitys['type2']['starttime'] < TIMESTAMP) {
                    $item_default_spec_price = SupermanUtil::float_format($item['price'] * $activitys['type2']['ai_extend']['value']/10);
                    $item_default = $item['price'];
                } else {
                    $item_default_spec_price = $item['price'];
                }
            }
            //print_r($item);
            $item_attr = array();
            $attrids = array_unique($attrids);
            sort($attrids);
            $valueids = array_unique($valueids);
            sort($valueids);
            //print_r($attrids);
            //print_r($valueids);
            if ($attrids) {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'id' => $attrids,
                );
                $item_attr = M::t('superman_mall_item_attr')->fetchall($filter, 'ORDER BY displayorder DESC, id DESC', 0, -1);
                if ($item_attr) {
                    foreach ($item_attr as &$attr) {
                        $values = M::t('superman_mall_item_value')->fetchall(array('attrid' => $attr['id']), 'ORDER BY displayorder DESC, id DESC', 0, -1);
                        if ($values) {
                            foreach ($values as $v) {
                                if (in_array($v['id'], $valueids)) {
                                    $attr['values'][] = $v;
                                }
                            }
                        }
                    }
                    unset($attr);
                }
            }
            //print_r($item_attr);
            $item_sku = M::t('superman_mall_item_sku')->fetchall(array('itemid' => $itemid), null, 0, -1, 'valueids');
            if ($item_sku) {
                foreach ($item_sku as &$sku) {
                    if (isset($activitys['type2']['ai_extend']['value']) && $activitys['type2']['starttime'] < TIMESTAMP) {
                        $sku['price'] = SupermanUtil::float_format($sku['price'] * $activitys['type2']['ai_extend']['value'] / 10);
                    }
                }
                unset($sku);
            }
            //print_r($item_sku);
            $this->browse($itemid);

            //分销
            $partner_permission = $this->check_plugin_permission('partner', $item['shopid']);    //商户分销功能开关
            if (isset($this->plugin_setting['partner']) && $this->plugin_setting['partner'] == 1 && !is_error($partner_permission)) {
                $partner = M::t('superman_mall_partner')->fetch(array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
                if (!$partner) {
                    $fromid = intval($_GPC['fromid']);
                    if ($fromid) {
                        $recommend = M::t('superman_mall_partner')->fetch($fromid);
                        if ($recommend && $recommend['status'] == 1 && $recommend['uid'] != $_W['member']['uid']) {  //客户
                            $expire_time = 30*24*60*60; //有效期30天
                            isetcookie('__partnerid', $fromid, $expire_time);
                        }
                    }
                } else {
                    if ($item['partner_open'] == 1) {
                        $partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
                        if ($partner && $partner['status'] == 1) {  //状态正常的分销商
                            $item_partner_stat = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $item['id']));
                            if ($item_partner_stat) {
                                $discount_price = SupermanUtil::float_format($item_partner_stat['discount_rate']>0?$item_partner_stat['discount_rate']/100*$item['price']:$item['price']-$item_partner_stat['discount_value']);
                                //开启佣金显示
                                if ($item_partner_stat['commission_show'] == 1) {
                                    //佣金自定义
                                    if ($item_partner_stat['commission_custom'] == 1) {
                                        $commission1 = SupermanUtil::float_format($item_partner_stat['commission1_rate']>0?$item_partner_stat['commission1_rate']/100*$item['price']:$item_partner_stat['commission1_value']);
                                        $commission2 = SupermanUtil::float_format($item_partner_stat['commission2_rate']>0?$item_partner_stat['commission2_rate']/100*$item['price']:$item_partner_stat['commission2_value']);
                                        $commission3 = SupermanUtil::float_format($item_partner_stat['commission3_rate']>0?$item_partner_stat['commission3_rate']/100*$item['price']:$item_partner_stat['commission3_value']);
                                    } else {
                                        //分销等级
                                        $partner_group = M::t('superman_mall_partner_group')->fetch($partner['groupid']);
                                        if ($partner_group) {
                                            $commission1 = SupermanUtil::float_format($partner_group['rate1']/100*$item['price']);
                                            $commission2 = SupermanUtil::float_format($partner_group['rate2']/100*$item['price']);
                                            $commission3 = SupermanUtil::float_format($partner_group['rate3']/100*$item['price']);
                                        }
                                    }
                                }
                            }
                            //设置分享参数
                            $_share = array(
                                'title'   => $item['title'],
                                'link'    => $_W['siteurl'].'&fromid='.$partner['id'],
                                'imgUrl'  => tomedia($item['cover']),
                                'content' => $item['subtitle']?cutstr($item['subtitle'], 60):cutstr($item['summary'], 60),
                            );
                        }
                        if (!$partner_setting) {
                            $partner_setting['text'] = array();
                        }
                        SupermanMallData::initPartnerCustomText($partner_setting['text']);
                    }
                }
            }

            $_SESSION['confirm_check'] = true;
        } else if ($act == 'follow') {
            $this->checkauth();
            $itemid = intval($_GPC['itemid']);
            $status = intval($_GPC['status']);
            if (!$itemid) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $item = M::t('superman_mall_item')->fetch($itemid);
            if (!$item) {
                $this->json(ERRNO::ITEM_NOT_FOUND);
            }
            $filter = array(
                'uid' => $_W['member']['uid'],
                'itemid' => $itemid,
            );
            $follow = M::t('superman_mall_item_follow')->fetch($filter);
            if ($follow) {  //update
                $data = array(
                    'status' => $status,
                    'dateline' => TIMESTAMP,
                );
                $condition = array(
                    'id' => $follow['id'],
                );
                M::t('superman_mall_item_follow')->update($data, $condition);
            } else {    //insert
                $data = array(
                    'uid' => $_W['member']['uid'],
                    'itemid' => $itemid,
                    'status' => $status,
                    'dateline' => TIMESTAMP,
                );
                $new_id = M::t('superman_mall_item_follow')->insert($data);
                if (!$new_id) {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
            }
            $this->json(ERRNO::OK);
        } else if ($act == 'view') {
            $itemid = intval($_GPC['itemid']);
            $shopid = intval($_GPC['shopid']);
            //if ($_W['container'] == 'wechat' && $itemid && $shopid) {
            if ($itemid && $shopid) {
                //更新商品浏览数&统计数据(item_view)
                $this->update_item_view($itemid, $shopid);
                //更新统计数据(page_view)
                $this->update_page_view($shopid);
                //更新统计数据(unique_visitor)
                $this->update_unique_visitor($shopid);
            }
            exit();
        } else if ($act == 'share') {
            $itemid = intval($_GPC['itemid']);
            $shopid = intval($_GPC['shopid']);
            //if ($_W['container'] == 'wechat' && $itemid && $shopid) {
            if ($itemid && $shopid) {
                //更新商品分享数
                $this->update_item_share($itemid, $shopid);
            }
            exit();
        }
		include $this->template('detail');
    }

    private function get_type2_activity($item) {
        global $_W;
        $active = array();
        $low_price = $item['price'];
        $sql = "SELECT a.*,i.extend AS ai_extend FROM " . tablename('superman_mall_shop_activity') . " AS a," . tablename('superman_mall_shop_activity_item') . " AS i";
        $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=2 AND a.endtime>:endtime AND i.itemid=:itemid";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':shopid' => $item['shopid'],
//            ':starttime' => TIMESTAMP,
            ':endtime' => TIMESTAMP,
            ':itemid' => $item['id']
        );
        $activitys = pdo_fetchall($sql, $params);
        if ($activitys) {
            foreach ($activitys as $k => &$aty) {
                $aty['ai_extend'] = $aty['extend']?iunserializer($aty['ai_extend']):array();
                if (isset($aty['ai_extend']['value'])) {
                    $price = $item['price']*$aty['ai_extend']['value']/10;
                    if ($price < $low_price) {
                        $low_price = $price;
                        $active = $aty;
                    }
                }
            }
            unset($aty);
        }
        return $active;
    }

    //获取所有满减活动
    private function get_type3_activity($item) {
        global $_W;
        $arr = array();
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'shopid' => $item['shopid'],
            'type' => 3,
            'starttime' => '# <' . TIMESTAMP,
            'endtime' => '# >' . TIMESTAMP,
            'isglobal' => 1,
        );
        $activitys = M::t('superman_mall_shop_activity')->fetchall($filter, '', 0, -1);
        if ($activitys) {
            foreach ($activitys as &$li) {
                $li['extend'] = $li['extend'] ? iunserializer($li['extend']) : array();
                if ($li['extend']) {
                    foreach ($li['extend'] as $ext) {
                        $title = '满'.$ext['full']['value'];
                        $title .= $ext['full']['unit'] == 'yuan'?'元':'件';
                        $title .= $ext['minus']['unit'] == 'yuan'?'减'.$ext['minus']['value'].'元':'打'.$li['minus']['value'].'折';
                        $li['extend_content'][] = $title;
                    }
                }
                $arr[] = $li;
            }
            unset($li);
        }
        $sql = "SELECT a.* FROM ".tablename('superman_mall_shop_activity')." AS a,".tablename('superman_mall_shop_activity_item')." AS i";
        $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=3 AND a.starttime<:starttime AND a.endtime>:endtime AND i.itemid=:itemid";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':shopid' => $item['shopid'],
            ':starttime' => TIMESTAMP,
            ':endtime' => TIMESTAMP,
            ':itemid' => $item['id']
        );
        $activitys = pdo_fetchall($sql, $params);
        if ($activitys) {
            foreach ($activitys as &$li) {
                $li['extend'] = $li['extend'] ? iunserializer($li['extend']) : array();
                if ($li['extend']) {
                    foreach ($li['extend'] as $ext) {
                        $title = '满'.$ext['full']['value'];
                        $title .= $ext['full']['unit'] == 'yuan'?'元':'件';
                        $title .= $ext['minus']['unit'] == 'yuan'?'减'.$ext['minus']['value'].'元':'打'.$li['minus']['value'].'折';
                        $li['extend_content'][] = $title;
                    }
                }
                $arr[] = $li;
            }
            unset($li);
        }
        return $arr;
    }

    //获取所有包邮活动
    private function get_type1_activity($item) {
        global $_W;
        $arr = array();
        //全店活动
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'shopid' => $item['shopid'],
            'type' => 1,
            'starttime' => '# <' . TIMESTAMP,
            'endtime' => '# >' . TIMESTAMP,
            'isglobal' => 1,
        );
        $activitys = M::t('superman_mall_shop_activity')->fetchall($filter, '', 0, -1);
        if ($activitys) {
            foreach ($activitys as &$li) {
                $li['extend'] = $li['extend'] ? iunserializer($li['extend']) : array();
                $arr[] = $li;
            }
            unset($li);
        }
        $sql = "SELECT a.* FROM ".tablename('superman_mall_shop_activity')." AS a,".tablename('superman_mall_shop_activity_item')." AS i";
        $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=1 AND a.starttime<:starttime AND a.endtime>:endtime AND i.itemid=:itemid";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':shopid' => $item['shopid'],
            ':starttime' => TIMESTAMP,
            ':endtime' => TIMESTAMP,
            ':itemid' => $item['id']
        );
        $activitys = pdo_fetchall($sql, $params);
        if ($activitys) {
            foreach ($activitys as &$li) {
                $li['extend'] = $li['extend'] ? iunserializer($li['extend']) : array();
                $arr[] = $li;
            }
            unset($li);
        }
        return $arr;
    }

    private function browse($itemid) {
        global $_W;
        if ($_W['member']['uid']) {
            $row = M::t('superman_mall_item_browse')->fetch(array(
                'uid' => $_W['member']['uid'],
                'itemid' => $itemid,
            ));
            if ($row) { //update
                $condition = array(
                    'uid' => $_W['member']['uid'],
                    'itemid' => $itemid,
                );
                M::t('superman_mall_item_browse')->update(array('dateline' => TIMESTAMP), $condition);
            } else { //insert
                $data = array(
                    'uid' => $_W['member']['uid'],
                    'itemid' => $itemid,
                    'dateline' => TIMESTAMP,
                );
                M::t('superman_mall_item_browse')->insert($data);
            }

            //清理
            $max_total = 100;
            $total = M::t('superman_mall_item_browse')->count(array('uid' => $_W['member']['uid']));
            if ($total > $max_total) {
                M::t('superman_mall_item_browse')->clean($_W['member']['uid'], $max_total);
            }
        }
    }
    private function update_item_view($itemid, $shopid) {
        global $_W, $_GPC;
        $key = '_'.md5('superman_mall:item_view:'.$itemid);
        $value = 'yes';
        if (!isset($_GPC[$key]) || $_GPC[$key] != $value) {
            //更新商品浏览数数
            $data = array(
                'view_count' => 1,
            );
            $ret = M::t('superman_mall_item')->increment($data, array('id' => $itemid));
            if ($ret) {
                $expire_time = strtotime(date('Y-m-d 23:59:59')) - TIMESTAMP;
                $expire_time = $expire_time>=3600?$expire_time:3600;
                isetcookie($key, $value, $expire_time);
            }
            //更新统计数据(item_view)
            $id = M::t('superman_mall_stat')->init(array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $shopid,
                'daytime' => date('Ymd'),
            ));
            if ($id) {
                $data = array(
                    'item_view' => 1,
                );
                M::t('superman_mall_stat')->increment($data, array('id' => $id));
            }
        }
    }
    private function update_page_view($shopid) {
        global $_W;
        $id = M::t('superman_mall_stat')->init(array(
            'uniacid' => $_W['uniacid'],
            'shopid' => $shopid,
            'daytime' => date('Ymd'),
        ));
        if ($id) {
            $data = array(
                'page_view' => 1,
            );
            M::t('superman_mall_stat')->increment($data, array('id' => $id));
        }
    }
    private function update_unique_visitor($shopid) {
        global $_W, $_GPC;
        $key = '_'.md5('superman_mall:shop_uv:'.$shopid);
        $value = 'yes';
        if (!isset($_GPC[$key]) || $_GPC[$key] != $value) {
            $id = M::t('superman_mall_stat')->init(array(
                'uniacid' => $_W['uniacid'],
                'shopid' => $shopid,
                'daytime' => date('Ymd'),
            ));
            if ($id) {
                $data = array(
                    'unique_visitor' => 1,
                );
                $ret = M::t('superman_mall_stat')->increment($data, array('id' => $id));
                if ($ret) {
                    $expire_time = strtotime(date('Y-m-d 23:59:59')) - TIMESTAMP;
                    $expire_time = $expire_time>=3600?$expire_time:3600;
                    isetcookie($key, $value, $expire_time);
                }
            }
        }
    }
    private function update_item_share($itemid, $shopid) {
        global $_W;
        $data = array(
            'share_count' => 1,
        );
        M::t('superman_mall_item')->increment($data, array('id' => $itemid));
        //更新统计数据
        $id = M::t('superman_mall_stat')->init(array(
            'uniacid' => $_W['uniacid'],
            'shopid' => $shopid,
            'daytime' => date('Ymd'),
        ));
        if ($id) {
            $data = array(
                'item_share' => 1,
            );
            M::t('superman_mall_stat')->increment($data, array('id' => $id));
        }
    }
}
$obj = new Superman_mall_doMobileDetail;