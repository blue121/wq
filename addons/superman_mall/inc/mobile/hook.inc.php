<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileHook extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W;
        //拼团自动化处理
        if (isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) {
            if ($_W['member']['uid']) {
                //自动成团
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'mgid' => 0,
                    'pay_status' => 1,
                    'status' => 0,
                    'expiretime' => '#>'.TIMESTAMP, //未过期
                );
                $orderby = ' ORDER BY `expiretime` ASC'; //优先处理过期时间最小的拼团
                $list = M::t('superman_mall_merge_groupon')->fetchall($filter, $orderby, 0, 5);  //取5个团开始处理
                if ($list) {
                    //自动化处理状态,0:未处理,1:拼团成功,2:未开启自动成团,3:退款成功,4:订单状态异常退款失败
                    foreach ($list as $mgroup) {
                        //是否离结束时间还剩20%时间
                        if (TIMESTAMP + ($mgroup['expiretime'] - $mgroup['createtime'])*0.2 < ($mgroup['expiretime'])) {
                            continue;
                        }
                        //团员人数
                        $filter = array(
                            'mgid' => $mgroup['id'],
                            'pay_status' => 1
                        );
                        $mgroup_member = M::t('superman_mall_merge_groupon')->count($filter);
                        $remain_total = $mgroup['limit'] - intval($mgroup_member) - 1;   //拼团空位
                        $mgroup_status = 1;  //初始化拼团成功状态

                        if ($remain_total > 0) {    //有空位
                            //读取店铺拼团设置
                            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MGROUPON_SETTING, $mgroup['shopid']);
                            if (isset($setting['autogroup']) && $setting['autogroup']) {  //开启自动成团
                                for ($i = 1; $i <= $remain_total; $i++) {
                                    $data = array(
                                        'uniacid' => $mgroup['uniacid'],
                                        'shopid' => $mgroup['shopid'],
                                        'mgid' => $mgroup['id'],
                                        'uid' => 0,
                                        'orderid' => 0,
                                        'ordersn' => 0,
                                        'limit' => $mgroup['limit'],
                                        'pay_status' => 1,
                                        'status' => 0,
                                        'expiretime' => $mgroup['expiretime'],
                                        'createtime' => TIMESTAMP,
                                    );
                                    M::t('superman_mall_merge_groupon')->insert($data);
                                }
                            } else {
                                $mgroup_status = 2;
                            }
                        }
                        $this->update_mgstatus($mgroup['id'], $mgroup_status);
                    }
                }
            }
        }
    }

    private function update_mgstatus($mgid, $status) {
        if (in_array($status, array(1,2,3,4)) && $mgid > 0) {
            M::t('superman_mall_merge_groupon')->update(array('status' => $status), array('id' => $mgid));
            M::t('superman_mall_merge_groupon')->update(array('status' => $status), array('mgid' => $mgid));
        }
    }
}
$obj = new Superman_mall_doMobileHook;