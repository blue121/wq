<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileConfirm extends Superman {
    private $cart_list = array();
    private $shops = array();
    private $discount_setting = array();
    private $partner_setting = array();
    private $partner = array();
	public function __construct() {
		parent::__construct();
        parent::init();
        global $_W;
        if (isset($this->plugin_setting['discount']) && $this->plugin_setting['discount'] == 1) {
            $this->discount_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_DISCOUNT);
        }
        if (isset($this->plugin_setting['partner']) && $this->plugin_setting['partner'] == 1) {
            $this->partner_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PARTNER_SETTING);
            $this->partner = M::t('superman_mall_partner')->fetch(array(
                'uniacid' => $_W['uniacid'],
                'uid' => $_W['member']['uid']
            ));
        }
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '确认订单';
        $do = $do?$do:'confirm';
        $act = in_array($_GPC['act'], array('display', 'check'))?$_GPC['act']:'display';
        $this->checkauth();
        $referer = referer();
        if (strexists($referer, 'do=cart')) {
            $back_url = $this->createMobileUrl('cart');
        } else if (strexists($referer, 'do=detail')) {
            if (isset($_GPC['itemid'])) {
                $back_url = $this->createMobileUrl('detail', array('itemid' => intval($_GPC['itemid'])));
            }
        }
        if (!$_SESSION['confirm_check']) {
            $this->message('非法请求', $this->createMobileUrl('order', array('status' => 'no_pay')));
        }
        if ((empty($_W['fans']) || !$_W['fans']['follow'])) {
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);
            if ($setting && $setting['unsubscribe_order'] == 1) { //未关注不允许下单
                $subscribeurl = $setting['subscribeurl']?$setting['subscribeurl']:$_W['account']['subscribeurl'];
                $tips = $setting['subscribe_tips']?$setting['subscribe_tips']:'关注公众号，享受更好的购物体验！';
                if ($_W['isajax']) {
                    $this->json(ERRNO::NOT_LOGIN, $tips, array(
                        'url' => $subscribeurl,
                    ));
                } else {
                    $this->message($tips, $subscribeurl, 'info');
                }
            }
        }
        if ($act == 'display') {
            $this->check_item(false);
            //关注设置
            $subscribe_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);

            $credit_group = $this->get_credit_titles();
            //获取当前商户所有门店
            if (isset($_GPC['myfetch_list']) && $_GPC['myfetch_list'] == 1
                && isset($_GPC['shopid']) && $this->shops[$_GPC['shopid']]['myfetch_switch']) {
                $filter = array(
                    'shopid' => intval($_GPC['shopid']),
                    'isshow' => 1,
                );
                $myfetch_list = M::t('superman_mall_myfetch')->fetchall($filter, 'ORDER BY displayorder DESC', 0, -1);
                if ($myfetch_list) {
                    foreach ($myfetch_list as $v) {
                        $data['title'][] = $v['title'];
                        $data['address'][$v['title']] = $v['province'].' '.$v['city'].' '.$v['district'].' '.$v['address'].'<br>'.$v['username'].' '.$v['mobile'];
                        $data['myfetchid'][$v['title']] = $v['id'];
                    }
                    $this->json(ERRNO::OK, '', $data);
                }
            }
            $need_address = $this->check_need_address();
            $need_IDcard = $this->check_need_IDcard();
            if ($need_IDcard) {
                $row = M::t('superman_mall_member_field')->fetch(array(
                    'uid' => $_W['member']['uid']
                ));
                if ($row) {
                    $IDcard = $row['idcard'];
                    $hide_IDcard = SupermanUtil::hide_idcard($IDcard);
                }
            }
            if ($need_address) {
                //获取默认地址
                $address_url = $this->createMobileUrl('address', array(
                    'forward' => base64_encode($this->createMobileUrl('confirm', array(
                        'act' => 'display',
                        'itemid' => intval($_GPC['itemid']),
                        'total' => intval($_GPC['total']),
                        'skuid' => intval($_GPC['skuid']),
                    )))
                ));
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'isdefault' => 1
                );
                $address = M::t('mc_member_address')->fetch($filter);
                if ($address) {
                    $address['hide_mobile'] = SupermanUtil::hide_mobile($address['mobile']);
                }
                $alladdr = $address['province'].' '.$address['city'].' '.$address['district'].' '.$address['address'];
            }
            //总价格
            $allprice = 0;
            $alltotal = 0;
            foreach ($this->shops as $shop) {
                $alltotal += $shop['total'];
                $allprice += $shop['price'] + $shop['postage'];
                unset($shop);
            }

            if (checksubmit('submit')) {
                //TODO: 检查未支付订单，避免生成太多未支付订单
                //条件: order.uid=1 AND order.status=0 AND order.createtime > (TIMESTAMP-30*60) AND order.id=order_item.orderid AND order_item.itemid IN (1,2,3)
                //$this->check_nopay();
                $post_data = array(
                    'remark_arr' => $_GPC['remark'],
                    'myfetchid_arr' => $_GPC['myfetchid'],
                    'cash_credit_arr' => $_GPC['cash_credit'],
                    'alltotal' => $alltotal,
                    'IDcard' => $_GPC['idcard']?$_GPC['idcard']:(isset($IDcard)?$IDcard:''),
                );

                if ($need_IDcard) {
                    if (!$post_data['IDcard']) {
                        $this->json(ERRNO::IDCARD_NULL);
                    }
                    if (!preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $post_data['IDcard'])) {
                        $this->json(ERRNO::IDCARD_INVALID);
                    }
                    //更新身份证表信息
                    if (isset($IDcard)) {
                        M::t('superman_mall_member_field')->update(array(
                            'idcard' => $post_data['IDcard']
                        ), array(
                            'uid' => $_W['member']['uid']
                        ));
                    } else {
                        M::t('superman_mall_member_field')->insert(array(
                            'uid' => $_W['member']['uid'],
                            'idcard' => $post_data['IDcard'],
                        ));
                    }
                } else {
                    unset($post_data['IDcard']);
                }
                if ($need_address) {
                    //检查是否选择自提
                    if ($post_data['myfetchid_arr']) {
                        foreach ($post_data['myfetchid_arr'] as $v) {
                            if ($v <= 0) {
                                $myfetch = false;
                                break;
                            }
                        }
                    }
                    if (!$address && isset($myfetch) && !$myfetch) {
                        $this->json(ERRNO::ADDRESS_NOT_EXIST);
                    }
                    $post_data['username'] = $address['username'];
                    $post_data['mobile'] = $address['mobile'];
                    $post_data['zipcode'] = $address['zipcode'];
                    $post_data['address'] = $address['province'].' '.$address['city'].' '.$address['district'].' '.$address['address'];
                }
                $is_multi_order = count($post_data['remark_arr']) > 1?true:false;
                //获取分销上线信息
                $this->set_partner_upline();

                if ($is_multi_order) {  //多商户订单
                    $order = $this->create_multi_order($post_data);
                } else {    //单商户订单
                    $order = $this->create_single_order($post_data);
                    $this->create_mgroupon_order($order);
                }
                if ($is_multi_order) {
                    $shop_orders = M::t('superman_mall_order')->fetchall(array('pid' => $order['id']));
                    if ($shop_orders) {
                        //商户触发器
                        foreach ($shop_orders as $li) {
                            $extra_info = "\n\n==订单详情==\n";
                            $extra_info .= "订单号：{$li['ordersn']}\n";
                            $extra_info .= "金额：￥{$li['price']}\n";
                            $item_info = '';
                            foreach ($this->shops[$li['shopid']]['item'] as $item) {
                                if ($item_info != '') {
                                    $item_info .= '、';
                                }
                                $item_info .= "{$item['title']}(x{$item['total']})";
                            }
                            $extra_info .= "商品：{$item_info}";
                            $params = array(
                                'action' => 'order_submit',
                                'shopid' => $li['shopid'],
                                'extra_info' => $extra_info,
                                'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $li['id']))
                            );
                            Trigger::init('shop')->send($params);
                        }
                        //平台触发器
                        $extra_info = "\n\n==订单详情(".count($shop_orders).")==\n";
                        $shop_info = '';
                        foreach ($shop_orders as $li) {
                            if ($shop_info != '') {
                                $shop_info .= "\n--\n";
                            }
                            $shop_info .= "商户：{$this->shops[$li['shopid']]['title']}\n";
                            $shop_info .= "订单号：{$li['ordersn']}\n";
                            /*$shop_info .= "金额：￥{$li['price']}\n";
                            $item_info = '';
                            foreach ($this->shops[$li['shopid']]['item'] as $item) {
                                if ($item_info != '') {
                                    $item_info .= '、';
                                }
                                $item_info .= "{$item['title']}(x{$item['total']})";
                            }
                            $shop_info .= "商品：{$item_info}";*/
                        }
                        $extra_info .= $shop_info;
                        $params = array(
                            'action' => 'order_submit',
                            'uniacid' => $_W['uniacid'],
                            'extra_info' => $extra_info,
                            'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                        );
                        Trigger::init('platform')->send($params);
                    }
                } else {
                    //商户触发器
                    $extra_info = "\n\n==订单详情==\n";
                    $extra_info .= "订单号：{$order['ordersn']}\n";
                    $extra_info .= "金额：￥{$order['price']}\n";
                    $item_info = '';
                    foreach ($this->shops[$order['shopid']]['item'] as $item) {
                        if ($item_info != '') {
                            $item_info .= '、';
                        }
                        $item_info .= "{$item['title']}(x{$item['total']})";
                    }
                    $extra_info .= "商品：{$item_info}";
                    $params = array(
                        'action' => 'order_submit',
                        'shopid' => $order['shopid'],
                        'extra_info' => $extra_info,
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('shop')->send($params);
                    //平台触发器
                    $extra_info = "\n\n==订单详情==\n";
                    $extra_info .= "商户：{$this->shops[$order['shopid']]['title']}\n";
                    $extra_info .= "订单号：{$order['ordersn']}\n";
                    /*$extra_info .= "金额：￥{$order['price']}\n";
                    $item_info = '';
                    foreach ($this->shops[$order['shopid']]['item'] as $item) {
                        if ($item_info != '') {
                            $item_info .= '、';
                        }
                        $item_info .= "{$item['title']}(x{$item['total']})";
                    }
                    $extra_info .= "商品：{$item_info}";*/
                    $params = array(
                        'action' => 'order_submit',
                        'uniacid' => $_W['uniacid'],
                        'extra_info' => $extra_info,
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($params);
                }
                unset($_SESSION['confirm_check']);
                $this->order_print(SUPERMAN_CREATED_ORDER_PRINT, $order['id']);
                if (!$is_multi_order && $order['payu_rid'] > 0 && defined('SUPERMAN_CONNECT_BMPAYU')) {
                    $query = array(
                        'i' => $_W['uniacid'],
                        'c' => 'entry',
                        'tid' => $order['id'],
                        'title' => $item_info,
                        'fee' => $order['price'],
                        'ordersn' => $order['ordersn'],
                        'user' => $_W['fans']['openid'],
                        'rid' => $order['payu_rid'],
                        'ms' => 'superman_mall',
                        'do' => 'payex',
                        'm' => 'bm_payu',
                    );
                    $this->json(ERRNO::OK, '订单创建成功，跳转中...', array(
                        'url' => $_W['siteroot'].'app/index.php?'.http_build_query($query),
                    ));
                } else {
                    $this->json(ERRNO::OK, '订单创建成功，跳转中...', array('url' => $this->createMobileUrl('pay', array('act' =>'display', 'orderid' => $order['id']))));
                }
            }
        } else if ($act == 'check') {   //详情页兑换检查
            $this->check_item();
            $_SESSION['confirm_check'] = true;
        }
		include $this->template('confirm');
    }
    //获取分销商上线信息
    private function set_partner_upline() {
        global $_GPC, $_W;
        if (isset($this->partner_setting) && $this->partner_setting) {
            if ($this->partner) {     //分销商
                if ($this->partner['recommendid'] > 0
                    && isset($this->partner_setting['base']['level']) && $this->partner_setting['base']['level'] > 0) { //分销等级大于0
                    $num = ($this->partner['position']-1) > $this->partner_setting['base']['level']?$this->partner_setting['base']['level']:$this->partner['position']-1;
                    $remid = $this->partner['recommendid'];
                    //取出所有领佣上线
                    for ($i = 1; $i <= $num; $i++) {
                        if ($remid == 0) {
                            break;
                        }
                        $upline = M::t('superman_mall_partner')->fetch($remid);
                        $this->partner['upline'][$i] = array(
                            'id' => $upline['id'],
                            'groupid' => $upline['groupid'],
                            'status' => $upline['status'],
                            'recommendid' => $upline[$i]['recommendid'],
                        );
                        $remid = $upline['recommendid'];
                    }
                }
            } else {            //客户
                $__partnerid = intval($_GPC['__partnerid']);
                if ($__partnerid) {
                    //邀请人
                    $invite_partner = M::t('superman_mall_partner')->fetch($__partnerid);
                    if ($invite_partner && $invite_partner['uniacid'] == $_W['uniacid']
                        && $invite_partner['uid'] != $_W['member']['uid'] && $invite_partner['status'] == 1) {
                        $num = $invite_partner['position']> $this->partner_setting['base']['level']?$this->partner_setting['base']['level']:$invite_partner['position'];
                        $remid = $invite_partner['id'];
                        //取出所有领佣上线
                        for ($i = 1; $i <= $num; $i++) {
                            if ($remid == 0) {
                                break;
                            }
                            $upline = M::t('superman_mall_partner')->fetch($remid);
                            $this->partner['upline'][$i] = array(
                                'id' => $upline['id'],
                                'groupid' => $upline['groupid'],
                                'status' => $upline['status'],
                                'recommendid' => $upline['recommendid'],
                            );
                            $remid = $upline['recommendid'];
                        }
                    }
                }
            }
        }
    }
    /*
     * return order array
     *
     */
    private function create_multi_order($post_data) {
        global $_W;
        $order = array();
        //计算实际总价格
        $realprice = 0;
        $cash_credit = 0;
        $credit_type = $this->discount_setting['credit']['credit_type'];
        foreach ($post_data['myfetchid_arr'] as $shopid => $myfetchid) {
            if (intval($myfetchid) > 0) {
                $realprice += $this->shops[$shopid]['price'];
            } else {
                $realprice += $this->shops[$shopid]['price'] + $this->shops[$shopid]['postage'];
            }
            if ($post_data['cash_credit_arr'][$shopid] == 1) {   //选择抵现
                $realprice -= $this->shops[$shopid]['discount']['cash'];
                $cash_credit += $this->shops[$shopid]['discount']['cash_credit'];
            }
        }
        if ($realprice <= 0) {
            $this->json(ERRNO::ORDER_DATA_UNUSUAL);
        }
        //生成父订单
        $ordersn = $this->create_ordersn();
        $parent_order_data = array(
            'pid' => 0,     //父订单
            'shopid' => 0,  //父订单
            'uniacid' => $_W['uniacid'],
            'uid' => $_W['member']['uid'],
            'ordersn' => $ordersn,
            'total' => $post_data['alltotal'],
            'price' => $realprice,
            'username' => $post_data['username'],
            'mobile' => $post_data['mobile'],
            'zipcode' => $post_data['zipcode'],
            'address' => $post_data['address'],
            'status' => 0,        //支付状态：0，待支付；1，已支付。
            'extend' => '',
            'createtime' => TIMESTAMP,
            'cash_credit' => $cash_credit>0?$cash_credit:0,
            'credit_type' => $cash_credit>0&&$credit_type?$credit_type:'',
        );
        if ($cash_credit > 0 && $credit_type) {
            $cash_ret = mc_credit_update($_W['member']['uid'], $credit_type, -$cash_credit, array(
                $_W['member']['uid'],
                '订单抵现积分扣除',
                'superman_mall'
            ));
            if (is_error($cash_ret)) {
                $this->json(ERRNO::CREDIT_NOT_ENOUGH);
            }
        }
        $parent_orderid = M::t('superman_mall_order')->insert($parent_order_data);
        if ($parent_orderid <= 0) {
            $this->json(ERRNO::SYSTEM_ERROR);
        }
        $parent_order_data['id'] = $parent_orderid;
        //生成子订单
        foreach ($post_data['myfetchid_arr'] as $shopid => $myfetchid) {
            $ordersn = $this->create_ordersn();
            $child_order_data = array(
                'pid' => $parent_orderid,
                'shopid' => $shopid,
                'uniacid' => $_W['uniacid'],
                'remark' => substr($post_data['remark_arr'][$shopid], 0, 100),
                'uid' => $_W['member']['uid'],
                'ordersn' => $ordersn,
                'total' => $this->shops[$shopid]['total'],
                'price' => $this->shops[$shopid]['price'] + $this->shops[$shopid]['postage'],
                'username' => $post_data['username'],
                'mobile' => $post_data['mobile'],
                'zipcode' => $post_data['zipcode'],
                'address' => $post_data['address'],
                'express_fee' => $this->shops[$shopid]['postage'],
                'status' => 0,
                'createtime' => TIMESTAMP,
                'dispatch_type' => 1,
                'partner1_commission' => 0,
                'partner2_commission' => 0,
                'partner3_commission' => 0,
                'checkout' => isset($this->shops[$shopid]['checkout']) && $this->shops[$shopid]['checkout'] == 1?1:0,
            );
            $extend = array();
            //自提相关
            if ($myfetchid > 0) {
                $myfetch = M::t('superman_mall_myfetch')->fetch($myfetchid);
                if ($myfetch) {
                    $child_order_data['price'] = $this->shops[$shopid]['price'];
                    $child_order_data['express_fee'] = 0;
                    $extend['myfetch'] = array(
                        'title' => $myfetch['title'],
                        'address' => $myfetch['province'].' '.$myfetch['city'].' '.$myfetch['district'].' '.$myfetch['address'],
                        'username' => $myfetch['username'],
                        'mobile' => $myfetch['mobile'],
                    );

                    $child_order_data['dispatch_type'] = 2;
                    $child_order_data['address'] = '';
                } else {
                    $this->json(ERRNO::MYFETCH_CITY_ERROR);
                }
            }
            //清关身份证
            if ($post_data['IDcard']) {
                $extend['IDcard']['num'] = $post_data['IDcard'];
            }
            //营销相关
            if (isset($this->plugin_setting['discount']) && $this->plugin_setting['discount']) {
                $child_order_data['credit_type'] = $credit_type;
                if ($post_data['cash_credit_arr'][$shopid] == 1) {   //选择抵现
                    $child_order_data['price'] -= $this->shops[$shopid]['discount']['cash'];
                    $child_order_data['cash_credit'] = $this->shops[$shopid]['discount']['cash_credit'];
                }
                if (isset($this->discount_setting['credit']['remark_open']) && $this->discount_setting['credit']['remark_open'] == 1) { //开启返积分
                    if ($child_order_data['price'] > $this->discount_setting['credit']['min_order_amount']) {
                        $child_order_data['reward_credit'] = $this->discount_setting['credit']['remark_rate'] > 0 ? SupermanUtil::float_format($child_order_data['price']*$this->discount_setting['credit']['remark_rate']/100): $this->discount_setting['credit']['remark_value'];
                    }
                }
                if (isset($cash_ret) && !is_error($cash_ret) && $child_order_data['cash_credit'] > 0) {
                    $extend['discount_status']['cash_deduct'] = 1;
                }
                $extend['discount_info'] = array(
                    'free_ship' => isset($this->shops[$shopid]['discount']['free_ship'])?$this->shops[$shopid]['discount']['free_ship']:0,
                    'full_dec' => isset($this->shops[$shopid]['discount']['full_dec'])?$this->shops[$shopid]['discount']['full_dec']:array(),
                    'cash_credit' => (isset($this->shops[$shopid]['discount']['cash_credit']) && $this->shops[$shopid]['discount']['cash'] > 0)?array(
                        'cash' => $this->shops[$shopid]['discount']['cash'],
                        'credit' => $this->shops[$shopid]['discount']['cash_credit'],
                    ):array(),
                );
            }
            if ($child_order_data['price'] <= 0) {
                $this->json(ERRNO::ORDER_DATA_UNUSUAL);
            }
            //计算分销佣金
            if (isset($this->partner['upline']) && count($this->partner['upline']) > 0) {   //有上线
                $partner_permission = $this->check_plugin_permission('partner', $shopid);    //读取商户分销功能权限
                if (!is_error($partner_permission)) {
                    //计算订单佣金
                    $order_commission = $this->get_order_commission($this->shops[$shopid]['item']);
                    if (!empty($order_commission)) {
                        foreach ($this->partner['upline'] as $k => $p) {
                            $child_order_data['partner' . $k . '_id'] = $p['id'];
                        }
                        unset($k, $p);
                        foreach ($order_commission as $k => $c) {
                            $child_order_data['partner'.$k.'_commission'] = $c;
                        }
                        unset($k, $c);
                    }
                    if ($child_order_data['partner1_id'] > 0) {
                        M::t('superman_mall_partner')->increment(array('order_total' => 1), array('id' => $child_order_data['partner1_id']));
                    }
                }
            }

            $child_order_data['extend'] = $extend?iserializer($extend):'';
            $child_orderid = M::t('superman_mall_order')->insert($child_order_data);
            if ($child_orderid <= 0) {
                $this->json(ERRNO::SYSTEM_ERROR);
            }
            $child_order_data['id'] = $child_orderid;
            //生成子订单商品信息
            $path = SupermanUtil::attachment_path();
            foreach ($this->shops[$shopid]['item'] as $li) {
                $source = $li['cover'];
                $pos = strrpos($source, '.');
                $destination = substr($source, 0, $pos)."-{$child_orderid}-{$li['itemid']}-".TIMESTAMP.substr($source, $pos);
                $copy_result = @copy($path.$source, $path.$destination);
                $itemdata = array(
                    'orderid' => $child_orderid,
                    'itemid' => $li['itemid'],
                    'type' => $li['type'],
                    'title' => $li['title'],
                    'number' => $li['number'],
                    'cover' => $copy_result?$destination:$source,
                    'total' => $li['total'],
                    'price' => $li['price'],
                    'skuid' => $li['skuid'],
                    'special' => $li['special'],
                    'sku' => $li['attr'],
                    'iscomment' => 0,
                );
                if (isset($partner_permission) && !is_error($partner_permission)) {
                    $li['commission'] = $this->get_item_commission($li);//获取每件商品的佣金
                    if ($li['commission']) {
                        foreach ($li['commission'] as $k => $c) {
                            $itemdata['partner' . $k . '_commission'] = $c;
                        }
                        unset($k, $c);
                    }
                }
                M::t('superman_mall_order_item')->insert($itemdata);

                //提交订单后删除购物车中已选商品
                if ($li['checked'] == 1) {
                    M::t('superman_mall_cart')->delete(array(
                        'uniacid' => $_W['uniacid'],
                        'uid' => $_W['member']['uid'],
                        'itemid' => $li['itemid'],
                    ));
                }

                //拍下减库存的减库存
                if ($li['minus_total'] == 2) {
                    if ($li['skuid'] > 0) {
                        M::t('superman_mall_item_sku')->decrement(array('total' => $li['total']), array('id' => $li['skuid']));
                    }
                    M::t('superman_mall_item')->decrement(array('total' => $li['total']), array('id' => $li['itemid']));
                }
            }
            //订单提交成功，发送模板消息
            $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                    'act' => 'detail',
                    'orderid' => $child_orderid,
                ));
            $this->send_order_tmplmsg('submit', $child_order_data, $_W['openid'], $url);
        }
        return $parent_order_data;
    }
    private function create_single_order($post_data) {
        global $_W;
        $credit_type = $this->discount_setting['credit']['credit_type'];
        foreach ($post_data['myfetchid_arr'] as $shopid => $myfetchid) {
            $ordersn = $this->create_ordersn();
            $order_data = array(
                'pid' => 0,
                'shopid' => $shopid,
                'uniacid' => $_W['uniacid'],
                'remark' => $post_data['remark_arr'][$shopid]?substr($post_data['remark_arr'][$shopid], 0, 100):'',
                'uid' => $_W['member']['uid'],
                'ordersn' => $ordersn,
                'total' => $this->shops[$shopid]['total'],
                'price' => $this->shops[$shopid]['price'] + $this->shops[$shopid]['postage'],
                'username' => $post_data['username'],
                'mobile' => $post_data['mobile'],
                'zipcode' => $post_data['zipcode'],
                'address' => $post_data['address'],
                'express_fee' => $this->shops[$shopid]['postage'],
                'status' => 0,
                'createtime' => TIMESTAMP,
                'dispatch_type' => 1,
                'partner1_commission' => 0,
                'partner2_commission' => 0,
                'partner3_commission' => 0,
                'checkout' => isset($this->shops[$shopid]['checkout']) && $this->shops[$shopid]['checkout'] == 1?1:0,
            );
            if (isset($this->plugin_setting['bm_payu']) && $this->plugin_setting['bm_payu'] == 1) { //微信服务商
                $bm_payu_permission = $this->check_plugin_permission('bm_payu', $shopid);    //读取商户分销功能权限
                if (!is_error($bm_payu_permission) && $this->shops[$shopid]['payu_rid'] > 0) {
                    $order_data['payu_rid'] = $this->shops[$shopid]['payu_rid'];
                }
            }
            $extend = array();
            //自提相关
            if ($myfetchid > 0) {
                $myfetch = M::t('superman_mall_myfetch')->fetch($myfetchid);
                if ($myfetch) {
                    $order_data['price'] = $this->shops[$shopid]['price'];
                    $order_data['express_fee'] = 0;
                    $extend['myfetch'] = array(
                        'title' => $myfetch['title'],
                        'address' => $myfetch['province'].' '.$myfetch['city'].' '.$myfetch['district'].' '.$myfetch['address'],
                        'username' => $myfetch['username'],
                        'mobile' => $myfetch['mobile'],
                    );
                    $order_data['dispatch_type'] = 2;
                    $order_data['address'] = '';
                } else {
                    $this->json(ERRNO::MYFETCH_CITY_ERROR);
                }
            }
            if ($post_data['IDcard']) {
                $extend['IDcard']['num'] = $post_data['IDcard'];
            }
            //营销相关
            if (isset($this->plugin_setting['discount']) && $this->plugin_setting['discount']) {
                $order_data['credit_type'] = $credit_type;
                if ($post_data['cash_credit_arr'][$shopid] == 1) {   //选择抵现
                    $order_data['price'] -= $this->shops[$shopid]['discount']['cash'];
                    $order_data['cash_credit'] = $this->shops[$shopid]['discount']['cash_credit'];
                    if ($order_data['cash_credit'] > 0 && $credit_type) {
                        $cash_ret = mc_credit_update($_W['member']['uid'], $credit_type, -$order_data['cash_credit'], array(
                            $_W['member']['uid'],
                            '订单抵现积分扣除',
                            'superman_mall'
                        ));
                        if (is_error($cash_ret)) {
                            $this->json(ERRNO::CREDIT_NOT_ENOUGH);
                        }
                        $extend['discount_status']['cash_deduct'] = 1;
                    }
                }
                if (isset($this->discount_setting['credit']['remark_open']) && $this->discount_setting['credit']['remark_open'] == 1) { //开启返积分
                    if ($order_data['price'] > $this->discount_setting['credit']['min_order_amount']) {
                        $order_data['reward_credit'] = $this->discount_setting['credit']['remark_rate'] > 0 ? SupermanUtil::float_format($order_data['price']*$this->discount_setting['credit']['remark_rate']/100): $this->discount_setting['credit']['remark_value'];
                    }
                }
                //记录订单享受的营销优惠活动
                $extend['discount_info'] = array(
                    'free_ship' => isset($this->shops[$shopid]['discount']['free_ship'])?$this->shops[$shopid]['discount']['free_ship']:0,
                    'full_dec' => isset($this->shops[$shopid]['discount']['full_dec'])?$this->shops[$shopid]['discount']['full_dec']:array(),
                    'cash_credit' => ($post_data['cash_credit_arr'][$shopid] == 1 && isset($this->shops[$shopid]['discount']['cash_credit']) && $this->shops[$shopid]['discount']['cash'] > 0)?array(
                        'cash' => $this->shops[$shopid]['discount']['cash'],
                        'credit' => $this->shops[$shopid]['discount']['cash_credit'],
                    ):array(),
                );
                if (isset($this->shops[$shopid]['discount']['reduction'])) {
                    $extend['discount_info']['reduction'] = $this->shops[$shopid]['discount']['reduction'];
                }
            }
            if ($order_data['price'] < 0) {
                $this->json(ERRNO::ORDER_DATA_UNUSUAL);
            }

            //计算分销佣金
            if (isset($this->partner['upline']) && count($this->partner['upline']) > 0) {   //有上线
                $partner_permission = $this->check_plugin_permission('partner', $shopid);    //读取商户分销功能权限
                if (!is_error($partner_permission)) {
                    //计算订单佣金
                    $order_commission = $this->get_order_commission($this->shops[$shopid]['item']);
                    if (!empty($order_commission)) {
                        foreach ($this->partner['upline'] as $k => $p) {
                            $order_data['partner' . $k . '_id'] = $p['id'];
                        }
                        unset($k, $p);
                        foreach ($order_commission as $k => $c) {
                            $order_data['partner'.$k.'_commission'] = $c;
                        }
                        unset($k, $c);
                    }
                    if ($order_data['partner1_id'] > 0) {
                        M::t('superman_mall_partner')->increment(array('order_total' => 1), array('id' => $order_data['partner1_id']));
                    }
                }
            }

            $order_data['extend'] = $extend?iserializer($extend):'';
            $orderid = M::t('superman_mall_order')->insert($order_data);
            if ($orderid <= 0) {
                $this->json(ERRNO::SYSTEM_ERROR);
            }
            $order_data['id'] = $orderid;
            $path = SupermanUtil::attachment_path();
            foreach ($this->shops[$shopid]['item'] as $li) {
                if ($li['special'] == 2) {
                    $order_data['is_mgroupon'] = true;
                }
                $source = $li['cover'];
                $pos = strrpos($source, '.');
                $destination = substr($source, 0, $pos)."-{$orderid}-{$li['itemid']}-".TIMESTAMP.substr($source, $pos);
                $copy_result = @copy($path.$source, $path.$destination);
                $itemdata = array(
                    'orderid' => $orderid,
                    'itemid' => $li['itemid'],
                    'type' => $li['type'],
                    'title' => $li['title'],
                    'number' => $li['number'],
                    'cover' => $copy_result?$destination:$source,
                    'total' => $li['total'],
                    'price' => $li['price'],
                    'skuid' => $li['skuid'],
                    'special' => $li['special'],
                    'sku' => $li['attr'],
                    'iscomment' => 0,
                );

                if (isset($partner_permission) && !is_error($partner_permission)) {
                    $li['commission'] = $this->get_item_commission($li);//获取每件商品的佣金
                    if ($li['commission']) {
                        foreach ($li['commission'] as $k => $c) {
                            $itemdata['partner' . $k . '_commission'] = $c;
                        }
                        unset($k, $c);
                    }
                }
                M::t('superman_mall_order_item')->insert($itemdata);

                //提交订单后删除购物车中已选商品
                if ($li['checked'] == 1) {
                    M::t('superman_mall_cart')->delete(array(
                        'uniacid' => $_W['uniacid'],
                        'uid' => $_W['member']['uid'],
                        'itemid' => $li['itemid'],
                    ));
                }

                //拍下减库存的减库存
                if ($li['minus_total'] == 2) {
                    if ($li['skuid'] > 0) {
                        M::t('superman_mall_item_sku')->decrement(array('total' => $li['total']), array('id' => $li['skuid']));
                    }
                    M::t('superman_mall_item')->decrement(array('total' => $li['total']), array('id' => $li['itemid']));
                }
            }
            //订单提交成功，发送模板消息
            $url = $_W['siteroot'].'app/'.$this->createMobileUrl('order', array(
                    'act' => 'detail',
                    'orderid' => $orderid,
                ));
            $this->send_order_tmplmsg('submit', $order_data, $_W['openid'], $url);
        }
        return $order_data;
    }
    private function create_mgroupon_order($order) {
        global $_W, $_GPC;
        //记录拼团订单
        if ($order['is_mgroupon'] && isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) {
            $mgroupon_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, $order['shopid']);
            if ($mgroupon_setting) {
                //拼团订单更新订单状态
                M::t('superman_mall_order')->update(array('type' => 1), array('id' => $order['id']));
                //检查拼团状态
                $allow = true;
                $mgroupon_id = intval($_GPC['__mgroupon_id'])>0?intval($_GPC['__mgroupon_id']):0;
                while ($mgroupon_id > 0) {  //参团
                    $mgroupon = M::t('superman_mall_merge_groupon')->fetch($mgroupon_id);
                    if ($mgroupon) {
                        //不能参加自己的团
                        if ($mgroupon['uid'] == $_W['member']['uid']) {
                            isetcookie('__mgroupon_id', 0, -1);
                            $allow = true;
                            $mgroupon_id = 0;
                            if ($this->mgroupon_log) {
                                WeUtility::logging('trace', '[confirm] 用户不能参加自己的拼团, mgroupon='.var_export($mgroupon, true));
                            }
                            break;
                        }

                        //不同商户拼团商品不能组团，将发起新团
                        if ($mgroupon['shopid'] != $order['shopid']) {
                            isetcookie('__mgroupon_id', 0, -1);
                            $allow = true;
                            $mgroupon_id = 0;
                            if ($this->mgroupon_log) {
                                WeUtility::logging('trace', '[confirm] 不同商户拼团商品不能拼团,可以重新发起新团, mgroupon='.var_export($mgroupon, true));
                            }
                            break;
                        }

                        //已过期拼团，可以重新发起新团
                        if ($mgroupon['expiretime'] < TIMESTAMP) {
                            isetcookie('__mgroupon_id', 0, -1);
                            $allow = true;
                            $mgroupon_id = 0;
                            if ($this->mgroupon_log) {
                                WeUtility::logging('trace', '[confirm] 已过期拼团,可以重新发起新团, mgroupon='.var_export($mgroupon, true));
                            }
                            break;
                        }

                        //检查拼团人数，已满时无法参加
                        $filter = array(
                            'mgid' => $mgroupon_id,
                            'pay_status' => 1,
                        );
                        $total = M::t('superman_mall_merge_groupon')->count($filter);
                        if ($total+1 >= $mgroupon['limit']) { //total+1需要计算团长
                            isetcookie('__mgroupon_id', 0, -1);
                            $allow = true;
                            $mgroupon_id = 0;
                            if ($this->mgroupon_log) {
                                WeUtility::logging('trace', '[confirm] 无法参团: 拼团人数已满, mgroupon=' . var_export($mgroupon, true));
                            }
                            break;                        }

                        //检查是否有未支付团
                        $sql = "SELECT a.* FROM ".tablename('superman_mall_order')." AS a,".tablename('superman_mall_merge_groupon')." AS b";
                        $sql .= " WHERE a.id=b.orderid AND a.uid=:uid AND b.mgid=:mgid AND a.status IN (0,1)";
                        $params = array(
                            ':uid' => $_W['member']['uid'],
                            ':mgid' => $mgroupon_id,
                        );
                        $joined = pdo_fetchall($sql, $params);
//                        WeUtility::logging('trace', 'tttttt$joined='.var_export($joined, true));
                        //检查是否参加过该团
//                        $filter = array(
//                            'uid' => $_W['member']['uid'],
//                            'mgid' => $mgroupon_id,
//                            'pay_status' => 1,
//                        );
//                        $row = M::t('superman_mall_merge_groupon')->fetch($filter);
                        if ($joined) {
                            isetcookie('__mgroupon_id', 0, -1);
                            $allow = true;
                            $mgroupon_id = 0;
                            if ($this->mgroupon_log) {
                                WeUtility::logging('trace', '[confirm] 无法参团: 已参加该拼团, mgroupon=' . var_export($mgroupon, true));
                            }
                            break;
                        }
                    }
                    break;
                }
                if ($allow) {
                    $expiretime = $mgroupon_setting['expire']?TIMESTAMP+$mgroupon_setting['expire']*3600:24*3600; //默认24小时
                    //商品成团人数
                    $limit = 0;
                    if (!$mgroupon_id) { //只有发起人（团长）记录拼团人数
                        $limit = 2;
                        $order_item = M::t('superman_mall_order_item')->fetch(array('orderid' => $order['id']));
                        if ($order_item) {
                            $item = M::t('superman_mall_item')->fetch($order_item['itemid']);
                            if ($item) {
                                $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
                                if (isset($item['extend']['multi_member_limit']) && $item['extend']['multi_member_limit'] > 0) {
                                    $limit = $item['extend']['multi_member_limit'];
                                }
                            }
                        }
                    }
                    $mgroupon_data = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $order['shopid'],
                        'mgid' => $mgroupon_id,
                        'uid' => $_W['member']['uid'],
                        'orderid' => $order['id'],
                        'ordersn' => $order['ordersn'],
                        'limit' => $limit,
                        'pay_status' => 0,
                        'expiretime' => $mgroupon_id?0:$expiretime,
                        'createtime' => TIMESTAMP,
                    );
                    $new_id = M::t('superman_mall_merge_groupon')->insert($mgroupon_data);
                    if (!$new_id) {
                        WeUtility::logging('fatal', '发起拼团失败: insert `superman_mall_merge_groupon` failed, mgroupon_data='.var_export($mgroupon_data, true));
                    } else {
                        if ($this->mgroupon_log) {
                            WeUtility::logging('trace', ($mgroupon_id ? '参团成功' : '发起拼团成功') . ', mgroupon_data=' . var_export($mgroupon_data, true));
                        }
                    }
                }
            }
        }
    }

    private function get_item_commission($item) {
        $arr = array();
        if ($item['partner_open']) {
            $item_partner_stat = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $item['itemid']));
            foreach ($this->partner['upline'] as $k => &$p) {
                if ($item_partner_stat['commission_custom']) {  //商品自定义佣金
                    $item_commission = SupermanUtil::float_format(($item_partner_stat['commission' . $k . '_rate'] > 0 ? $item_partner_stat['commission' . $k . '_rate'] / 100 * $item['price'] : $item_partner_stat['commission' . $k . '_value']) * $item['total']);
                } else {
                    $pg = M::t('superman_mall_partner_group')->fetch($p['groupid']);
                    $item_commission = SupermanUtil::float_format($pg['rate' . $k] / 100 * $item['price'] * $item['total']);
                }
                $arr[$k] = $item_commission;
            }
        }
        return $arr;
    }
    
    private function get_order_commission($items) {
        $arr = array();
        foreach ($items as $li) {
            if ($li['partner_open']) {
                $item_partner_stat = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $li['itemid']));
                foreach ($this->partner['upline'] as $k => &$p) {
                    if ($item_partner_stat['commission_custom']) {  //商品自定义佣金
                        $item_commission = SupermanUtil::float_format(($item_partner_stat['commission' . $k . '_rate'] > 0 ? $item_partner_stat['commission' . $k . '_rate'] / 100 * $li['price'] : $item_partner_stat['commission' . $k . '_value']) * $li['total']);
                    } else {        //按分销等级计算佣金
                        $pg = M::t('superman_mall_partner_group')->fetch($p['groupid']);
                        $item_commission = SupermanUtil::float_format($pg['rate' . $k] / 100 * $li['price'] * $li['total']);
                    }
                    if (!isset($arr[$k])) {
                        $arr[$k] = 0;
                    }
                    $arr[$k] += $item_commission;
                }
            }
        }
        return $arr;
    }

    private function set_cart_list() {
        global $_W, $_GPC;
        if (isset($_GPC['itemid']) && intval($_GPC['itemid']) > 0
            && isset($_GPC['total']) && intval($_GPC['total']) > 0) { //立即购买
            $this->cart_list = array(
                array(
                    'itemid' => intval($_GPC['itemid']),
                    'skuid' => intval($_GPC['skuid']),  //可能为0
                    'total' => intval($_GPC['total']),
                    'single' => intval($_GPC['single'])
                )
            );
        } else {    //购物车下单
            $filter = array(
                'uid' => $_W['member']['uid'],
                'checked' => 1
            );
            $this->cart_list = M::t('superman_mall_cart')->fetchall($filter);
        }
        if (!$this->cart_list) {
            $this->json(ERRNO::CART_NO_ITEM);
        }
    }

    /**
     * @param bool $exit
     * @return int
     */
    private function check_item($exit = true) {
        global $_W;
        $this->set_cart_list();
        foreach ($this->cart_list as &$li) {
            //查出每件商品信息
            $item = M::t('superman_mall_item')->fetch($li['itemid']);
            //商品不存在
            if (!$item) {
                $this->json(ERRNO::ITEM_NOT_FOUND);
            }
            //商品已下架
            if (!in_array($item['status'], array(1, 3))) {
                $this->json(ERRNO::ITEM_OFFLINE);
            }
            $item['extend'] = $item['extend']?iunserializer($item['extend']):array();
            //特殊商品
            if (in_array($item['special'], array('1', '2'))) {
                //不在可秒杀时间段内
                if ($item['special'] == 1) {
                    if ($item['starttime'] > TIMESTAMP || $item['endtime'] < TIMESTAMP) {
                        $this->json(ERRNO::ITEM_NOT_INTIME);
                    } else {
                        $seckill_time = SupermanUtil::get_seckill_time();
                        if ($seckill_time != $item['seckill_time']) {
                            $this->json(ERRNO::ITEM_NOT_INTIME);
                        }
                    }
                }
                //拼团商品
                if ($item['special'] == 2) {
                    //判断平台拼团模块开关和商户拼团模块开关
                    if (!isset($this->plugin_setting['mgroupon']) || !$this->plugin_setting['mgroupon']) {
                        $this->json(ERRNO::SYSTEM_ERROR, '拼团功能未开启');
                    }
                    $mgroupon_permission = $this->check_plugin_permission('mgroupon', $item['shopid']);
                    if (is_error($mgroupon_permission)) {
                        $this->json(ERRNO::SYSTEM_ERROR, '该商户拼团功能未开启');
                    }
                    //单人购买
                    if ($li['single'] == 1) {
                        $item['price'] = $item['extend']['single_price']?$item['extend']['single_price']:$item['market_price'];
                        $item['special'] = 0;
                    }

                }
                //购买限制检查
                if ($item['extend']['other_attr']['order_buy_num'] > 0) { //每订单可买
                    if ($li['total'] > intval($item['extend']['other_attr']['order_buy_num'])) {
                        $this->json(ERRNO::ITEM_OVER_LIMIT, '商品【'.$item['title'].'】超过该订单可购买数量'.intval($item['extend']['other_attr']['order_buy_num']).'件');
                    }
                }
                if ($item['extend']['other_attr']['max_buy_num'] > 0) { //每帐号可买
                    $sql = 'SELECT SUM(a.total) FROM '.tablename('superman_mall_order_item').' AS a,'.tablename('superman_mall_order').' AS b ';
                    $sql .= ' WHERE a.orderid=b.id AND b.uid=:uid AND a.itemid=:itemid AND b.status>0';
                    $params = array(
                        ':uid' => $_W['member']['uid'],
                        ':itemid' => $li['itemid'],
                    );
                    $buy_sum = pdo_fetchcolumn($sql, $params);
                    if (($buy_sum + $li['total']) > $item['extend']['other_attr']['max_buy_num']) {
                        $this->json(ERRNO::ITEM_OVER_LIMIT, '商品【'.$item['title'].'】超过可购买数量'.intval($item['extend']['other_attr']['max_buy_num']).'件');
                    }
                }
            }

            //根据商品规格检查库存
            if ($li['skuid'] > 0) {
                $sku = M::t('superman_mall_item_sku')->fetch($li['skuid']);
                //sku不存在
                if (!$sku) {
                    $this->json(ERRNO::SKU_NOT_EXIST);
                }
                //库存为0或购买数量超过库存
                if ($sku['total'] <= 0 || $sku['total'] - $li['total'] < 0) {
                    if ($item['special'] == 1) { //秒杀
                        $this->json(ERRNO::ITEM_SECKILL_NOT_TOTAL);
                    } else {
                        $this->json(ERRNO::SKU_NOT_TOTAL);
                    }
                }
                //商品规格数据
                $valueids = explode(',', $sku['valueids']);
                if (is_array($valueids)) {
                    $values = M::t('superman_mall_item_attr')->fetchall_by_valueids($valueids);
                    if ($values) {
                        foreach ($values as $v) {
                            $li['attr'][] = $v['title'].':'.$v['value'];
                        }
                        $li['attr'] = implode(' ', $li['attr']);
                    }
                }
                $li['price'] = $sku['price'];
            } else {
                //库存为0或购买数量超过库存
                if ($item['total'] <= 0 || $item['total'] - $li['total'] < 0) {
                    if ($item['special'] == 1) { //秒杀
                        $this->json(ERRNO::ITEM_SECKILL_NOT_TOTAL);
                    } else {
                        $this->json(ERRNO::SKU_NOT_TOTAL);
                    }
                }
                $li['price'] = $item['price'];
            }

            //分销内部价
            $this->partner_inner_price($li['price'], $item);
            //查询商户信息
            $shop = M::t('superman_mall_shop')->fetch($item['shopid']);
            if (!$shop) {
                $this->json(ERRNO::SHOP_NOT_FOUND);
            }
            //查询自提开关
            $myfetch_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MYFETCH_SETTING, $item['shopid']);

            //按商户拆分商品
            $this->shops[$item['shopid']]['myfetch_switch'] = isset($myfetch_setting)&&$myfetch_setting['open']==1?true:false;
            $this->shops[$item['shopid']]['itemids'][] = $item['id'];
            $this->shops[$item['shopid']]['title'] = $shop['title'];
            $this->shops[$item['shopid']]['payu_rid'] = $shop['payu_rid'];

            //可核销
            if ($item['ischeckout'] == 1) {
                $this->shops[$item['shopid']]['checkout'] = 1;
            }
            $this->shops[$item['shopid']]['item'][] = array(
                'itemid' => $item['id'],
                'special' => $item['special'],
                'title' => $item['title'],
                'type' => $item['type'],
                'number' => $item['number'],
                'total' => $li['total'],
                'skuid' => $li['skuid'],
                'price' => $li['price'],
                'cover' => $item['cover'],
                'cash_credit' => $item['cash_credit'],
                'minus_total' => $item['minus_total'],
                'attr' => $li['attr'],
                'commission' => isset($item_commission)?$item_commission:array(),
                'checked' => $li['checked'], //判断是否是从购物车过来的
                'customs_clearance' => $item['customs_clearance'],
                'delivery_mode' => $item['delivery_mode'],
                'postage_tmplid' => $item['postage_tmplid'],
                'postage' => floatval($item['postage']),
                'weight' => (isset($sku) && $sku)?$sku['weight']:$item['weight'],
                'partner_open' => $item['partner_open'],
            );
            $this->shops[$item['shopid']]['price'] += $li['price']*$li['total'];
            $this->shops[$item['shopid']]['total'] += $li['total'];
        }
        unset($li, $item, $sku);

        $this->check_delivery_mode();

        $this->set_shop_postage();

        //营销相关
        if (isset($this->plugin_setting['discount']) && $this->plugin_setting['discount']) {
            //如果开启了积分抵现
            if (isset($this->discount_setting['credit']['cash_open']) && $this->discount_setting['credit']['cash_open'] == 1) {
                $member_credit = $_W['member'][$this->discount_setting['credit']['credit_type']];
            }
            foreach ($this->shops as $shopid => &$shop) {
                $discount_permission = $this->check_plugin_permission('discount', $shopid);
                if (!is_error($discount_permission)) {
                    //包邮
                    $free_ship = $this->check_free_ship($shop['item'], $shopid);
                    if ($free_ship === true) {
                        $shop['postage'] = 0;
                        $shop['discount']['free_ship'] = 1;
                    }
                    //限时打折
                    foreach ($shop['item'] as $k => $item) {
                        $item_activity = $this->set_reduction_price($item, $shopid);
                        if (isset($item_activity['ai_extend']['value'])) {
                            $shop['item'][$k]['price'] = SupermanUtil::float_format($item['price'] * $item_activity['ai_extend']['value']/10);
                            $shop['discount']['reduction'] = $item_activity['ai_extend']['value'];
                        }
                    }
                    //满减
                    $full_minus = $this->set_full_minus($shop['item'], $shopid);
                    $shop['price'] = $full_minus['price'];
                    if (isset($full_minus['activity']) && $full_minus['activity']) {
                        $shop['discount']['full_dec'] = $full_minus['activity'];
                    }

                    //计算抵现积分
                    $cash_credit = 0;
                    foreach ($shop['item'] as $item) {//计算当前商户所有商品需要的积分数
                        $cash_credit += $item['cash_credit'] * $item['total'];
                    }
                    //可以积分抵现
                    if ($cash_credit > 0 && isset($member_credit) && $member_credit > 0) {
                        //抵现足一分并且不大于订单金额
                        if ($cash_credit/$this->discount_setting['credit']['cash_rate'] >= 0.01 && $cash_credit/$this->discount_setting['credit']['cash_rate'] <= $shop['price']) {
                            //抵现积分大于用户积分
                            if ($cash_credit >= $member_credit) {
                                $shop['discount']['cash_credit'] = $member_credit;
                                $member_credit = 0;
                            } else {
                                $shop['discount']['cash_credit'] = $cash_credit;
                                $member_credit -= $cash_credit;
                            }
                            //计算实际抵现金额
                            $shop['discount']['cash'] = SupermanUtil::float_format($shop['discount']['cash_credit']/$this->discount_setting['credit']['cash_rate']);
                        }
                    }
                }
            }
            unset($shop, $shopid);
        }

        if ($exit) {
            $this->json(ERRNO::OK, '跳转中...', array(
                'url' => $this->createMobileUrl('confirm', array('__t' => TIMESTAMP)),
            ));
        } else {
            return ERRNO::OK;
        }
    }

    //计算分销内部价
    private function partner_inner_price(&$price, $item = array()) {

        if (isset($this->partner) && $this->partner['status'] == 1) {
            $partner_permission = $this->check_plugin_permission('partner', $item['shopid']);    //商户分销功能开关
            if (!is_error($partner_permission) && $item['partner_open']) {
                $item_partner_stat = M::t('superman_mall_item_partner_attr')->fetch(array('itemid' => $item['id']));
                $price = SupermanUtil::float_format($item_partner_stat['discount_rate'] > 0 ? $item_partner_stat['discount_rate'] / 100 * $price : $price - $item_partner_stat['discount_value']);
            }
        }
    }

    //计算满减活动
    private function set_full_minus($items, $shopid) {
        global $_W;
        $item_total = $item_price = 0;
        //计算商品总件数和总价格（不包含邮费）
        foreach ($items as $item) {
            $item_total += $item['total'];
            $item_price += $item['price'] * $item['total']; //商品总价
        }
        $arr = array(
            'price' => $item_price,
            'activity' => array()
        );
        //全店满减活动
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'shopid' => $shopid,
            'type' => 3,
            'starttime' => '# <' . TIMESTAMP,
            'endtime' => '# >' . TIMESTAMP,
            'isglobal' => 1,
        );
        $activitys = M::t('superman_mall_shop_activity')->fetchall($filter, '', 0, -1);
        if ($activitys) {
            //循环每个活动
            foreach ($activitys as $activity) {
                $extend = $activity['extend'] ? iunserializer($activity['extend']) : array();
                if (!empty($extend)) {
                    //循环活动中每项优惠
                    foreach ($extend as $aty) {
                        if ($aty['full']['unit'] == 'yuan') {   //满元
                            $full_value = $item_price;
                        } else {    //满件
                            $full_value = $item_total;
                        }
                        if ($full_value >= $aty['full']['value']) {  //满足满减条件
                            //计算满减后价格
                            if ($aty['minus']['unit'] == 'yuan') {  //减元
                                $price = $item_price - $aty['minus']['value'];
                            } else {    //减折
                                $price = $item_price * $aty['minus']['value'] / 10;
                            }
                            $aty['minus']['money'] = SupermanUtil::float_format($item_price - $price);
                            //比较满减后价格是否是当前活动中最低价
                            if ($price < $arr['price']) {
                                $arr['price'] = $price;
                                $aty['title'] = $activity['title'];
                                $arr['activity'] = $aty;
                            }
                        }
                    }
                }
            }
            unset($full_value, $activity);
        }
        //指定商品满减活动
        $sql = "SELECT a.* FROM " . tablename('superman_mall_shop_activity') . " AS a," . tablename('superman_mall_shop_activity_item') . " AS i";
        $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=3 AND a.starttime<:starttime AND a.endtime>:endtime AND i.itemid in (".implode(",", $this->shops[$shopid]['itemids']) . ")";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':shopid' => $shopid,
            ':starttime' => TIMESTAMP,
            ':endtime' => TIMESTAMP,
        );
        $activitys = pdo_fetchall($sql, $params);
        if ($activitys) {
            //循环每个活动
            foreach ($activitys as $activity) {
                $extend = $activity['extend'] ? iunserializer($activity['extend']) : array();
                if (!empty($extend)) {
                    //循环活动中每项优惠
                    foreach ($extend as $aty) {
                        if ($aty['full']['unit'] == 'yuan') {   //满元
                            $full_value = $item_price;
                        } else {    //满件
                            $full_value = $item_total;
                        }
                        if ($full_value >= $aty['full']['value']) {  //满足满减条件
                            if ($aty['minus']['unit'] == 'yuan') {  //减元
                                $price = $item_price - $aty['minus']['value'];
                            } else {    //减折
                                $price = $item_price * $aty['minus']['value'] / 10;
                            }
                            $aty['minus']['money'] = SupermanUtil::float_format($item_price - $price);
                            if ($price < $arr['price']) {  //满减后价格低于当前活动中最低价格
                                $arr['price'] = $price;
                                $aty['title'] = $activity['title'];
                                $arr['activity'] = $aty;
                            }
                        }
                    }
                }
            }
            unset($full_value, $activity);
        }
        if ($arr['activity']) {
            $title = '满'.$arr['activity']['full']['value'];
            $title .= $arr['activity']['full']['unit'] == 'yuan'?'元':'件';
            $title .= $arr['activity']['minus']['unit'] == 'yuan'?'减'.$arr['activity']['minus']['value'].'元':'打'.$arr['activity']['minus']['value'].'折';
            $arr['activity']['content'] = $title;
        }
        /*return array(
            '' =>
            'price' => ,
            'activity' => array (
                'content' => 拼接的活动内容
                'title' => '',活动标题
                'full' => array(    最优惠满减项目详情
                    'value' => '',
                    'unit' => '',
                ),
                'minus' => array(
                    'value' => '',
                    'unit' => '',
        ),),);*/
        return $arr;
    }

    //计算商品限时折扣
    private function set_reduction_price($item, $shopid) {
        global $_W;
        $low_price = $item['price'];
        $active = array();
        $sql = "SELECT a.*,i.extend AS ai_extend FROM " . tablename('superman_mall_shop_activity') . " AS a," . tablename('superman_mall_shop_activity_item') . " AS i";
        $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=2 AND a.starttime<:starttime AND a.endtime>:endtime AND i.itemid=:itemid";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':shopid' => $shopid,
            ':starttime' => TIMESTAMP,
            ':endtime' => TIMESTAMP,
            ':itemid' => $item['itemid']
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
            if (isset($active['extend'])) {
                //检查限购
                $active['extend'] = $active['extend']?iunserializer($active['extend']):array();
                if (isset($active['extend']['quota']) && $active['extend']['quota'] > 0) {
                    $sql = 'SELECT SUM(a.total) FROM '.tablename('superman_mall_order_item').' AS a,'.tablename('superman_mall_order').' AS b ';
                    $sql .= ' WHERE a.orderid=b.id AND b.uid=:uid AND a.itemid=:itemid AND b.status>0';
                    $params = array(
                        ':uid' => $_W['member']['uid'],
                        ':itemid' => $item['itemid']
                    );
                    $buy_sum = pdo_fetchcolumn($sql, $params);
                    if ($buy_sum + $item['total'] > $active['extend']['quota']) {
                        $this->json(ERRNO::ITEM_OVER_LIMIT);
                    }
                }
            }
        }
        return $active;
    }

    //检查是否包邮
    private function check_free_ship($items, $shopid) {
        global $_W, $_GPC;
        $item_total = $item_price = 0;
        foreach ($items as $item) {
            $item_total += $item['total'];
            $item_price += $item['price'] * $item['total'];
        }
        //全店包邮活动
        $filter = array(
            'uniacid' => $_W['uniacid'],
            'shopid' => $shopid,
            'type' => 1,
            'starttime' => '# <' . TIMESTAMP,
            'endtime' => '# >' . TIMESTAMP,
            'isglobal' => 1,
        );
        $activitys = M::t('superman_mall_shop_activity')->fetchall($filter, '', 0, -1);
        if ($activitys) {
            foreach ($activitys as $activity) {
                $extend = $activity['extend'] ? iunserializer($activity['extend']) : array();
                if (!empty($extend)) {
                    foreach ($extend as $aty) {
                        //单位:yuan元,piece件
                        if ($aty['unit'] == 'yuan') {
                            if ($aty['value'] <= $item_price) {
                                return true;
                            }
                        } else if ($aty['unit'] == 'piece') {
                            if ($aty['value'] <= $item_total) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        //指定商品包邮活动
        $sql = "SELECT a.* FROM " . tablename('superman_mall_shop_activity') . " AS a," . tablename('superman_mall_shop_activity_item') . " AS i";
        $sql .= " WHERE a.id=i.activityid AND a.uniacid=:uniacid AND a.shopid=:shopid AND a.type=1 AND a.starttime<:starttime AND a.endtime>:endtime AND i.itemid in (" . implode(",", $this->shops[$shopid]['itemids']) . ")";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':shopid' => $shopid,
            ':starttime' => TIMESTAMP,
            ':endtime' => TIMESTAMP,
        );
        $activitys = pdo_fetchall($sql, $params);
        if ($activitys) {
            foreach ($activitys as $activity) {
                $extend = $activity['extend'] ? iunserializer($activity['extend']) : array();
                if (!empty($extend)) {
                    foreach ($extend as $aty) {
                        //单位:yuan元,piece件
                        if ($aty['unit'] == 'yuan') {
                            if ($aty['value'] <= $item_price) {
                                return true;
                            }
                        } else if ($aty['unit'] == 'piece') {
                            if ($aty['value'] <= $item_total) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    //计算订单邮费
    private function set_shop_postage() {
        global $_W, $_GPC;
        $postage_tmpl = array();
        //取出默认地址
        $filter = array(
            'uid' => $_W['member']['uid'],
            'isdefault' => 1
        );
        $address = M::t('mc_member_address')->fetch($filter);
        if ($address) {
            foreach ($this->shops as $shopid => $shop) {
                //包含快递配送方式
                if ($shop['delivery_mode'] == 1 || $shop['delivery_mode'] == 0) {
                    //计算快递费
                    foreach ($shop['item'] as $item) {
                        $arr = array(
                            'weight' => floatval($item['weight']),
                            'total' => $item['total'],
                            'postage' => floatval($item['postage']),
                        );
                        if ($item['postage_tmplid'] > 0) {
                            $postage_tmpl[$shopid][$item['postage_tmplid']][] = $arr;
                        } else {
                            $postage_tmpl[$shopid][0][] = $arr;
                        }
                    }
                }
            }
            foreach ($postage_tmpl as $shopid => $tmpl) {
                $postage = 0;
                foreach ($tmpl as $tmplid => $items) {
                    //取模板
                    if ($tmplid > 0) {
                        $tmpl = M::t('superman_mall_postage_template')->fetch($tmplid);
                        if ($tmpl) {   //模板存在
                            $tmpl_item_param = 0;   //当前模版所有商品的某参数总和/数量/重量
                            $filter = array(
                                'templateid' => $tmplid,
                                'area' => '# LIKE "%'.$address['province'].'%"'
                            );
                            $tmpl_val = M::t('superman_mall_postage_template_value')->fetch($filter);
                            if (!$tmpl_val) {
                                $filter = array(
                                    'templateid' => $tmplid,
                                    'area' => '其他地区',
                                );
                                $tmpl_val = M::t('superman_mall_postage_template_value')->fetch($filter);   //模板值
                            }
                        }
                    }
                    //循环有用数据
                    foreach ($items as $item) {
                        if (isset($tmpl) && isset($tmpl_val) && $tmpl_val && isset($tmpl_item_param)) {
                            //邮费模板
                            if ($tmpl['valuation'] == 1) {
                                //按件
                                $tmpl_item_param += $item['total'];
                            } else if ($tmpl['valuation'] == 2) {
                                //按重量
                                $tmpl_item_param += $item['weight'] * $item['total'];
                            }
                        } else {
                            //统一邮费
                            $postage += $item['postage'] * $item['total'];
                        }
                    }
                    unset($item);
                    //邮费
                    if (isset($tmpl) && isset($tmpl_val) && $tmpl_val && isset($tmpl_item_param)) {
                        //参数转float型
                        $tmpl_val['start'] = floatval($tmpl_val['start']);
                        $tmpl_val['postage'] = floatval($tmpl_val['postage']);
                        $tmpl_val['step'] = floatval($tmpl_val['step']);
                        $tmpl_val['renew'] = floatval($tmpl_val['renew']);
                        //按件/按重量计费
                        if ($tmpl['valuation'] == 1 || $tmpl['valuation'] == 2) {
                            //首重/首件费用
                            $first = $tmpl_val['postage'];
                            $second = 0;
                            if ($tmpl_item_param - $tmpl_val['start'] > 0 && $tmpl_val['step'] > 0) {    //除去首件的件数
                                //续重/续件费用
                                $second = ceil(($tmpl_item_param - $tmpl_val['start'])/$tmpl_val['step']) * $tmpl_val['renew'];
                            }
                            $postage += $first + $second;
                        }
                    }
                }
                $this->shops[$shopid]['postage'] = SupermanUtil::float_format($postage);
            }
        }
    }

    //检查配送方式
    private function check_delivery_mode() {
        foreach ($this->shops as $shopid => $shop) {
            //初始化变量
            $this->shops[$shopid]['delivery_mode'] = $mode1 = $mode2 = 0;
            foreach ($shop['item'] as $item) {
                if ($item['delivery_mode'] == 0) {
                    $mode1 = $mode2 = 1;
                    break;
                } else if ($item['delivery_mode'] == 1) {
                    $mode1 = 1;
                } else if ($item['delivery_mode'] == 2) {
                    $mode2 = 1;
                }
            }
            if ($mode1 == 1 && $mode2 == 1) {
                $this->shops[$shopid]['delivery_mode'] = 0; //快递+自提
            } else if ($mode1 == 1) {
                $this->shops[$shopid]['delivery_mode'] = 1; //快递
            } else {
                $this->shops[$shopid]['delivery_mode'] = 2; //自提
                $filter = array(
                    'shopid' => $shopid,
                    'isshow' => 1,
                );
                $myfetch = M::t('superman_mall_myfetch')->fetch($filter);
                $this->shops[$shopid]['default_myfetch'] = $myfetch;
            }
        }
    }
    //检查是否需要身份证
    private function check_need_IDcard() {
        $need_IDcard = false;
        foreach ($this->shops as $shop) {
            foreach ($shop['item'] as $item) {
                if ($item['customs_clearance'] == 1) {
                    $need_IDcard = true;
                    break;
                }
            }
            if ($need_IDcard) {
                break;
            }
        }

        return $need_IDcard;
    }
    //检查是否需要收货地址
    private function check_need_address() {
        $need_address = false;
        $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_ORDER);
        foreach ($this->shops as $shop) {
            foreach ($shop['item'] as $item) {
                if ($item['type'] == 1) {       //实物商品
                    //如配送方式非自提
                    if ($shop['delivery_mode'] != 2) {
                        $need_address = true;
                        break;
                    }
                } else if ($item['type'] == 2) {    //虚拟商品
                    //如设置虚拟商品需地址
                    if (isset($setting['virtual_need_express']) && $setting['virtual_need_express']) {
                        $need_address = true;
                        break;
                    }
                }
            }
            if ($need_address) {
                break;
            }
        }
        return $need_address;
    }
}
$obj = new Superman_mall_doMobileConfirm;